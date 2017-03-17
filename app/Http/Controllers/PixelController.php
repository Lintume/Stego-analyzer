<?php

namespace App\Http\Controllers;

use App\Models\Pixel;
use Illuminate\Http\Request;

class PixelController extends Controller
{
    public function index()
    {
        $originalSrc = asset('images/original/originalCat.png');
        $crypto['LSB1000'] = asset('images/crypto/LSB1000.png');
        $crypto['LSB7000'] = asset('images/crypto/LSB7000.png');
        $crypto['LSB15000'] = asset('images/crypto/LSB15000.png');
        $IF[1000] = $this->calculateIf($originalSrc, $crypto['LSB1000']);
        $IF[7000] = $this->calculateIf($originalSrc, $crypto['LSB7000']);
        $IF[15000] = $this->calculateIf($originalSrc, $crypto['LSB15000']);

        return view('stegonography', compact('originalSrc', 'crypto', 'IF'));
    }

    public function calculateIf($originalSrc, $cryptoLSB1000)
    {
        $imageOriginal = imagecreatefrompng($originalSrc);
        $imageCrypto = imagecreatefrompng($cryptoLSB1000);

        $x_dimension = imagesx($imageOriginal); //height
        $y_dimension = imagesy($imageOriginal); //width

        $sum1 = 0;
        $sum2 = 0;

        for ($x = 0; $x < $x_dimension; $x++) {
            for ($y = 0; $y < $y_dimension; $y++) {

                $rgbOriginal = imagecolorat($imageOriginal, $x, $y);//get index color
//                $r = ($rgbOriginal >> 16) & 0xFF;
//                $g = ($rgbOriginal >> 8) & 0xFF;
//                $b = $rgbOriginal & 0xFF;
//
//                $pixelOriginal = new Pixel($r, $g, $b);

                $rgbCrypto = imagecolorat($imageCrypto, $x, $y);//get index color
                $r = ($rgbCrypto >> 16) & 0xFF;
                $g = ($rgbCrypto >> 8) & 0xFF;
                $b = $rgbCrypto & 0xFF;

                $pixelCrypto = new Pixel($r, $g, $b);

                $sum1 += ($rgbOriginal - $rgbCrypto) * ($rgbOriginal - $rgbCrypto);
                $sum2 += ($rgbOriginal) * ($rgbOriginal);
            }
        }

        return 1 - $sum1 / $sum2;
    }
}
