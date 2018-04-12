<?php

if (!function_exists('limitCharacters')) {
    /**
     * @param $value
     * @param int $decimal
     * @return float
     */
    function limitCharacters($value, $length = 0)
    {
        return substr($value, 0, $length);
    }
}