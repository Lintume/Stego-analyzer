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
               $data = preg_replace('/data:image\/jpeg;base64,/', '', $picture['base64Picture']);
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

        $imageCrypto = $imageOriginal;
        $string = 'Lorem 
        
        ipsum dolor sit amet, consectetur adipiscing elit. Aenean tristique mi urna. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Morbi vel velit sed libero finibus volutpat at sed est. Ut iaculis lorem lacus, sed vehicula nisl fermentum quis. Nam scelerisque interdum facilisis. In fermentum, mi sit amet gravida aliquam, risus eros dapibus eros, vitae facilisis urna purus at augue. Aliquam eros urna, maximus et arcu non, mollis volutpat neque. In hac habitasse platea dictumst. Phasellus maximus scelerisque lacinia. Nunc accumsan ante non quam tincidunt rutrum. Pellentesque aliquam convallis fermentum.
Proin arcu est, laoreet vitae orci sit amet, aliquet imperdiet nunc. Nam ut lacus et tellus laoreet ornare ut quis ex. Praesent pulvinar lacus sit amet diam fermentum, id fermentum turpis varius. Phasellus sodales metus justo, nec varius odio vulputate nec. Aenean dapibus sapien sit amet venenatis ornare. Maecenas eu enim in augue dapibus dapibus. Sed sit amet turpis auctor, scelerisque risus id, volutpat sem. Vivamus ullamcorper ornare quam ut accumsan. Curabitur varius mauris in mauris imperdiet facilisis. Nunc eu dolor nibh. In lacinia consectetur lectus eu egestas. Donec diam lorem, iaculis ac sapien quis, pulvinar tincidunt risus. Aliquam ut ex facilisis, posuere metus eu, dignissim dui. Pellentesque tempus luctus erat, sit amet vulputate sem laoreet non. Praesent molestie odio vitae sapien elementum, tempus bibendum tortor faucibus. Integer ac quam velit.
In tristique non velit ac semper. Vivamus eu arcu pretium, sollicitudin velit sed, laoreet mauris. Nunc elementum quis diam at blandit. Pellentesque neque tortor, dapibus quis tristique vitae, rhoncus eu mi. Duis augue dolor, faucibus at augue sed, porttitor accumsan augue. Duis vitae fermentum ante, eu porta velit. Donec finibus tellus metus, eget malesuada mi luctus id. Cras mattis orci in luctus semper. Ut laoreet nisi vitae augue iaculis, ut cursus augue lacinia. Mauris rutrum sapien tellus, id pretium metus fermentum eget. Nunc eget lorem ac leo tempor rutrum. Proin mauris orci, fringilla quis erat nec, ornare tempus nisi.
Nullam tincidunt convallis dolor id porta. Integer tempor sapien a lectus tempor pretium. Fusce ut scelerisque eros, at volutpat turpis. Sed varius arcu tincidunt urna sodales, vel malesuada massa consequat. Suspendisse facilisis luctus leo, vel viverra orci ullamcorper nec. Donec urna urna, pretium venenatis nunc nec, sodales pretium augue. Mauris congue porttitor ex vel finibus. Praesent a pharetra erat. Donec sed dictum sem. Orci varius natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus.
Sed ultricies condimentum risus, at dapibus libero tristique vitae. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Nam in lacus in erat pretium feugiat euismod porttitor sapien. Etiam cursus ante et tempor iaculis. Vivamus massa urna, placerat eu diam eget, sodales laoreet ex. Mauris convallis mauris sit amet consectetur rutrum. Integer sodales euismod maximus. In augue leo, elementum in rhoncus accumsan, vehicula quis odio. Nullam non ultricies libero, a sodales purus. Orci varius natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Cras venenatis elit ut lorem interdum mattis. Etiam vitae bibendum velit. Aliquam ornare eros bibendum vulputate pretium. Aliquam elementum urna in erat dapibus, maximus laoreet diam feugiat. Donec mattis venenatis ante, a tincidunt libero placerat sit amet. Nulla ut sodales neque, et lobortis lacus.';
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

                $color = imagecolorallocate($imageOriginal,
                    $r,
                    $g,
                    bindec($blueBinary));
                imagesetpixel($imageCrypto, $x, $y, $color);
                $rgb = imagecolorat($imageCrypto, $x, $y);
                $bnew = $rgb & 0xFF;

                $count++;
            }
        }
        $imageSave = imagepng($imageCrypto,'C:\Users\User\Desktop\sdf.png');
        imagedestroy($imageCrypto);
        return response()->json(['success' => true]);
    }

    public function LSBAnalyzeDecode(Request $request)
    {
        $pictures = $request->get('pictures');
        $original = preg_replace('/data:image\/png;base64,/', '', $pictures['original']);
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

        return response()->json(['success' => $result]);
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
