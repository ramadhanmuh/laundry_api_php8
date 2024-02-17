<?php

namespace Src\Helpers;

class StringHelper
{
    static function createCode() {
        $characters = '2346789ABCDEFGHJKLMNPQRTUVWXYZabcdefghjkmnpqrsuvwxyz';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < 6; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    static function createSlug($text) {
        // Menghapus karakter khusus
        $text = preg_replace('/[^a-zA-Z0-9]+/', '-', $text);
        
        // Menghilangkan tanda strip di awal dan akhir teks
        $text = trim($text, '-');
        
        // Mengonversi huruf menjadi huruf kecil semua
        $text = strtolower($text);
        
        return $text;
    }

    function createAdditionalSlug() {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyz';
        $charactersLength = strlen($characters);
        $randomString = '-';

        for ($i = 0; $i < 5; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }

        return $randomString;
    }
}
