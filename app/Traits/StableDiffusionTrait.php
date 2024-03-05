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
        $this->imageRepository->addPrompt('Draw a illustration for the following article.');
        $this->imageRepository->addPrompt($article);
        return $this->imageRepository->getImage();
    }

    private function translateToEnglish(string $text): string
    {
        $deepL = app(DeepLRepository::class);
        $deepL->setLang('en');
        $deepL->addPrompt($text);
        return $deepL->requestApi();
    }
 
    public function getImageModel() : string
    {
        return $this->imageRepository->getModel();
    }
    
}