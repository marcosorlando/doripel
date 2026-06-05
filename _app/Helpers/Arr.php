<?php

namespace App\Helpers;

use function implode;
use function is_array;

final class Arr
{
    /**
     * @param array<int|string, string>|string $pieces
     */
    public static function join(string $glue, array|string $pieces): string
    {

        $arr = is_array($pieces) ? $pieces : (array)$pieces;

        return implode($glue, $arr);
    }
}
