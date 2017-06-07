<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LSB2LastBitsBlueController extends Controller
{
    public function show()
    {
        return view('lsb2bits');
    }

    public function encode(Request $request)
    {
        $pictures = $request->get('pictures');
        $original = preg_replace('/data:image\/\w+;base64,/', '', $pictures['original']);
        $original = base64_decode($original);
        $imageOriginal = imagecreatefromstring($original);
        $x_dimension = imagesx($imageOriginal); //height
        $y_dimension = imagesy($imageOriginal); //width

        $key = $request->get('password');
        $offsetPercent = $request->get('offset');
        $lengthStegoContainer = $x_dimension * $y_dimension;
        $offset = round($lengthStegoContainer / 100 * $offsetPercent);

        $imageCrypto = $imageOriginal;
        $string =  $request->get('text');
        $stringCount = strlen($string);

        $iv = "1234567812345678";

        $stringCrypto = openssl_encrypt($string, 'AES-256-CFB', $key, OPENSSL_RAW_DATA, $iv);
        $bin = $this->textBinASCII2($stringCrypto); //string to array

        $stringLength = $this->textBinASCII2((string)strlen($bin));

        $signBegin = $this->textBinASCII2('stego');
        $sign = $this->textBinASCII2('gravitation');

        $binaryText = str_split($signBegin.$stringLength.$sign.$bin);
        $textCount = count($binaryText) + $offset;
        $count = $offset;
        $countOffset = 0;

        for ($x = 0; $x < $x_dimension; $x++) {

            if ($count >= $textCount)
                break;

            for ($y = 0; $y < $y_dimension; $y++) {

                if ($countOffset < $offset) {
                    $countOffset++;
                    continue;
                }

                if ($count >= $textCount)
                    break;

                $rgbOriginal = imagecolorat($imageOriginal, $x, $y);

                $r = ($rgbOriginal >> 16) & 0xFF;
                $g = ($rgbOriginal >> 8) & 0xFF;
                $b = $rgbOriginal & 0xFF;

                $blueBinaryArray = str_split((string)base_convert($b,10,2));
                $blueBinaryArray[count($blueBinaryArray)-2] = $binaryText[$count-$offset];
                if(array_key_exists((int)($count - $offset + 1), $binaryText)) {
                    $blueBinaryArray[count($blueBinaryArray) - 1] = $binaryText[$count - $offset + 1];
                }
                $blueBinary = implode($blueBinaryArray);

                $color = imagecolorallocate($imageOriginal, $r, $g,
                    bindec($blueBinary));
                imagesetpixel($imageCrypto, $x, $y, $color);

                $count+=2;
            }
        }
        //$imageSave = imagepng($imageCrypto,'C:\Users\User\Desktop\2bit\2bit-'.$stringCount.'.png');
        ob_start();
        imagepng($imageCrypto);
        $image_string = base64_encode(ob_get_contents());
        ob_end_clean();
        $base64 = 'data:image/png;base64,' . $image_string;
        imagedestroy($imageCrypto);
        return response()->json(['data' => $base64]);
    }

    public function decode(Request $request)
    {
        $pictures = $request->get('pictures');
        $original = preg_replace('/data:image\/\w+;base64,/', '', $pictures['original']);
        $original = base64_decode($original);
        $imageOriginal = imagecreatefromstring($original);

        $x_dimension = imagesx($imageOriginal); //height
        $y_dimension = imagesy($imageOriginal); //width

        $binaryString = '';

        for ($x = 0; $x < $x_dimension; $x++) {

            for ($y = 0; $y < $y_dimension; $y++) {

                $rgbOriginal = imagecolorat($imageOriginal, $x, $y);

                $b = $rgbOriginal & 0xFF;

                $blueBinaryArray = str_split((string)base_convert($b, 10, 2));
                if(count($blueBinaryArray) < 2)
                {
                    $bit1 = 0;
                    $bit2 = 0;
                }
                else {
                    $bit1 = $blueBinaryArray[count($blueBinaryArray) - 2];
                    $bit2 = $blueBinaryArray[count($blueBinaryArray) - 1];
                }
                $binaryString .= $bit1.$bit2;
            }
        }

        $iv = "1234567812345678";
        $key = $request->get('password');

        $sign = $this->textBinASCII2('gravitation');
        $signBegin = $this->textBinASCII2('stego');

        $lengthSign = strlen($sign);
        $lengthSignBegin = strlen($signBegin);

        $positionSign = strpos($binaryString, $sign);
        $positionSignBegin = strpos($binaryString, $signBegin);

        $lengthLength = $positionSign - $positionSignBegin - $lengthSignBegin;

        $offsetToLength = $positionSignBegin + $lengthSignBegin;

        $lengthBinData = mb_substr($binaryString, $offsetToLength, $lengthLength);

        $lengthData = $this->stringBinToStringChars8($lengthBinData);
        $positionData = $positionSign + $lengthSign;
        $binaryData = mb_substr($binaryString, $positionData, $lengthData);

        $cryptoString = $this->stringBinToStringChars8($binaryData);
        $output = openssl_decrypt($cryptoString, 'AES-256-CFB', $key, OPENSSL_RAW_DATA, $iv);

        return response()->json(['text' => json_encode( utf8_encode( $output ) )]);
    }

    public function stringBinToStringChars8($strBin)
    {
        $arrayChars = str_split($strBin, 8);
        $result = '';
        for ($i = 0; $i<count($arrayChars); $i++)
        {
            $result.=$this->ASCIIBinText2($arrayChars[$i]);
        }
        return $result;
    }

    function textBinASCII2($text)
    {
        $bin = array();
        $max = 0;
        for($i=0; strlen($text)>$i; $i++) {
            $bin[] = decbin(ord($text[$i]));
            if(strlen($bin[$i]) < 8)
            {
                $countNull = 8 - strlen($bin[$i]);
                $stringNull = '';
                for($j = 0; $j < $countNull; $j++) {
                    $stringNull .= '0';
                }
                $bin[$i] = $stringNull.$bin[$i];
            }
            if(strlen($bin[$i]) > 8 && strlen($bin[$i]) > $max)
            {
                $max = strlen($bin[$i]);
            }
        }
        return implode('',$bin);
    }

    function ASCIIBinText2($bin)
    {
        $text = array();
        $bin = explode(" ", $bin);
        for($i=0; count($bin)>$i; $i++)
            $text[] = chr(bindec($bin[$i]));
        return implode($text);
    }
}
