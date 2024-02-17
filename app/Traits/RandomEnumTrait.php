<?php
declare(strict_types=1);

namespace App\Traits;

trait RandomEnumTrait {

    static function randomValue() : string
    {
        $cases = self::cases();
        $index = array_rand($cases);
        return $cases[$index]->value;
    }

}