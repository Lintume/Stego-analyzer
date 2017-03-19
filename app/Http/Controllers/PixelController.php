<?php

namespace App\Http\Controllers;

use App\Models\Pixel;
use Illuminate\Http\Request;

class PixelController extends Controller
{
    public function index()
    {
//        $originalSrc = asset('images/original/originalCat.png');
//        $crypto['LSB']['images']['1000'] = asset('images/crypto/LSB1000.png');
//        $crypto['LSB']['images']['7000'] = asset('images/crypto/LSB7000.png');
//        $crypto['LSB']['images']['15000'] = asset('images/crypto/LSB15000.png');
//        $crypto['LSB']['images']['94000'] = asset('images/crypto/LSB94000.png');
//
//        $crypto['LSB']['IF'][1000] = $this->calculateIf($originalSrc, $crypto['LSB']['images']['1000']);
//        $crypto['LSB']['IF'][7000] = $this->calculateIf($originalSrc, $crypto['LSB']['images']['7000']);
//        $crypto['LSB']['IF'][15000] = $this->calculateIf($originalSrc, $crypto['LSB']['images']['15000']);
//        $crypto['LSB']['IF'][94000] = $this->calculateIf($originalSrc, $crypto['LSB']['images']['94000']);
//
//        $crypto['LSB']['SNR'][1000] = $this->calculateSnr($originalSrc, $crypto['LSB']['images']['1000']);
//        $crypto['LSB']['SNR'][7000] = $this->calculateSnr($originalSrc, $crypto['LSB']['images']['7000']);
//        $crypto['LSB']['SNR'][15000] = $this->calculateSnr($originalSrc, $crypto['LSB']['images']['15000']);
//        $crypto['LSB']['SNR'][94000] = $this->calculateSnr($originalSrc, $crypto['LSB']['images']['94000']);
//
//        $crypto['LSB']['NC'][1000] = $this->calculateNc($originalSrc, $crypto['LSB']['images']['1000']);
//        $crypto['LSB']['NC'][7000] = $this->calculateNc($originalSrc, $crypto['LSB']['images']['7000']);
//        $crypto['LSB']['NC'][15000] = $this->calculateNc($originalSrc, $crypto['LSB']['images']['15000']);
//        $crypto['LSB']['NC'][94000] = $this->calculateNc($originalSrc, $crypto['LSB']['images']['94000']);
//
//        $crypto['LSB']['NAD'][1000] = $this->calculateNad($originalSrc, $crypto['LSB']['images']['1000']);
//        $crypto['LSB']['NAD'][7000] = $this->calculateNad($originalSrc, $crypto['LSB']['images']['7000']);
//        $crypto['LSB']['NAD'][15000] = $this->calculateNad($originalSrc, $crypto['LSB']['images']['15000']);
//        $crypto['LSB']['NAD'][94000] = $this->calculateNad($originalSrc, $crypto['LSB']['images']['94000']);

        return view('stegonography'
//            compact('originalSrc', 'crypto')
        );
    }

    public function analyze(Request $request)
    {
        if($request->has('pictures.original') && $request->has('pictures.containers'))
        {
            $pictures = $request->get('pictures');
            $original = preg_replace('/data:image\/.+;base64,/', '', $pictures['original']);
            $original = base64_decode($original);
            $crypto = [];
           foreach ($pictures['containers'] as $picture)
           {
               $data = preg_replace('/data:image\/.+;base64,/', '', $picture['base64Picture']);
               $data = base64_decode($data);
               $crypto['IF']['coefficients'][$picture['bytes']] =  $this->ifAnalyze($original, $data);
               $crypto['SNR']['coefficients'][$picture['bytes']] = $this->snrAnalyze($original, $data);
               $crypto['NC']['coefficients'][$picture['bytes']] = $this->ncAnalyze($original, $data);
               $crypto['NAD']['coefficients'][$picture['bytes']] = $this->nadAnalyze($original, $data);
           }

            foreach ($crypto as &$alg)
            {
                $alg['max'] = $this->max($alg);
                $alg['min'] = $this->min($alg);
            }

            return response()->json(compact('crypto'));
        }
        return null;
    }

    public function max($array)
    {
        $max = reset($array['coefficients']);
        foreach ($array['coefficients'] as $item) {
            if ($item > $max)
            {
                $max = $item;
            }
        }
        return $max;
    }

    public function min($array)
    {
        $min = reset($array['coefficients']);
        foreach ($array['coefficients'] as $item) {
            if ($item < $min)
            {
                $min = $item;
            }
        }
        return $min;
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

    public function calculateNc($original, $crypto)
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

                $sum1 += $rgbOriginal * $rgbCrypto;
                $sum2 += $rgbOriginal * $rgbOriginal;
            }
        }

        return $sum1 / $sum2;
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

    public function ifAnalyze($originalSrc, $cryptoLSB1000)
    {
        $imageOriginal = imagecreatefromstring($originalSrc);
        $imageCrypto = imagecreatefromstring($cryptoLSB1000);

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

    public function snrAnalyze($original, $crypto)
    {
        $imageOriginal = imagecreatefromstring($original);
        $imageCrypto = imagecreatefromstring($crypto);

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

    public function ncAnalyze($original, $crypto)
    {
        $imageOriginal = imagecreatefromstring($original);
        $imageCrypto = imagecreatefromstring($crypto);

        $x_dimension = imagesx($imageOriginal); //height
        $y_dimension = imagesy($imageOriginal); //width

        $sum1 = 0;
        $sum2 = 0;

        for ($x = 0; $x < $x_dimension; $x++) {
            for ($y = 0; $y < $y_dimension; $y++) {

                $rgbOriginal = imagecolorat($imageOriginal, $x, $y);//get index color

                $rgbCrypto = imagecolorat($imageCrypto, $x, $y);//get index color

                $sum1 += $rgbOriginal * $rgbCrypto;
                $sum2 += $rgbOriginal * $rgbOriginal;
            }
        }

        return $sum1 / $sum2;
    }

    public function nadAnalyze($original, $crypto)
    {
        $imageOriginal = imagecreatefromstring($original);
        $imageCrypto = imagecreatefromstring($crypto);

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
