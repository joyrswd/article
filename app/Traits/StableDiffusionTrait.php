<?php
declare(strict_types=1);

namespace App\Traits;
use App\Repositories\DeepLRepository;
use App\Repositories\StableDiffusionRepository;

trait StableDiffusionTrait {

    private StableDiffusionRepository $imageRepository;

    public function makeImage(string $article): string
    {
        if (app()->currentLocale() !== 'en') {
            $article = $this->translateToEnglish($article);
        }
        $this->imageRepository->setContent('Draw a illustration for the following article.');
        $this->imageRepository->setContent($article);
        return $this->imageRepository->getImage();
    }

    private function translateToEnglish(string $text): string
    {
        $deepL = app(DeepLRepository::class);
        $deepL->setLang('EN');
        $deepL->setContent($text);
        return $deepL->requestApi();
    }
 
    public function getImageModel() : string
    {
        return $this->imageRepository->getModel();
    }
    
}