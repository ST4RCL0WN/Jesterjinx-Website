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

Route::get('/', 'HomeController@getIndex')->name('home');

# BROWSE
require_once __DIR__.'/lorekeeper/browse.php';

/**************************************************************************************************
    Routes that require login
**************************************************************************************************/
Route::group(['middleware' => ['auth', 'verified']], function() {

    # LINK DA ACCOUNT
    Route::get('/link', 'HomeController@getLink')->name('link');

    # BANNED
    Route::get('banned', 'Users\AccountController@getBanned');

    /**********************************************************************************************
        Routes that require having a linked dA account (also includes blocked routes when banned)
    **********************************************************************************************/
    Route::group(['middleware' => ['alias']], function() {

        require_once __DIR__.'/lorekeeper/members.php';

        /**********************************************************************************************
            Admin panel routes
        **********************************************************************************************/
        Route::group(['prefix' => 'admin', 'namespace' => 'Admin', 'middleware' => ['staff']], function() {

            require_once __DIR__.'/lorekeeper/admin.php';

        });
    });
});
