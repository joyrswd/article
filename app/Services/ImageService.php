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

    public function put(string $binary, string $watermark): string
    {
        $path = $this->prepareImagePath($binary);
        $watermarkId = $this->setUpWatermark($watermark);
        $id = $this->imagickRepository->setBinaryImage($binary);
        $this->imagickRepository->minimize($id, 512, 0);
        $this->imagickRepository->compositeOver($id, $watermarkId);
        $this->imagickRepository->save($id, $path);
        $this->imagickRepository->clear();
        return $path;
    }

    private function setUpWatermark(string $text): int
    {
        $config = config('llm.watermark');
        $watermarkId = $this->imagickRepository->setRectImage($config['width'], $config['height'], $config['background'], 'png');
        $this->imagickRepository->setTextOnImage($watermarkId, $text, [
            'font' => $config['font'],
            'fontSize' => $config['size'],
            'fillColor' => $config['color'],
            'gravity' => Imagick::GRAVITY_CENTER,   
        ]);
        return $watermarkId;
    }

    private function setUpDirectory(): string
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

    private function prepareImagePath(string $content): string
    {
        $dir = $this->setUpDirectory();
        $fiename = md5($content) . '.png';
        return $dir . $fiename;
    }

    public function add(int $articleId, string $path, string $modelName): array
    {
        $id = $this->repository->create([
            'article_id' => $articleId,
            'path' => $path,
            'model_name' => $modelName,
        ]);
        return $this->repository->read($id);
    }

    public function findByPage(int $page, array $param, ?array $options = []) :array
    {
        $locale = app()->currentLocale();
        $options['orderBy'] = ['created_at', 'desc'];
        $options['whereHas'] = ['article', function($query) use ($locale){
            $query->where('locale', $locale);
        }];
        $pages = $this->repository->findByPage($page, $param, $options);
        return $pages->toArray();
    }
}
