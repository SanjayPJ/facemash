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

Route::get('{type}/{filename}', function ($type, $filename)
{
    if($type == '60'){
        $path = storage_path('app/public/60x60/' . $filename);
    }else if($type == 'image'){
        $path = storage_path('app/public/image/' . $filename);
    }
    
    // return $path;

    if (!File::exists($path)) {
        abort(404);
    }

    $file = File::get($path);
    $type = File::mimeType($path);

    $response = Response::make($file, 200);
    $response->header("Content-Type", $type);

    return $response;
});
