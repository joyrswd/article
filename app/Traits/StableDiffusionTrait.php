<?php
declare(strict_types=1);

namespace App\Traits;
use App\Repositories\DeepLRepository;
use App\Interfaces\AiImageRepositoryInterface;

trait StableDiffusionTrait {

    private AiImageRepositoryInterface $imageRepository;

    public function makeImage(string $article): string
    {
        if (app()->currentLocale() !== 'en') {
            $article = $this->translateToEnglish($article);
        }
        $prompt = ['Draw a illustration for the following article.', $article];
        $response = $this->imageRepository->makeImage($prompt);
        if (empty($response)) {
            return [];
        }
        return $this->imageRepository->getBinary($response);
    }

    private function translateToEnglish(string $text)
    {
        $deepL = app(DeepLRepository::class);
        $response = $deepL->requestApi($text, 'EN');
        return $deepL->getTranslation($response);
    }
 
    public function getImageModel() : string
    {
        return $this->imageRepository->getModel();
    }
    
}