<?php

declare(strict_types=1);

namespace App\Interfaces;

interface AiImageRepositoryInterface
{
    public function getImage() : string;
}