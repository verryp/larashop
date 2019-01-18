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

Route::middleware(['throttle:10,1', 'cors'])->prefix('api')->group(function(){

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
    // TODO adalah dengan menjalankan php artisan make:controller NamaController --api
    Route::apiResources([
        'books' => 'Api\BookController',
        'users' => 'Api\UserController',
        'categories' => 'Api\CategoryController',
        'orders' => 'Api\OrderController'
    ]);

});

// Route::get('/book/api', function(){
//     return new BookResource(Book::find(1));
// });
