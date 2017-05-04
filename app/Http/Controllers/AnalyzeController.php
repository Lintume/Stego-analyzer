<?php

namespace App\Http\Controllers;

use App\Http\Requests\CryptoRequest;

class AnalyzeController extends Controller
{
    public function index()
    {
        return view('stegonography');
    }

    public function analyze(CryptoRequest $request)
    {
        if($request->has('pictures.original') && $request->has('pictures.containers'))
        {
            $pictures = $request->get('pictures');
            $original = preg_replace('/data:image\/\w+;base64,/', '', $pictures['original']);
            $original = base64_decode($original);
            $crypto = [];
            foreach ($pictures['containers'] as $picture)
            {
                $data = preg_replace('/data:image\/\w+;base64,/', '', $picture['base64Picture']);
                if($data == null || $data == '')
                    return response(404);

                $data = base64_decode($data);

                $imageOriginal = imagecreatefromstring($original);
                $imageCrypto = imagecreatefromstring($data);

                $x_dimension = imagesx($imageOriginal); //height
                $y_dimension = imagesy($imageOriginal); //width

                if($this->equals($imageOriginal, $imageCrypto, $x_dimension, $y_dimension) == 0)
                    return response()->json(['errors' => ['Pictures are identical']], 404);

                $crypto['IF']['coefficients'][$picture['bytes']] =  $this->ifAnalyze($imageOriginal, $imageCrypto, $x_dimension, $y_dimension);
                $crypto['SNR']['coefficients'][$picture['bytes']] = $this->snrAnalyze($imageOriginal, $imageCrypto, $x_dimension, $y_dimension);
                $crypto['NC']['coefficients'][$picture['bytes']] = $this->ncAnalyze($imageOriginal, $imageCrypto, $x_dimension, $y_dimension);
                $crypto['NAD']['coefficients'][$picture['bytes']] = $this->nadAnalyze($imageOriginal, $imageCrypto, $x_dimension, $y_dimension);
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

    public function ifAnalyze($imageOriginal, $imageCrypto, $x_dimension, $y_dimension)
    {
        $sum1 = 0;
        $sum2 = 0;

        for ($x = 0; $x < $x_dimension; $x++) {
            for ($y = 0; $y < $y_dimension; $y++) {

                $rgbOriginal = imagecolorat($imageOriginal, $x, $y);//get index color

                $rgbCrypto = imagecolorat($imageCrypto, $x, $y);//get index color
//                $r = ($rgbCrypto >> 16) & 0xFF;
//                $g = ($rgbCrypto >> 8) & 0xFF;
//                $b = $rgbCrypto & 0xFF;
//                $pixelCrypto = new Pixel($r, $g, $b);

                $sum1 += ($rgbOriginal - $rgbCrypto) * ($rgbOriginal - $rgbCrypto);
                $sum2 += ($rgbOriginal) * ($rgbOriginal);
            }
        }

        return 1 - $sum1 / $sum2;
    }

    public function snrAnalyze($imageOriginal, $imageCrypto, $x_dimension, $y_dimension)
    {
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

        return 10 * log10($sum1 / $sum2);
    }

    public function equals($imageOriginal, $imageCrypto, $x_dimension, $y_dimension)
    {
        $sum2 = 0;

        for ($x = 0; $x < $x_dimension; $x++) {
            for ($y = 0; $y < $y_dimension; $y++) {

                $rgbOriginal = imagecolorat($imageOriginal, $x, $y);//get index color

                $rgbCrypto = imagecolorat($imageCrypto, $x, $y);//get index color

                $sum2 += ($rgbOriginal - $rgbCrypto) * ($rgbOriginal - $rgbCrypto);
            }
        }

        return $sum2;
    }

    public function ncAnalyze($imageOriginal, $imageCrypto, $x_dimension, $y_dimension)
    {
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

    public function nadAnalyze($imageOriginal, $imageCrypto, $x_dimension, $y_dimension)
    {
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
