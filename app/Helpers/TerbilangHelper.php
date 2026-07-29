<?php

if (!function_exists('terbilang')) {
    function terbilang($number) {
        $number = abs(intval($number));
        $satuan = ['', 'Satu', 'Dua', 'Tiga', 'Empat', 'Lima', 'Enam', 'Tujuh', 'Delapan', 'Sembilan',
                   'Sepuluh', 'Sebelas', 'Dua Belas', 'Tiga Belas', 'Empat Belas', 'Lima Belas',
                   'Enam Belas', 'Tujuh Belas', 'Delapan Belas', 'Sembilan Belas'];
        
        if ($number < 20) {
            return $satuan[$number];
        }
        
        if ($number < 100) {
            $tens = intdiv($number, 10);
            $rem  = $number % 10;
            $words = ['', 'Sepuluh', 'Dua Puluh', 'Tiga Puluh', 'Empat Puluh', 'Lima Puluh',
                      'Enam Puluh', 'Tujuh Puluh', 'Delapan Puluh', 'Sembilan Puluh'];
            return trim($words[$tens] . ' ' . ($rem > 0 ? $satuan[$rem] : ''));
        }
        
        if ($number < 200) {
            $rem = $number % 100;
            return 'Seratus' . ($rem > 0 ? ' ' . terbilang($rem) : '');
        }
        
        if ($number < 1000) {
            $hundreds = intdiv($number, 100);
            $rem = $number % 100;
            return $satuan[$hundreds] . ' Ratus' . ($rem > 0 ? ' ' . terbilang($rem) : '');
        }
        
        if ($number < 2000) {
            $rem = $number % 1000;
            return 'Seribu' . ($rem > 0 ? ' ' . terbilang($rem) : '');
        }
        
        if ($number < 1000000) {
            $thousands = intdiv($number, 1000);
            $rem = $number % 1000;
            return terbilang($thousands) . ' Ribu' . ($rem > 0 ? ' ' . terbilang($rem) : '');
        }
        
        if ($number < 1000000000) {
            $millions = intdiv($number, 1000000);
            $rem = $number % 1000000;
            return terbilang($millions) . ' Juta' . ($rem > 0 ? ' ' . terbilang($rem) : '');
        }
        
        if ($number < 1000000000000) {
            $billions = intdiv($number, 1000000000);
            $rem = $number % 1000000000;
            return terbilang($billions) . ' Miliar' . ($rem > 0 ? ' ' . terbilang($rem) : '');
        }
        
        return 'Angka terlalu besar';
    }
}
