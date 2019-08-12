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

Route::middleware('customer-auth')->group(function (){
    Route::get('/profile', 'AdminController@showProfilePage');
    Route::post('/profile/edit', 'AdminController@editProfile');
    Route::get('/my-page', 'AdminController@showMyPage');
    Route::get('/customers/detail/{id}', 'AdminController@showCustomerDetailPage');

    Route::prefix('products')->group(function () {

        Route::get('/', 'AdminController@showProductFirstPage');
        Route::get('/{customer_id}', 'AdminController@showProductsPage');
        Route::get('/{customer_id}/add', 'AdminController@showProductAddPage');
        Route::post('/{customer_id}/add', 'AdminController@addProduct');
        Route::get('/edit/{id}', 'AdminController@showProductEditPage');
        Route::post('/edit', 'AdminController@editProduct');
        Route::post('/del', 'AdminController@delProduct');
        Route::get('/detail/{id}', 'AdminController@showProductDetailPage');
        Route::post('/toggle-visible', 'AdminController@toggleProductVisible');
    });

    Route::prefix('categories')->group(function () {

        Route::get('/', 'AdminController@showCategoryFirstPage');
        Route::get('/{customer_id}', 'AdminController@showCategoriesPage');
        Route::get('/{customer_id}/add', 'AdminController@showCategoryAddPage');
        Route::post('/{customer_id}/add', 'AdminController@addCategory');
        Route::get('/edit/{id}', 'AdminController@showCategoryEditPage');
        Route::get('/edit', 'AdminController@showCategoriesPage');
        Route::post('/edit', 'AdminController@editCategory');
        Route::post('/del', 'AdminController@delCategory');
        Route::get('/detail/{id}', 'AdminController@showCategoryDetailPage');
        Route::post('/toggle-visible', 'AdminController@toggleCategoryVisible');
    });

});

Route::middleware('user-auth')->group(function (){
    Route::get('/dashboard', 'AdminController@dashboard');

    Route::prefix('customers')->group(function () {
        Route::get('/', 'AdminController@showCustomersPage');
        Route::post('/toggle-add-product', 'AdminController@toggleCustomerAddProduct');
    });

});

Route::middleware('admin-auth')->group(function (){
    Route::prefix('employees')->group(function () {

        Route::get('/', 'AdminController@showEmployeesPage');
        Route::get('/add', 'AdminController@showEmployeeAddPage');
        Route::post('/add', 'AdminController@addEmployee');
        Route::get('/edit/{id}', 'AdminController@showEmployeeEditPage');
        Route::post('/edit', 'AdminController@editEmployee');
        Route::post('/del', 'AdminController@delEmployee');
        Route::post('/toggle-enable', 'AdminController@toggleEmployeeEnable');
    });

    Route::prefix('customers')->group(function () {

        Route::get('/add', 'AdminController@showCustomerAddPage');
        Route::post('/add', 'AdminController@addCustomer');
        Route::get('/edit/{id}', 'AdminController@showCustomerEditPage');
        Route::post('/edit', 'AdminController@editCustomer');
        Route::post('/del', 'AdminController@delCustomer');
        Route::post('/toggle-enable', 'AdminController@toggleCustomerEnable');
        Route::get('/print-invoice/{id}', 'AdminController@showCustomerInvoicePrintPreviewPage');
        Route::get('/print-invoice/{id}/print', 'AdminController@printCustomerInvoice');
        Route::post('/resuscitate-customer', 'AdminController@resuscitateCustomer');

    });
});
