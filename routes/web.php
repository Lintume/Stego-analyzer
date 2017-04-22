<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/vkauthorize', function () {
    return '<a href="https://oauth.vk.com/authorize?client_id=4506839&scope=status,friends&redirect_uri=http://api.vk.com/blank.html&display=page&response_type=token">Push the button</a>';
});

Route::get('/vklintu', function () {
    return '<a href="https://oauth.vk.com/authorize?client_id=5929085&scope=status,friends&redirect_uri=http://api.vk.com/blank.html&display=page&response_type=token">Push the button</a>';
});

Route::get('/leaders', ['as' => 'leaders', 'uses' => 'MemberController@show']);

Route::get('/steganography', ['as' => 'steganography', 'uses' => 'AnalyzeController@index']);
Route::post('/analyze', ['as' => 'analyze', 'uses' => 'AnalyzeController@analyze']);

Route::get('/lsb_view', ['as' => 'lsb', 'uses' => 'LSBController@encodeLSB']);
Route::post('/lsb_encode', ['as' => 'lsb_encode', 'uses' => 'LSBController@LSBAnalyzeEncode']);
Route::post('/lsb_decode', ['as' => 'lsb_decode', 'uses' => 'LSBController@LSBAnalyzeDecode']);

Route::get('/lsb_crypt_view', ['as' => 'lsbCrypt', 'uses' => 'LSBCryptController@encodeLSBCrypt']);
Route::post('/lsb_encode_crypt', ['as' => 'lsb_encode_crypt', 'uses' => 'LSBCryptController@LSBEncodeCrypt']);
Route::post('/lsb_decode_crypt', ['as' => 'lsb_decode_crypt', 'uses' => 'LSBCryptController@LSBDecodeCrypt']);

Route::get('/lsb_offset_view', ['as' => 'lsbOffset', 'uses' => 'LSBOffsetController@showOffsetLSB']);
Route::post('/lsb_offset_encode', ['as' => 'lsb_encode_offset', 'uses' => 'LSBOffsetController@LSBOffsetEncode']);
Route::post('/lsb_offset_decode', ['as' => 'lsb_decode_offset', 'uses' => 'LSBOffsetController@LSBOffsetDecode']);

Route::get('/lsb_2bits_view', ['as' => 'lsb2bits', 'uses' => 'LSB2LastBitsBlueController@show']);
Route::post('/lsb_2bits_encode', ['as' => 'lsb_encode_2bits', 'uses' => 'LSB2LastBitsBlueController@encode']);
Route::post('/lsb_2bits_decode', ['as' => 'lsb_decode_2bits', 'uses' => 'LSB2LastBitsBlueController@decode']);

Route::get('/lsb_3channels_view', ['as' => 'lsb3channels', 'uses' => 'LSB3channelsController@show']);
Route::post('/lsb_3channels_encode', ['as' => 'lsb_encode3channels', 'uses' => 'LSB3channelsController@encode']);
Route::post('/lsb_3channels_decode', ['as' => 'lsb_decode3channels', 'uses' => 'LSB3channelsController@decode']);

    Route::get('vk', function () {
        $client_id = '5922811';
        $scope = 'status';
        $group_ids = '48715015';
        echo "<a href=\"https://oauth.vk.com/authorize?client_id=$client_id&display=page&redirect_uri=http://mytattoo.sexy/oauth/vk/callback&scope=$scope&group_ids=$group_ids&response_type=code&v=5.62\">Push the button</a>";
    });
    Route::get('vk/callback', function (\Illuminate\Http\Request $request) {
        $client_id = '5922811';
        $client_secret = 'MXDFuLCCA5Or6LTWSP8K';
        if ($request->has('code')) {
            $code = $request->get('code');
            echo "<a href=\"https://oauth.vk.com/access_token?client_id=$client_id&client_secret=$client_secret&redirect_uri=http://mytattoo.sexy/oauth/vk/callback&code=$code\">Get the code</a>";
        }
        if ($request->has('access_token')) {
            dd($request->get('access_token'));
        }
    });
