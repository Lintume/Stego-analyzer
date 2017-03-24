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
Route::get('/steganography', ['as' => 'stegonography', 'uses' => 'PixelController@index']);
Route::post('/analyze', ['as' => 'analyze', 'uses' => 'PixelController@analyze']);

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
