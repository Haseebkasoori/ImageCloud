<?php

use App\Http\Controllers\API\RegistrationController;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\EmailVarified;
use App\Http\Controllers\API\ForgotPasswordController;
use App\Http\Controllers\API\LoginController;
use App\Http\Controllers\API\userController;

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
    //REGISTER
    Route::post('register', [UserController::class, 'register']);

    //MIDDLEWARE
    Route::middleware([EmailVarified::class])->group(function(){
    //LOGIN
    Route::post('UserLogin', [UserController::class, 'LoginUser']);
    //FORGOT PASSWORD
    Route::post('/forgotPassword', [UserController::class, 'forgotPassword']);
    });

    //EMAIL VERIFY
    Route::get('emailConfirmation/{email}/{token}', [UserController::class, 'verifyingEmail']);

    //MIDDLEWARE
    Route::middleware([JwtAuth::class])->group(function(){

    //CRUD Users SECTION
    //Get specific user
    Route::get('GetUser/{id}', [UserController::class, 'GetUser']);
    //Get All user
    Route::get('GetUser', [UserController::class, 'GetAllUsers']);
    //Update
    Route::put('updateUser/{id}', [UserController::class, 'UpdateUser']);
    //Delete
    Route::put('updateUserPassword/{id}', [UserController::class, 'updateUserPassword']);
    //Delete
    Route::delete('deleteUser/{id}', [UserController::class, 'DeleteUser']);
    //Search User
    Route::get('search/{name}', [UserController::class, 'SearchUser']);

    //LOGOUT
    Route::get('logout', [UserController::class, 'logout']);
});
