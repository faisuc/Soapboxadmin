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

Route::get('/', function () {
    return redirect('/login');
});

Route::group(['middleware' => 'guest'], function() {

    Route::post('/register', 'RegistrationController@postRegister');
    Route::get('/login', 'LoginController@index');
    Route::post('/login', 'LoginController@postLogin');

});

Route::middleware(['auth'])->group(function() {

    Route::get('dashboard', 'DashboardController@index');
    Route::get('profile', 'ProfileController@index');
    Route::get('messages/{user_id?}', 'MessageController@index');
    Route::get('queues/{user_id?}', 'PostController@index');

    Route::get('contentbank/{user_id?}', 'ContentBankController@index');
    Route::post('content/upload/image', 'ImageRepositoryController@store');
    Route::get('content/image/delete/{image_id}', 'ImageRepositoryController@delete');
    Route::post('content/text/add', 'TextRepositoryController@store');
    Route::get('content/text/delete/{image_id}', 'TextRepositoryController@delete');

    Route::get('socialaccounts/{user_id?}', 'SocialAccountController@index');
    Route::post('socialaccount/add', 'SocialAccountController@store');
    Route::get('socialaccount/delete/{social_id}', 'SocialAccountController@delete');

    /**/
    Route::get('fb_connect_app', 'SocialAccountController@fb_connect_app');
    Route::get('fb_callback', 'SocialAccountController@fb_callback');
    Route::get('fb_publish_post/{post_id?}', 'PostController@fb_publish_post');
    /**/

    Route::prefix('post')->group(function() {
        Route::get('add/{user_id?}', 'PostController@create');
        Route::post('store', 'PostController@store');
        Route::get('edit/{post_id}', 'PostController@edit');
        Route::post('update/{post_id}', 'PostController@update');
        Route::get('delete/{post_id}', 'PostController@delete');
    });

    Route::prefix('ajax')->group(function() {
        Route::post('add/post/notes', 'PostNotesController@store');
        Route::get('get/post/notes', 'PostNotesController@collection');
        Route::post('delete/post/notes', 'PostNotesController@delete');
    });

    Route::middleware('canView')->group(function() {
        Route::get('clients', 'ProfileController@myclients');
        Route::prefix('user')->group(function() {
            Route::get('{user_id}/delete/client/{client_id}', 'ClientController@delete')->middleware('userCanDeleteClient');
            Route::get('edit/{user_id}', 'ProfileController@edit')->middleware('userCanEditClient');
            Route::post('update/{user_id?}', 'ProfileController@update')->middleware('userCanEditClient');
            Route::get('create', 'ProfileController@create');
            Route::post('create', 'ProfileController@store');
        });
    });

    Route::prefix('profile')->group(function() {
        Route::post('update', 'ProfileController@update');
    });

    Route::middleware('admin')->group(function() {
        Route::prefix('user')->group(function() {
            Route::get('delete/{user_id}', 'ProfileController@delete');
            Route::get('clients/{user_id}', 'ClientController@create');
        });
    });

    Route::get('manageusers', 'ManageUsers@index')->middleware('admin');

});

Route::get('/logout', 'LoginController@logout');