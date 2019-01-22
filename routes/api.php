<?php

use Illuminate\Http\Request;

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

Route::prefix('api')->group(function(){

    // * route yang sifatnya public
    Route::post('login', 'Api\AuthController@login');
    Route::post('register', 'Api\AuthController@register');

    Route::get('categories/random/{count}', 'Api\CategoryController@random');
    Route::get('categories/slug/{slug}', 'Api\CategoryController@slug');
    Route::resource('categories', 'Api\CategoryController');
    Route::resource('books', 'Api\BookController');

    Route::get('books/top/{count}', 'Api\BookController@top');
    Route::get('books/slug/{slug}', 'Api\BookController@slug');

    // * route yang sifatnya private
    Route::group(['middleware' => ['auth:api', 'throttle:10,1', 'cors']], function () {

        // * route resource controller yang ke pake hanya index dan show
        // Route::resources([
        //     'books' => 'Api\BookController',
        //     'users' => 'Api\UserController',
        //     'categories' => 'Api\CategoryController',
        //     'orders' => 'Api\OrderController'
        // ])->only([
        //     'index', 'show'
        // ]);

        // * kebalikan dari method only
        // Route::resources([
        //     'books' => 'Api\BookController',
        //     'users' => 'Api\UserController',
        //     'categories' => 'Api\CategoryController',
        //     'orders' => 'Api\OrderController'
        // ])->expect([
        //     'create', 'store', 'update', 'destroy'
        // ]);

        // * include semua resource controller
        // Route::resources([
        //     'books' => 'Api\BookController',
        //     'users' => 'Api\UserController',
        //     'categories' => 'Api\CategoryController',
        //     'orders' => 'Api\OrderController'
        // ]);

        // * tanpa perlu menggunakan method only atau expect
        // ? karna di route api method create dan update tidak diperlukan, cara lain ..
        // TODO => adalah dengan menjalankan php artisan make:controller NamaController --api
        // Route::apiResources([
        //     'books' => 'Api\BookController',
        //     'users' => 'Api\UserController',
        //     'categories' => 'Api\CategoryController',
        //     'orders' => 'Api\OrderController',
        // ]);
        
        Route::post('logout', 'Api\AuthController@logout');
    });
});

// Route::get('/book/api', function(){
//     return new BookResource(Book::find(1));
// });
