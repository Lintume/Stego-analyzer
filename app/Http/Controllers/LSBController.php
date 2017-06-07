<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LSBController extends Controller
{
    public function encodeLSB()
    {
        return view('lsb');
    }

    public function LSBAnalyzeEncode(Request $request)
    {
        $pictures = $request->get('pictures');
        $original = preg_replace('/data:image\/\w+;base64,/', '', $pictures['original']);
        $original = base64_decode($original);
        $imageOriginal = imagecreatefromstring($original);

        $x_dimension = imagesx($imageOriginal); //height
        $y_dimension = imagesy($imageOriginal); //width

        $imageCrypto = $imageOriginal;
        $string =  $request->get('text');
        $stringCount = strlen($string);
        $string .= '~';
        $binaryText = str_split($this->textBinASCII($string)); //string to array
        $textCount = count($binaryText);
        $count = 0;

        for ($x = 0; $x < $x_dimension; $x++) {

            if ($count >= $textCount)
                break;

            for ($y = 0; $y < $y_dimension; $y++) {

                if ($count >= $textCount)
                    break;

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
        //$imageSave = imagepng($imageCrypto,'C:\Users\User\Desktop\lsb\lsb-'.$stringCount.'.png');
        ob_start();
        imagepng($imageCrypto);
        $image_string = base64_encode(ob_get_contents());
        ob_end_clean();
        $base64 = 'data:image/png;base64,' . $image_string;
        imagedestroy($imageCrypto);
        return response()->json(['data' => $base64]);
    }

    public function LSBAnalyzeDecode(Request $request)
    {
        $pictures = $request->get('pictures');
        $original = preg_replace('/data:image\/\w+;base64,/', '', $pictures['original']);
        $original = base64_decode($original);
        $imageOriginal = imagecreatefromstring($original);

        $x_dimension = imagesx($imageOriginal); //height
        $y_dimension = imagesy($imageOriginal); //width

        $tild = '~';
        $binaryTild = str_split($this->textBinASCII($tild)); //string to array
        $flagEnd = false;
        $flagTild = [];
        for($i = 0; $i<7; $i++)
        {
            array_push($flagTild, false);
        }
        $binaryString = [];
        $binaryArrayResult = [];
        $i = 0;
        for ($x = 0; $x < $x_dimension; $x++) {

            for ($y = 0; $y < $y_dimension; $y++) {

                $rgbOriginal = imagecolorat($imageOriginal, $x, $y);

                $b = $rgbOriginal & 0xFF;

                $blueBinaryArray = str_split((string)base_convert($b, 10, 2));
                $bit = $blueBinaryArray[count($blueBinaryArray) - 1];
                array_push($binaryString, $bit);

                $binaryTild[$i] == $bit ? $flagTild[$i] = true : $flagTild[$i] = false;

                $flagTrueTild = false;
                if ($i == 6) {
                    $flagTrueTild = true;
                    for ($k = 0; $k < 7; $k++) {
                        if ($flagTild[$k] == false) {
                            $flagTrueTild = false;
                        }
                    }

                    if ($flagTrueTild == true) {
                        $flagEnd = true;
                        break;
                    }
                }
                $i++;
                if($i == 7 && $flagTrueTild == false)
                {
                    $i = 0;
                    array_push($binaryArrayResult, $binaryString);
                    $binaryString = [];
                    for($l = 0; $l<7; $l++)
                    {
                        $flagTild[$l] = false;
                    }
                }
            }
            if($flagEnd == true)
            {
                break;
            }
        }

        $result = '';
        for ($i = 0; $i<count($binaryArrayResult); $i++)
        {
            $result.=$this->ASCIIBinText(implode('', $binaryArrayResult[$i]));
        }

        return response()->json(['text' => $result]);
    }

    function textBinASCII($text)
    {
        $bin = array();
        for($i=0; strlen($text)>$i; $i++) {
            $bin[] = decbin(ord($text[$i]));
            if(strlen($bin[$i]) < 7)
            {
                $countNull = 7 - strlen($bin[$i]);
                $stringNull = '';
                for($j = 0; $j < $countNull; $j++) {
                    $stringNull .= '0';
                }
                $bin[$i] = $stringNull.$bin[$i];
            }
        }
        return implode('',$bin);
    }

    function ASCIIBinText($bin)
    {
        $text = array();
        $bin = explode(" ", $bin);
        for($i=0; count($bin)>$i; $i++)
            $text[] = chr(bindec($bin[$i]));
        return implode($text);
    }
}
