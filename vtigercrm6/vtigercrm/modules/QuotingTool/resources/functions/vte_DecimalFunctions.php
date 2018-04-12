<?php

if (!function_exists('limitDecimalOfNumber')) {
    /**
     * @param $value
     * @param int $decimal
     * @return float
     */
    function limitDecimalOfNumber($value, $decimal = 0)
    {
        global $current_user;
        $userModel = Users_Record_Model::getCurrentUserModel();
        $currency_grouping_separator = $current_user->currency_grouping_separator;
        $currency_decimal_separator = $current_user->currency_decimal_separator;
        $no_of_decimals = $current_user->no_of_currency_decimals;
        $newValue = floatval(str_replace($currency_decimal_separator, '.', str_replace($currency_grouping_separator, '', $value)));
        if (!is_numeric($newValue)) {
            // invalid number
            return 0;
        }
        // round value by decimal
        return number_format($newValue, $decimal,$currency_decimal_separator,  $currency_grouping_separator);
    }
}