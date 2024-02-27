<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\ImageRepository;
use Illuminate\Support\Facades\File;
use finfo;

class ImageService
{

    private ImageRepository $repository;
    private File $file;
    private finfo $finfo;
    private array $dirs = ['img', 'posts'];

    public function __construct(ImageRepository $repository, File $file, finfo $finfo)
    {
        $this->repository = $repository;
        $this->file = $file;
        $this->finfo = new $finfo(FILEINFO_EXTENSION);
        $this->dirs += [date('Y'), date('m'), date('g')];
    }

    public function put(string $url):string
    {
        $image = $this->file::get($url);
        if (empty($image)) {
            new \Exception('ファイル取得失敗');
        }
        $extension = $this->finfo->buffer($image);
        $dir = $this->setUpDirectory();
        $path = $dir . md5($url) . '.' . $extension;
        if($this->file::put($path, $image) ) {
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
