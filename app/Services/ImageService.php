<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\ImageRepository;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use finfo;

class ImageService
{

    private ImageRepository $repository;
    private finfo $finfo;
    private array $dirs = ['img', 'posts'];

    public function __construct(ImageRepository $repository, finfo $finfo)
    {
        $this->repository = $repository;
        $this->finfo = new $finfo(FILEINFO_EXTENSION);
        $this->dirs += [date('Y'), date('m'), date('g')];
    }

    public function put(string $url):string
    {
        $response = Http::withoutVerifying()->get($url);
        if ($response->successful() === false) {
            new \Exception('ファイル取得失敗');
        }
        $image = $response->body();
        $extension = $this->finfo->buffer($image);
        $dir = $this->setUpDirectory();
        $path = $dir . md5($url) . '.' . $extension;
        if(File::put($path, $image) ) {
            return $path;
        } else {
            new \Exception('ファイル保存失敗');
        }
    }

    private function setUpDirectory() 
    {
        $dir = public_path() . '/';
        foreach($this->dirs as $name) {
            $dir .= $name . '/';
            if (is_dir($dir) === false) {
                mkdir($dir);
            }
        }
        return $dir;
    }

    public function add(int $articleId, string $path, string $description, string $size, string $modelName) : array
    {
        $id = $this->repository->create([
            'article_id' => $articleId,
            'path' => $path,
            'description' => $description,
            'size' => $size,
            'model_name' => $modelName,
        ]);
        return $this->repository->read($id);
    }

}
