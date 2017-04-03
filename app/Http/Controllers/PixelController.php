<?php

namespace App\Http\Controllers;

use App\Http\Requests\CryptoRequest;
use App\Models\Pixel;
use Illuminate\Http\Request;

class PixelController extends Controller
{
    public function index()
    {
        return view('stegonography'
        );
    }

    public function LSB()
    {
        return view('lsb'
        );
    }

    public function analyze(CryptoRequest $request)
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

    public function ifAnalyze($original, $container)
    {
        $imageOriginal = imagecreatefromstring($original);
        $imageCrypto = imagecreatefromstring($container);

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

    public function LSBAnalyze(Request $request)
    {
        $pictures = $request->get('pictures');
        $original = preg_replace('/data:image\/.+;base64,/', '', $pictures['original']);
        $original = base64_decode($original);

        $imageOriginal = imagecreatefromstring($original);

        $x_dimension = imagesx($imageOriginal); //height
        $y_dimension = imagesy($imageOriginal); //width

        $imageCrypto = imagecreate($x_dimension, $y_dimension);
        $binaryText = str_split($this->textBinASCII('Hello nigga'));
        $textCount = count($binaryText);
        $count = 0;

//        $text = $this->ASCIIBinText($binaryText);

        for ($x = 0; $x < $x_dimension; $x++) {

            if ($count > $textCount)
                break;

            for ($y = 0; $y < $y_dimension; $y++) {

                if ($count > $textCount)
                    break;

                $rgbOriginal = imagecolorat($imageOriginal, $x, $y);

                $r = ($rgbOriginal >> 16) & 0xFF;
                $g = ($rgbOriginal >> 8) & 0xFF;
                $b = $rgbOriginal & 0xFF;

                $blueBinaryArray = str_split((string)base_convert($b,10,2));
                $blueBinaryArray[count($blueBinaryArray)-1] = $binaryText[$count];
                $blueBinary = implode($blueBinaryArray);

                $color = imagecolorallocate($imageOriginal,
                    $r,
                    $g,
                    $blueBinary);
                imagesetpixel($imageCrypto, $x, $y, $color);

                $count++;
            }
        }
        return response()->json(['success' => true]);
    }

    function textBinASCII($text)
    {
        $bin = array();
        for($i=0; strlen($text)>$i; $i++)
            $bin[] = decbin(ord($text[$i]));
        return implode(' ',$bin);
    }

    function ASCIIBinText($bin)
    {
        $text = array();
        $bin = explode(" ", $bin);
        for($i=0; count($bin)>$i; $i++)
            $text[] = chr(bindec($bin[$i]));
        return implode($text);
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
