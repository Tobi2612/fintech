<?php

use App\Classes\Register;
use App\Classes\Transaction;
use App\Classes\AdminTransaction;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AccountController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group([

    'middleware' => 'api',
    'prefix' => 'auth'

], function ($router) {
    
    Route::post('register',[AuthController::class,'register']);
    Route::post('login', [AuthController::class, 'login']);
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('refresh',[AuthController::class, 'refresh']);
    Route::get('me', [AuthController::class, 'me']);


    Route::get('history', [AccountController::class, 'transactionHistoryM']);
    Route::get('details', [AccountController::class, 'getAccountNumber']);
    Route::get('all', [AuthController::class, 'all']);


    Route::put('withdraw',[AccountController::class,'withdrawal']);
    Route::put('transfer',[AccountController::class,'transfer']);



    Route::post('admin/login', [AuthController::class, 'adminLogin']);
    Route::post('admin/register',[AuthController::class,'registerAdmin']);
    Route::put('admin/credit',[AccountController::class,'adminCredit']); 
    Route::put('admin/debit',[AccountController::class,'admindebit']); 
    Route::get('admin/view/users', [AccountController::class,'adminViewUser']);
    Route::get('admin/view/admins', [AccountController::class,'adminViewAdmin']);
    Route::get('admin/search', [AccountController::class,'adminSearch']);
    Route::get('admin/view/user/{id}', [AccountController::class,'getbyId']);


    // Route::get('test', [AccountController::class,'checkAdmin']);
    // Route::get('test', [AuthController::class,'checkAdmin']);


});