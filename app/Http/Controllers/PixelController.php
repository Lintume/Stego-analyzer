<?php

namespace App\Http\Controllers;

use App\Models\Pixel;
use Illuminate\Http\Request;

class PixelController extends Controller
{
    public function index()
    {
        $originalSrc = asset('images/original/originalCat.png');
        $crypto['LSB']['images']['1000'] = asset('images/crypto/LSB1000.png');
        $crypto['LSB']['images']['7000'] = asset('images/crypto/LSB7000.png');
        $crypto['LSB']['images']['15000'] = asset('images/crypto/LSB15000.png');
        $crypto['LSB']['images']['94000'] = asset('images/crypto/LSB94000.png');

        $crypto['LSB']['IF'][1000] = $this->calculateIf($originalSrc, $crypto['LSB']['images']['1000']);
        $crypto['LSB']['IF'][7000] = $this->calculateIf($originalSrc, $crypto['LSB']['images']['7000']);
        $crypto['LSB']['IF'][15000] = $this->calculateIf($originalSrc, $crypto['LSB']['images']['15000']);
        $crypto['LSB']['IF'][94000] = $this->calculateIf($originalSrc, $crypto['LSB']['images']['94000']);

        $crypto['LSB']['SNR'][1000] = $this->calculateSnr($originalSrc, $crypto['LSB']['images']['1000']);
        $crypto['LSB']['SNR'][7000] = $this->calculateSnr($originalSrc, $crypto['LSB']['images']['7000']);
        $crypto['LSB']['SNR'][15000] = $this->calculateSnr($originalSrc, $crypto['LSB']['images']['15000']);
        $crypto['LSB']['SNR'][94000] = $this->calculateSnr($originalSrc, $crypto['LSB']['images']['94000']);

        $crypto['LSB']['CQ'][1000] = $this->calculateCq($originalSrc, $crypto['LSB']['images']['1000']);
        $crypto['LSB']['CQ'][7000] = $this->calculateCq($originalSrc, $crypto['LSB']['images']['7000']);
        $crypto['LSB']['CQ'][15000] = $this->calculateCq($originalSrc, $crypto['LSB']['images']['15000']);
        $crypto['LSB']['CQ'][94000] = $this->calculateCq($originalSrc, $crypto['LSB']['images']['94000']);

        $crypto['LSB']['AD'][1000] = $this->calculateAd($originalSrc, $crypto['LSB']['images']['1000']);
        $crypto['LSB']['AD'][7000] = $this->calculateAd($originalSrc, $crypto['LSB']['images']['7000']);
        $crypto['LSB']['AD'][15000] = $this->calculateAd($originalSrc, $crypto['LSB']['images']['15000']);
        $crypto['LSB']['AD'][94000] = $this->calculateAd($originalSrc, $crypto['LSB']['images']['94000']);

        $crypto['LSB']['NAD'][1000] = $this->calculateNad($originalSrc, $crypto['LSB']['images']['1000']);
        $crypto['LSB']['NAD'][7000] = $this->calculateNad($originalSrc, $crypto['LSB']['images']['7000']);
        $crypto['LSB']['NAD'][15000] = $this->calculateNad($originalSrc, $crypto['LSB']['images']['15000']);
        $crypto['LSB']['NAD'][94000] = $this->calculateNad($originalSrc, $crypto['LSB']['images']['94000']);

        return view('stegonography', compact('originalSrc', 'crypto'));
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

    public function calculateSnr($original, $crypto)
    {
        $imageOriginal = imagecreatefrompng($original);
        $imageCrypto = imagecreatefrompng($crypto);

        $x_dimension = imagesx($imageOriginal); //height
        $y_dimension = imagesy($imageOriginal); //width

        $sum1 = 0;
        $sum2 = 0;

        for ($x = 0; $x < $x_dimension; $x++) {
            for ($y = 0; $y < $y_dimension; $y++) {

                $rgbOriginal = imagecolorat($imageOriginal, $x, $y);//get index color

                $rgbCrypto = imagecolorat($imageCrypto, $x, $y);//get index color

                $sum1 += ($rgbOriginal) * ($rgbOriginal);
                $sum2 += ($rgbOriginal - $rgbCrypto) * ($rgbOriginal - $rgbCrypto);
            }
        }

        return $sum1 / $sum2;
    }

    public function calculateCq($original, $crypto)
    {
        $imageOriginal = imagecreatefrompng($original);
        $imageCrypto = imagecreatefrompng($crypto);

        $x_dimension = imagesx($imageOriginal); //height
        $y_dimension = imagesy($imageOriginal); //width

        $sum1 = 0;
        $sum2 = 0;

        for ($x = 0; $x < $x_dimension; $x++) {
            for ($y = 0; $y < $y_dimension; $y++) {

                $rgbOriginal = imagecolorat($imageOriginal, $x, $y);//get index color

                $rgbCrypto = imagecolorat($imageCrypto, $x, $y);//get index color

                $sum1 += ($rgbOriginal * $rgbCrypto);
                $sum2 += $rgbOriginal;
            }
        }

        return $sum1 / $sum2;
    }

    public function calculateAd($original, $crypto)
    {
        $imageOriginal = imagecreatefrompng($original);
        $imageCrypto = imagecreatefrompng($crypto);

        $x_dimension = imagesx($imageOriginal); //height
        $y_dimension = imagesy($imageOriginal); //width

        $sum1 = 0;

        for ($x = 0; $x < $x_dimension; $x++) {
            for ($y = 0; $y < $y_dimension; $y++) {

                $rgbOriginal = imagecolorat($imageOriginal, $x, $y);//get index color

                $rgbCrypto = imagecolorat($imageCrypto, $x, $y);//get index color

                $sum1 += abs($rgbOriginal - $rgbCrypto);
            }
        }

        return $sum1 * (1/ ($x_dimension * $y_dimension));
    }

    public function calculateNad($original, $crypto)
    {
        $imageOriginal = imagecreatefrompng($original);
        $imageCrypto = imagecreatefrompng($crypto);

        $x_dimension = imagesx($imageOriginal); //height
        $y_dimension = imagesy($imageOriginal); //width

        $sum1 = 0;
        $sum2 = 0;

        for ($x = 0; $x < $x_dimension; $x++) {
            for ($y = 0; $y < $y_dimension; $y++) {

                $rgbOriginal = imagecolorat($imageOriginal, $x, $y);//get index color

                $rgbCrypto = imagecolorat($imageCrypto, $x, $y);//get index color

                $sum1 += abs($rgbOriginal - $rgbCrypto);
                $sum2 += abs($rgbOriginal);
            }
        }

        return $sum1 / $sum2;
    }
}
