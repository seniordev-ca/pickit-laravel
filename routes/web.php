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

Route::get('/', 'AdminController@index');
Route::get('/login', 'AdminController@index');
Route::post('/login', 'AdminController@doLogin');
Route::get('/logout', 'AdminController@logout');

Route::middleware('admin-auth')->group(function (){
    Route::get('/dashboard', 'AdminController@dashboard');

    Route::prefix('users')->group(function () {

        Route::get('/', 'AdminController@showUsersPage');
        Route::get('/add', 'AdminController@showUserAddPage');
        Route::post('/add', 'AdminController@addUser');
        Route::get('/edit/{id}', 'AdminController@showUserEditPage');
        Route::post('/edit', 'AdminController@editUser');
    });

    Route::prefix('customers')->group(function () {

        Route::get('/', 'AdminController@showCustomersPage');
        Route::get('/add', 'AdminController@showCustomerAddPage');
        Route::post('/add', 'AdminController@addCustomer');
        Route::get('/edit/{id}', 'AdminController@showCustomerEditPage');
        Route::post('/edit', 'AdminController@editCustomer');
    });

    Route::prefix('products')->group(function () {

        Route::get('/', 'AdminController@showProductsPage');
        Route::get('/add', 'AdminController@showProductAddPage');
        Route::post('/add', 'AdminController@addProduct');
        Route::get('/edit/{id}', 'AdminController@showProductEditPage');
        Route::post('/edit', 'AdminController@editProduct');
        Route::post('/del', 'AdminController@delProduct');
        Route::post('/toggle_visible', 'AdminController@toggleProductVisible');
    });

    Route::prefix('categories')->group(function () {

        Route::get('/', 'AdminController@showCategoriesPage');
        Route::get('/add', 'AdminController@showCategoryAddPage');
        Route::post('/add', 'AdminController@addCategory');
        Route::get('/edit/{id}', 'AdminController@showCategoryEditPage');
        Route::get('/edit', 'AdminController@showCategoriesPage');
        Route::post('/edit', 'AdminController@editCategory');
        Route::post('/del', 'AdminController@delCategory');
        Route::post('/toggle_visible', 'AdminController@toggleCategoryVisible');
    });

});
