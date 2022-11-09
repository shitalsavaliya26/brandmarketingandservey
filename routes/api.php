<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\CustomerWithdrawalController;


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

Route::group(['namespace' => 'Api\V1'], function () {
    Route::get('get-country', 'CountryController@getCountry');
    Route::post('get-city', 'CountryController@getCity');

    // Auth Routes
    Route::post('login','AuthController@login');
    Route::post('forgot-password', 'AuthController@forgotPassword');
    Route::post('register', 'AuthController@register');
    Route::post('isEmailExists', 'AuthController@isEmailExists');
    Route::post('isPhoneExists', 'AuthController@isPhoneExists');
    Route::post('resendotp', 'AuthController@resendOtp');
    Route::post('isAppleIdExists', 'AuthController@isAppleIdExists');
    /* verifyOtp */
    Route::post('verifyotp', 'AuthController@verifyOtp');
});


Route::group(['namespace' => 'Api\V1', 'middleware' => ['jwt.verify']], function() {

    Route::get('logout', 'AuthController@logout');
    Route::get('get-profile', 'ProfileController@getProfile');
    Route::get('remove-profile-image', 'ProfileController@removeProfileimage');
    Route::post('updateProfile', 'ProfileController@updateProfile');
    Route::post('seachBrand', 'BrandController@seachBrand');
    Route::post('brandDetail', 'BrandController@brandDetail');
    Route::get('delete-account', 'ProfileController@deleteaccount');
    Route::post('changepassword', 'ProfileController@changepassword');

    /* message screen apis */
    Route::post('messageTopics', 'BrandController@messageTopics');
    Route::post('sendMessage', 'BrandController@sendMessage');

    /* advertisement */
    Route::post('getAdvertisement', 'BrandController@getAdvertisement');

    /* notification topics */
    Route::post('notificationTopics', 'BrandController@notificationTopics');
    Route::post('addNotificationTopic', 'BrandController@addNotificationTopic');
    Route::get('getUserNotificationTopics', 'BrandController@getUserNotificationTopics');
    Route::get('readUserNotificationTopics', 'BrandController@readUserNotificationTopics');
    Route::post('stopNotification', 'BrandController@stopNotification');
    Route::get('getUserUnreadNotifications', 'BrandController@getUserUnreadNotifications');

    /* buy abd win */
    Route::post('getBuyLinks', 'BrandController@getBuyLinks');
    Route::post('getWin', 'BrandController@getWin');
    Route::post('visitWebsite', 'BrandController@visitWebsite');

    /* poll */
    Route::post('getPolls', 'BrandController@getPolls');
    Route::post('sendPollAnswer', 'BrandController@sendPollAnswer');
    Route::post('getPollResult', 'BrandController@getPollResult');

    /* get poll history */
    Route::get('user-poll-histories', 'BrandController@getuserpollshistories');

    /* get user available poll brands */
    Route::get('available-poll-brands', 'BrandController@getavailablepollbrands');

    /* withdrawal request */
    Route::post('addwithdrawalrequest', 'CustomerWithdrawalController@addwithdrawalrequest');
    Route::get('withdrawal-request', 'CustomerWithdrawalController@customerwithdrawalrequest');
  
});
