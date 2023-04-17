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
        $output = array_filter($array, function ($item) use ($objectFilter, $valueFilter) {
            return $item->$objectFilter == $valueFilter;
        });

        return array_values($output)[0];
    }

    public static function convertToWords($number)
    {
        function penyebut($nilai)
        {
            $nilai = abs($nilai);
            $huruf = array("", "satu", "dua", "tiga", "empat", "lima", "enam", "tujuh", "delapan", "sembilan", "sepuluh", "sebelas");
            $temp = "";

            if ($nilai < 12) {
                $temp = " " . $huruf[$nilai];
            } else if ($nilai < 20) {
                $temp = penyebut($nilai - 10) . " belas";
            } else if ($nilai < 100) {
                $temp = penyebut($nilai / 10) . " puluh" . penyebut($nilai % 10);
            } else if ($nilai < 200) {
                $temp = " seratus" . penyebut($nilai - 100);
            } else if ($nilai < 1000) {
                $temp = penyebut($nilai / 100) . " ratus" . penyebut($nilai % 100);
            } else if ($nilai < 2000) {
                $temp = " seribu" . penyebut($nilai - 1000);
            } else if ($nilai < 1000000) {
                $temp = penyebut($nilai / 1000) . "ribu" . penyebut($nilai % 1000);
            } else if ($nilai < 1000000000) {
                $temp = penyebut($nilai / 1000000) . " juta" . penyebut($nilai % 1000000);
            } else if ($nilai < 1000000000000) {
                $temp = penyebut($nilai / 1000000000) . " milyar" . penyebut(fmod($nilai, 1000000000));
            } else if ($nilai < 1000000000000000) {
                $temp = penyebut($nilai / 1000000000000) . " trilyun" . penyebut(fmod($nilai, 1000000000000));
            }

            return $temp;
        }

        $hasil = trim(penyebut($number)) . ' rupiah';
        $result = ucwords($hasil);

        return $result;
    }

    public static function convertMonthToRomanNumerals($monthNumber)
    {
        $romanNumerals = array(
            'I' => 1,
            'II' => 2,
            'III' => 3,
            'IV' => 4,
            'V' => 5,
            'VI' => 6,
            'VII' => 7,
            'VIII' => 8,
            'IX' => 9,
            'X' => 10,
            'XI' => 11,
            'XII' => 12,
        );

        return array_search($monthNumber, $romanNumerals);
    }
}
