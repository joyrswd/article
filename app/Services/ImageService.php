<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\ImageRepository;
use App\Repositories\ImagickRepository;
use Imagick;

class ImageService
{

    private ImageRepository $repository;
    private ImagickRepository $imagickRepository;
    private array $dirs = ['img', 'posts'];

    public function __construct(ImageRepository $repository, ImagickRepository $imagickRepository)
    {
        $this->repository = $repository;
        $this->imagickRepository = $imagickRepository;
        $this->dirs = array_merge($this->dirs, [date('Y'), date('m'), date('d')]);
    }

    public function put(string $url, string $watermark): string
    {
        $config = config('llm.watermark');
        $dir = $this->setUpDirectory();
        $path = $dir . md5($url) . '.png';
        $watermarkId = $this->imagickRepository->setRectImage($config['width'], $config['height'], $config['background'], 'png');
        $this->imagickRepository->setTextOnImage($watermarkId, $watermark, [
            'font' => $config['font'],
            'fillColor' => $config['color'],
            'gravity' => Imagick::GRAVITY_CENTER,   
        ]);
        $urlImageId = $this->imagickRepository->setImageByUrl($url);
        $this->imagickRepository->minimize($urlImageId, 512, 0);
        $this->imagickRepository->compositeOver($urlImageId, $watermarkId);
        $this->imagickRepository->save($urlImageId, $path);
        $this->imagickRepository->clear();
        return $path;
    }

    private function setUpDirectory()
    {
        $dir = public_path() . '/';
        foreach ($this->dirs as $name) {
            $dir .= $name . '/';
            if (is_dir($dir) === false) {
                mkdir($dir);
            }
        }
        return $dir;
    }

    public function add(int $articleId, string $path, string $description, string $size, string $modelName): array
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
