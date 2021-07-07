<?php

use App\Http\Controllers\API\Admin\AuthController;
use App\Http\Controllers\API\CategoriesController;
use App\Http\Controllers\API\User\AuthLoginController;
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

Route::group(['middleware' => ['api',/*'checkPassword',*/'checkLanguage']], function () {
    Route::post('get-main-categories',[CategoriesController::class,'index']);
    Route::post('get-main-category-byid',[CategoriesController::class,'getCategoryById']);
    Route::post('change-main-category-status',[CategoriesController::class,'changeCategoryStatus']);

    Route::group(['prefix'=>'admin'],function (){
        Route::post('login',[AuthController::class,'login']);
        Route::post('logout',[AuthController::class,'logout'])->middleware('auth.guard:admin-api');

        // Invalidate token security side   الحاجات دي بتتعمل عليشنان لما بعمل logout التوكن بيفضل موجود فانا لازم اكسره بالداله بروكن

        // broken access controller user enumeration
    });

    Route::group(['prefix'=>'user','namespace'=>'User'],function (){
        Route::post('login',[AuthLoginController::class,'login']);
        // Invalidate token security side   الحاجات دي بتتعمل عليشنان لما بعمل logout التوكن بيفضل موجود فانا لازم اكسره بالداله بروكن

        // broken access controller user enumeration
    });

    Route::group(['prefix'=>'user','middleware'=>'auth.guard:user-api'],function () {
        Route::post('profile',function(){
            return  \Auth::user(); // return authenticated user data
        });
    });
});


//Route::group(['middleware' => ['api','checkPassword','changeLanguage','checkAdminToken:admin-api']], function () {
//    Route::get('offers', [CategoriesController::class,'index']);
//});
