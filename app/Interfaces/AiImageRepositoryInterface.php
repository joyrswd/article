<?php

declare(strict_types=1);

namespace App\Interfaces;

interface AiImageRepositoryInterface
{
    public function makeImage(array $messages) : array;
    public function getBinary(array $response) : string;
    public function getModel() : string;
}