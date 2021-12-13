<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\PostController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
header('Access-Control-Allow-Origin: *');

header('Access-Control-Allow-Credentials: true');

header('Access-Control-Allow-Headers: *');

header('Access-Control-Allow-Method: *');
    //MIDDLEWARE
    Route::middleware([JwtAuth::class])->group(function(){

    //POSTS SECTION
    //Create Post
    Route::post('SaveImage', [PostController::class, 'SaveImage']);
    //Get specific Post
    Route::get('GetPost/{id}', [PostController::class, 'GetPost']);
    //Get All Post
    //Delete Post
    Route::delete('DeletePost/{id}', [PostController::class, 'DeletePost']);
    //Update Post
    Route::put('update/{id}', [PostController::class, 'UpdatePost']);
});
