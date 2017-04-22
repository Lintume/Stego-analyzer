<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PixelController extends Controller
{
    public function LSBAnalyzeEncodeHash(Request $request)
    {
        $pictures = $request->get('pictures');
        $original = preg_replace('/data:image\/\w+;base64,/', '', $pictures['original']);
        $original = base64_decode($original);
        $imageOriginal = imagecreatefromstring($original);

        $x_dimension = imagesx($imageOriginal); //height
        $y_dimension = imagesy($imageOriginal); //width

        $middleX = $x_dimension/2;
        $middleY = $y_dimension/2;

        $imageCrypto = $imageOriginal;
        $string =  $request->get('text');
        $string .= '~';
        $binaryText = str_split($this->textBinASCII($string)); //string to array
        $textCount = count($binaryText);
        $count = 0;
        $countLengthHash = 0;
        $countLengthFlag = false;

        for ($x = 0; $x < $x_dimension; $x++) {

            if ($count >= $textCount)
                break;

            for ($y = 0; $y < $y_dimension; $y++) {

                if ($count >= $textCount)
                    break;

                if($x == $middleX && $y == $middleY)
                {
                    $countLengthFlag = true;
                }

                if ($countLengthHash == 224)
                {
                    $countLengthFlag = false;
                }

                if($countLengthFlag == true)
                {
                    $countLengthHash++;
                    continue;
                }

                $rgbOriginal = imagecolorat($imageOriginal, $x, $y);

                $r = ($rgbOriginal >> 16) & 0xFF;
                $g = ($rgbOriginal >> 8) & 0xFF;
                $b = $rgbOriginal & 0xFF;

                $blueBinaryArray = str_split((string)base_convert($b,10,2));
                $blueBinaryArray[count($blueBinaryArray)-1] = $binaryText[$count];
                $blueBinary = implode($blueBinaryArray);

                $color = imagecolorallocate($imageOriginal, $r, $g,
                    bindec($blueBinary));
                imagesetpixel($imageCrypto, $x, $y, $color);

                $count++;
            }
        }

        $hash = hash_file('md5', $original);
        $binaryHash = str_split($this->textBinASCII($hash)); //string to array
        $hashCount = count($binaryHash);
        $count = 0;

        for ($x = $middleX; $x < $middleX + $hashCount; $x++) {
            if ($count >= $hashCount)
                break;
            for ($y = $middleY; $y < $middleY + $hashCount; $y++) {
                if ($count >= $hashCount)
                    break;
                $rgbOriginal = imagecolorat($imageOriginal, $x, $y);

                $r = ($rgbOriginal >> 16) & 0xFF;
                $g = ($rgbOriginal >> 8) & 0xFF;
                $b = $rgbOriginal & 0xFF;

                $blueBinaryArray = str_split((string)base_convert($b,10,2));
                $blueBinaryArray[count($blueBinaryArray)-1] = $binaryHash[$count];
                $blueBinary = implode($blueBinaryArray);

                $color = imagecolorallocate($imageOriginal, $r, $g,
                    bindec($blueBinary));
                imagesetpixel($imageCrypto, $x, $y, $color);

                $count++;
            }
        }

        $imageSave = imagepng($imageCrypto,'C:\Users\User\Desktop\sdf.png');
        ob_start();
        imagepng($imageCrypto);
        $image_string = base64_encode(ob_get_contents());
        ob_end_clean();
        $base64 = 'data:image/png;base64,' . $image_string;
        imagedestroy($imageCrypto);
        return response()->json(['data' => $base64]);
    }
}
