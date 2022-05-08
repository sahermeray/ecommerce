<?php

use Illuminate\Support\Facades\Route;

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

define('PAGINATION_COUNT',10);

Route::group(['namespace'=>'admin','middleware'=>'auth:admin'],function(){
    Route::get('/', 'DashboardController@index')->name('admin.dashboard');

    #############languages start#############
    Route::group(['prefix'=>'languages'],function(){
        Route::get('/','LanguagesController@index')->name('admin.languages');
        Route::get('create','LanguagesController@create')->name('admin.languages.create');
        Route::post('store','LanguagesController@store')->name('admin.languages.store');
        Route::get('edit/{id}','LanguagesController@edit')->name('admin.languages.edit');
        Route::post('update/{id}','LanguagesController@update')->name('admin.languages.update');
        Route::get('delete/{id}','LanguagesController@destroy')->name('admin.languages.delete');
    });
    ############languages end ###############

    #############mainCateories start#############
    Route::group(['prefix'=>'main_categories'],function(){
        Route::get('/','mainCategoriesController@index')->name('admin.maincategories');
        Route::get('create','mainCategoriesController@create')->name('admin.maincategories.create');
        Route::post('store','mainCategoriesController@store')->name('admin.maincategories.store');
        Route::get('edit/{id}','mainCategoriesController@edit')->name('admin.maincategories.edit');
        Route::post('update/{id}','mainCategoriesController@update')->name('admin.maincategories.update');
        Route::get('delete/{id}','mainCategoriesController@destroy')->name('admin.maincategories.delete');
        Route::get('change_status/{id}','mainCategoriesController@changeStatus')->name('admin.maincategories.changeStatus');
    });
    ############mainCategories end ###############

    #############vendors start#############
    Route::group(['prefix'=>'vendors'],function(){
        Route::get('/','VendorsController@index')->name('admin.vendors');
        Route::get('create','VendorsController@create')->name('admin.vendors.create');
        Route::post('store','VendorsController@store')->name('admin.vendors.store');
        Route::get('edit/{id}','VendorsController@edit')->name('admin.vendors.edit');
        Route::post('update/{id}','VendorsController@update')->name('admin.vendors.update');
        Route::get('delete/{id}','VendorsController@destroy')->name('admin.vendors.delete');
        Route::get('change_status/{id}','VendorsController@changeStatus')->name('admin.vendors.changeStatus');
    });
    ############vendors end ###############

    #############subCateories start#############
    Route::group(['prefix'=>'sub_categories'],function(){
        Route::get('/','S   SubCategoryController@index')->name('admin.subcategories');
        Route::get('create','SubCategoryController@create')->name('admin.subcategories.create');
        Route::post('store','SubCategoryController@store')->name('admin.subcategories.store');
        Route::get('edit/{id}','SubCategoryController@edit')->name('admin.subcategories.edit');
        Route::post('update/{id}','SubCategoryController@update')->name('admin.subcategories.update');
        Route::get('delete/{id}','SubCategoryController@destroy')->name('admin.subcategories.delete');
        Route::get('change_status/{id}','SubCategoryController@changeStatus')->name('admin.subcategories.changeStatus');
    });
    ############subCategories end ###############

});


Route::group(['namespace'=>'admin','middleware'=>'guest:admin'],function(){
  Route::get('login','LoginController@getLogin')->name('get.admin.login');
    Route::post('login','LoginController@login')->name('admin.login');
});