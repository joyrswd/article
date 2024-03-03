<?php

declare(strict_types=1);

namespace App\Repositories;

use Exception;
use Imagick;
use ImagickDraw;

class ImagickRepository
{

    private array $instances = [];

    private Imagick $imagick;
    private ImagickDraw $imagickDraw;

    public function __construct(Imagick $imagick, ImagickDraw $imagickDraw)
    {
        $this->imagick = $imagick;
        $this->imagickDraw = $imagickDraw;
    }

    private function setImager() : int
    {
        $this->instances[] = clone $this->imagick;
        return (count($this->instances) - 1);
    }

    private function setDrawer(?array $params = []) : int
    {
        $drawer = clone $this->imagickDraw;
        foreach ($params as $key => $value) {
            $method = 'set' . ucfirst($key);
            if (method_exists($drawer, $method)) {
                $drawer->$method($value);
            }
        }
        $this->instances[] = $drawer;
        return (count($this->instances) - 1);
    }

    private function getInstance(int $id) : Imagick|ImagickDraw
    {
        if ($this->instances[$id] instanceof Imagick === false
            && $this->instances[$id] instanceof ImagickDraw === false
            ) {
            throw new Exception('無効なインスタンスIDです。');
        }
        return $this->instances[$id];
    }

    public function setBinaryImage(string $binary): int
    {
        $id = $this->setImager();
        $imager = $this->instances[$id];
        $imager->readImageBlob($binary);
        return $id;
    }

    public function setRectImage(int $width, int $height, string $color, string $format) : int
    {
        $id = $this->setImager();
        $image = $this->instances[$id];
        $image->newImage($width, $height, $color, $format);
        return $id;
    }

    public function setTextOnImage(int $id, string $text, array $params):void
    {
        $imager = $this->getInstance($id);
        $drawerId = $this->setDrawer($params);
        $drawer = $this->getInstance($drawerId);
        $imager->annotateImage($drawer, 0, 0, 0, $text);
    }

    public function minimize(int $id, int $width, int $height) :void
    {
        $imager = $this->getInstance($id);
        $imager->thumbnailImage($width, $height);
    }

    public function compositeOver(int $canvasId, int $contentId) :void
    {
        $canvas = $this->getInstance($canvasId);
        $content = $this->getInstance($contentId);
        $x = $canvas->getImageWidth() - $content->getImageWidth();
        $y = $canvas->getImageHeight() - $content->getImageHeight();
        $canvas->compositeImage($content, Imagick::COMPOSITE_OVER, $x, $y);
    }

    public function save($id, $path):void
    {
        $imager = $this->getInstance($id);
        $format = pathinfo($path, PATHINFO_EXTENSION);
        $imager->setFormat($format);
        $imager->writeImage($path);
    }

    public function clear (?int $id=null):void
    {
        if(is_null($id)) {
            array_map([$this, __METHOD__], array_keys($this->instances));
        } else {
            $instance = $this->getInstance($id);
            $instance->clear();
            $this->instances[$id] = null;
        }
    }

}
