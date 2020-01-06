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

/**/
Route::get('signup', 'ProfileController@register');
Route::get('verify_register_user', 'ProfileController@verify_register_user');
Route::post('guestuser/create', 'ProfileController@create_user');
/**/

Route::group(['middleware' => 'guest'], function() {

    Route::post('/register', 'RegistrationController@postRegister');
    Route::get('/login', 'LoginController@index');
    Route::post('/login', 'LoginController@postLogin');

});

Route::middleware(['auth'])->group(function() {

    Route::get('dashboard', 'DashboardController@index');
    Route::get('dashboard/{cell_id?}', 'DashboardController@index');
    Route::get('test_instagram', 'DashboardController@test_instagram');
    Route::get('callback_instagram', 'DashboardController@calbback_instagram');
    Route::get('profile', 'ProfileController@index');
    Route::get('messages/{user_id?}', 'MessageController@index');
    // Route::get('queues/{user_id?}', 'PostController@index');
    Route::get('queues/{cell_id?}', 'PostController@index');

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
    Route::get('fb_publish_post/{page_id?}/{post_id?}', 'PostController@fb_publish_post');
    Route::get('display_pages/{post_id?}/{user_id?}', 'PostController@display_pages');
    Route::get('fb_oauth_callback', 'PostController@fb_callback');
    Route::get('deauthorize_fb_app', 'PostController@deauthorize_fb_app');
    Route::get('destroy_session_fb_app', 'PostController@destroy_session_fb_app');
    /**/

    /* Twitter */
    Route::get('twitter_callback', 'SocialAccountController@twitter_callback');
    /* Twitter */

    /* Pinterest */
    Route::get('pinterest_callback', 'SocialAccountController@pinterest_callback');
    /* Pinterest */

    /*Google*/
    Route::get('/redirect_google', 'SocialAccountController@redirectToProvider');
    Route::get('/callback/google', 'SocialAccountController@handleProviderCallback');
    Route::get('/create-google-post', 'SocialAccountController@create_google_post');
    /*Google*/

    /* Social Cell */
    Route::get('socialcell/add', 'SocialCellController@add_social_cell');
    Route::get('socialcell', 'SocialCellController@index');
    Route::get('socialcell/{cell_id?}', 'SocialCellController@social_cell_accounts');
    Route::get('socialcell/status/{status_id?}', 'SocialCellController@index');
    Route::get('socialcell/date/{start_date?}/{end_date?}', 'SocialCellController@date_filter');
    Route::post('socialcell/store', 'SocialCellController@store');
    Route::get('socialcell/edit/{cell_id}', 'SocialCellController@edit');
    Route::post('socialcell/update/{cell_id}', 'SocialCellController@update');
    Route::get('socialcell/delete/{cell_id}', 'SocialCellController@delete');
    Route::post('socialcellaccount/add', 'SocialCellController@add_social_cell_account');
    Route::get('fb_cell_connect_app/{cell_id}', 'SocialCellController@fb_cell_connect_app');
    Route::get('fb_cell_callback', 'SocialCellController@fb_cell_callback');
    Route::get('socialcell/cancel_payment/{cell_id}', 'SocialCellController@cancel_payment');
    Route::get('socialcell/onhold_payment/{cell_id}', 'SocialCellController@onhold_payment');
    Route::get('socialcell/active_payment/{cell_id}', 'SocialCellController@active_payment');
    /* Social Cell */


    Route::prefix('post')->group(function() {
        // Route::get('add/{user_id?}', 'PostController@create');
        Route::get('add/{cell_id?}', 'PostController@create');
        Route::post('store', 'PostController@store');
        Route::get('edit/{post_id}', 'PostController@edit');
        Route::post('update/{post_id}', 'PostController@update');
        Route::get('delete/{post_id}', 'PostController@delete');
        /*Route::post('approve/{post_id}', 'PostController@approve');
        Route::post('decline/{post_id}', 'PostController@decline');
        Route::post('make_change/{post_id}', 'PostController@make_change');*/
        Route::get('approve/{post_id}', 'PostController@approve');
        Route::get('decline/{post_id}', 'PostController@decline');
        Route::get('make_change/{post_id}', 'PostController@make_change');
        Route::post('submit_make_change/{post_id}', 'PostController@submit_make_change');
    });
    Route::get('generate_payment/{cell_id}', 'SocialCellController@generate_payment');
    Route::post('create_payment/{cell_id}', 'SocialCellController@postPaymentStripe');
    // Route::get('run-cron', 'PostController@run_cron');

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

Route::get('run-cron', 'PostController@run_cron');

Route::get('/logout', 'LoginController@logout');