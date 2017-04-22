<?php

namespace App\Http\Controllers;

use App\Http\Requests\CryptoRequest;
use App\Models\Pixel;
use Illuminate\Http\Request;
use Intervention\Image\Facades\Image;

class PixelController extends Controller
{
    public function index()
    {
        return view('stegonography'
        );
    }

    public function encodeLSB()
    {
        return view('lsb_encode'
        );
    }

    public function encodeLSBCrypt()
    {
        return view('lsb_encode_crypt'
        );
    }

    public function decodeLSB()
    {
        return view('lsb_decode'
        );
    }

    public function decodeLSBCrypt()
    {
        return view('lsb_decode_crypt'
        );
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
        $imageSave = imagepng($imageCrypto,'C:\Users\User\Desktop\sdf.png');
        ob_start();
        imagepng($imageCrypto);
        $image_string = base64_encode(ob_get_contents());
        ob_end_clean();
        $base64 = 'data:image/png;base64,' . $image_string;
        imagedestroy($imageCrypto);
        return response()->json(['data' => $base64]);
    }

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

    public function LSBEncodeCrypt(Request $request)
    {
        $pictures = $request->get('pictures');
        $original = preg_replace('/data:image\/\w+;base64,/', '', $pictures['original']);
        $original = base64_decode($original);
        $imageOriginal = imagecreatefromstring($original);
        $x_dimension = imagesx($imageOriginal); //height
        $y_dimension = imagesy($imageOriginal); //width

        $key = $request->get('password');
        $imageCrypto = $imageOriginal;
        $string =  $request->get('text');
        
        $iv = "1234567812345678";

        $stringCrypto = openssl_encrypt($string, 'AES-256-CFB', $key, OPENSSL_RAW_DATA, $iv);
        $bin = $this->textBinASCII2($stringCrypto); //string to array

        $stringLength = $this->textBinASCII2((string)strlen($bin));
        //$unbinStringLength = (int)$this->stringBinToStringChars8($stringLength);

        //$cryptoString = $this->stringBinToStringChars8($bin);
        //$output = openssl_decrypt($cryptoString, 'AES-256-CFB', $key, OPENSSL_RAW_DATA, $iv);

        $sign = $this->textBinASCII2('gravitation');
        //$unbinSign = $this->stringBinToStringChars8($sign);
        
        $binaryText = str_split($stringLength.$sign.$bin);
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
        $imageSave = imagepng($imageCrypto,'C:\Users\User\Desktop\crypt.png');
        ob_start();
        imagepng($imageCrypto);
        $image_string = base64_encode(ob_get_contents());
        ob_end_clean();
        $base64 = 'data:image/png;base64,' . $image_string;
        imagedestroy($imageCrypto);
        return response()->json(['data' => $base64]);
    }

    public function stringBinToStringChars8($strBin)
    {
        $arrayChars = str_split($strBin, 8);
        $result = '';
        for ($i = 0; $i<count($arrayChars); $i++)
        {
            $result.=$this->ASCIIBinText($arrayChars[$i]);
        }
        return $result;
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

    public function LSBDecodeCrypt(Request $request)
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
                $bit = $blueBinaryArray[count($blueBinaryArray) - 1];
                $binaryString .= $bit;
            }
        }

        $iv = "1234567812345678";
        $key = $request->get('password');

        $sign = $this->textBinASCII2('gravitation');
        $lengthSign = strlen($sign);
        $position = strpos($binaryString, $sign);
        $lengthBinData = mb_substr($binaryString, 0, $position);
        $lengthData = $this->stringBinToStringChars8($lengthBinData);
        $positionData = $position + $lengthSign;
        $binaryData = mb_substr($binaryString, $positionData, $lengthData);

        $cryptoString = $this->stringBinToStringChars8($binaryData);
        $output = openssl_decrypt($cryptoString, 'AES-256-CFB', $key, OPENSSL_RAW_DATA, $iv);

        return response()->json(['text' => $output]);
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

        return $sum1 / $sum2;
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
