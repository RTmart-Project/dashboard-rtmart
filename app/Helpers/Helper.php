<?php

namespace App\Helpers;

class Helper
{
    public static function formatCurrency($value, $beforeValue = "Rp. ", $afterValue = "")
    {
        $output = $beforeValue . number_format($value, 0, ',', '.') . $afterValue;
        return $output;
    }

    public static function arrayFilterFirst($array, $objectFilter, $valueFilter)
    {
        $output = array_filter($array, function($item) use ($objectFilter, $valueFilter) {
            return $item->$objectFilter == $valueFilter;
        });
        return array_values($output)[0];
    }
}