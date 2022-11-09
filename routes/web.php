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

Route::get('/', function () {
    return view('welcome');  
})->name('home');


Route::get('update_old_brands', function () {
	$brands = App\Models\Brand::withTrashed()->get();
	foreach ($brands as $value) {
		$brand =  App\Models\Brand::withTrashed()->find($value->id);
        if($brand->group_id == 0){
            $brandgroup = new App\BrandGroup();
            $brandgroup->subscriber_id = $brand->subscriber_id;
            $brandgroup->name = $brand->name;
            $brandgroup->website_url = $brand->website_url;
            $brandgroup->status = $brand->status;
            $brandgroup->logo = basename($brand->logo);
            $brandgroup->save();
    
            $amount = App\Models\TransactionHistory::where('admin_id',$brand->subscriber_id)->where('brand_id', $brand->id)->sum('amount');
            $amount_usd = App\Models\TransactionHistory::where('admin_id',$brand->subscriber_id)->where('brand_id', $brand->id)->sum('amount_usd');
    
            $brand->group_id = $brandgroup->id;
            $brand->spend_amount = $amount;
            $brand->spend_amount_usd = $amount_usd;
            $brand->save();

            if(!empty($brand->deleted_at)){
               $group = App\BrandGroup::find($brandgroup->id);
               $group->deleted_at = $brand->deleted_at;
               $group->save();
            }
        }
	}
	dd("done");
});



//Subscriber
Route::prefix('subscriber')->group(function () {
    Route::middleware(['guest:subscribers'])->group(function () {
        Route::get('/', [App\Http\Controllers\Backend\Subscriber\SubscriberLoginController::class, 'showLoginForm'])->name('subscriber.login');
        Route::get('login', [App\Http\Controllers\Backend\Subscriber\SubscriberLoginController::class, 'showLoginForm'])->name('subscriber.login');
        Route::post('login', [App\Http\Controllers\Backend\Subscriber\SubscriberLoginController::class, 'login']);
        Route::get('register', [App\Http\Controllers\Backend\Subscriber\SubscriberRegisterController::class, 'showRegisterForm'])->name('subscriber.register');
        /* resend verification email */
        Route::any('resend-verification-link',[App\Http\Controllers\Backend\Subscriber\SubscriberRegisterController::class, 'showResendverificationForm'])->name('subscriber.resendverification');
        
        Route::post('register', [App\Http\Controllers\Backend\Subscriber\SubscriberRegisterController::class, 'register']);
        Route::get('forget-password', [App\Http\Controllers\Backend\Subscriber\SubscriberForgotPasswordController::class, 'showForgetPasswordForm'])->name('subscriber.forget.password.get');
        Route::post('forget-password', [App\Http\Controllers\Backend\Subscriber\SubscriberForgotPasswordController::class, 'submitForgetPasswordForm'])->name('subscriber.forget.password.post');
        Route::get('reset-password/{token}', [App\Http\Controllers\Backend\Subscriber\SubscriberForgotPasswordController::class, 'showResetPasswordForm'])->name('subscriber.reset.password.get');
        Route::post('reset-password', [App\Http\Controllers\Backend\Subscriber\SubscriberForgotPasswordController::class, 'submitResetPasswordForm'])->name('subscriber.reset.password.post');
        Route::get('redirect/{driver}', [App\Http\Controllers\Backend\Subscriber\SubscriberLoginController::class, 'redirectToProvider'])->name('subscriber.redirect');
        Route::get('{driver}/callback', [App\Http\Controllers\Backend\Subscriber\SubscriberLoginController::class, 'handleProviderCallback'])->name('subscriber.callback');
      
    });
    Route::middleware(['auth:subscribers', 'is_verify_email', 'is_active'])->group(function () {
        Route::any('/currency/{id}', [App\Http\Controllers\Backend\Subscriber\DashboardController::class, 'showCurrencyForm'])->name('currency.form');
      
        Route::any('/terms_and_conditions', [App\Http\Controllers\Backend\Subscriber\DashboardController::class, 'termsandconditionsForm'])->name('terms_and_conditions.form');

        Route::any('/subscriber_welcome', [App\Http\Controllers\Backend\Subscriber\DashboardController::class, 'subscriberwelcome'])->name('subscriber.welcome');

        Route::middleware(['is_terms_and_conditions_accepted'])->group(function () {

        Route::post('logout', [App\Http\Controllers\Backend\Subscriber\SubscriberLoginController::class, 'logout'])->name('subscriber.logout');
        Route::get('dashboard', [App\Http\Controllers\Backend\Subscriber\DashboardController::class, 'index'])->name('subscriber.dashboard');
        Route::resource('brand', Backend\Subscriber\BrandController::class)->middleware(['is_information_filled']);
        Route::get('change-brand-status', [App\Http\Controllers\Backend\Subscriber\BrandController::class, 'changeBrandStatus'])->name('brand.change-status');

        Route::post('brandExits', [App\Http\Controllers\Backend\Subscriber\BrandController::class, 'brandExits'])->name('brandExits');
        Route::get('bran-group-status', [App\Http\Controllers\Backend\Subscriber\BrandController::class, 'changeBrandGroupStatus'])->name('brandgroup.change-status');
        Route::get('main-brand/{groupId}/edit', [App\Http\Controllers\Backend\Subscriber\BrandController::class, 'mainbrandedit'])->name('main-brand.edit');
        Route::put('main-brand/{groupId}/update', [App\Http\Controllers\Backend\Subscriber\BrandController::class, 'mainbrandupdate'])->name('main-brand.update');
        Route::delete('main-brand/{groupId}/delete', [App\Http\Controllers\Backend\Subscriber\BrandController::class, 'mainbranddelete'])->name('main-brand.delete');

        Route::get('get_country_list', [App\Http\Controllers\Backend\Subscriber\BrandController::class, 'getcountrylist'])->name('get_country_list');

        /* sinle collapse brand edit */
        Route::match(['GET', 'POST'], 'sinle-brand/{brandId}/edit',[App\Http\Controllers\Backend\Subscriber\BrandController::class, 'sinlecollapsebrandedit'])->name('sinlecollapsebrandedit');


        Route::get('setting/{id}', [App\Http\Controllers\Backend\Subscriber\SettingController::class, 'index'])->name('setting.index');
        Route::post('update-setting', [App\Http\Controllers\Backend\Subscriber\SettingController::class, 'update'])->name('setting.update');
        Route::get('profile', [App\Http\Controllers\Backend\Subscriber\ProfileController::class, 'profile'])->name('subscriber.profile');
        Route::get('pause-account/{id}', [App\Http\Controllers\Backend\Subscriber\ProfileController::class, 'pauseAccount'])->name('subscriber.pauseaccount');
        Route::get('unpause-account/{id}', [App\Http\Controllers\Backend\Subscriber\ProfileController::class, 'unpauseAccount'])->name('subscriber.unpauseaccount');
        Route::get('delete-account/{id}', [App\Http\Controllers\Backend\Subscriber\ProfileController::class, 'deleteAccount'])->name('subscriber.deleteaccount');
        Route::post('update-profile/{id}', [App\Http\Controllers\Backend\Subscriber\ProfileController::class, 'updateProfile'])->name('profile.update');

        Route::get('currencyget/{id}', [App\Http\Controllers\Backend\Subscriber\ProfileController::class, 'currencyget'])->name('currencyget');

        Route::resource('advertisement', Backend\Subscriber\AdvertisementController::class);

        // Topic Routes
        Route::get('topic/{brandId}', [App\Http\Controllers\Backend\Subscriber\TopicController::class, 'index'])->name('topic.index');
        Route::get('topic/create/{brandId}', [App\Http\Controllers\Backend\Subscriber\TopicController::class, 'create'])->name('topic.create');
        Route::get('topic/{topic}/{brandId}/edit', [App\Http\Controllers\Backend\Subscriber\TopicController::class, 'edit'])->name('topic.edit');
        Route::delete('topic/{id}', [App\Http\Controllers\Backend\Subscriber\TopicController::class, 'destroy'])->name('topic.destroy');
        Route::post('topic/{brandId}', [App\Http\Controllers\Backend\Subscriber\TopicController::class, 'store'])->name('topic.store');
        Route::post('update-topic/{id}', [App\Http\Controllers\Backend\Subscriber\TopicController::class, 'update'])->name('topic.update');
        Route::get('change-topic-status', [App\Http\Controllers\Backend\Subscriber\TopicController::class, 'changeTopicStatus'])->name('topic.change-status');

        // Message Routes
        Route::get('get-message-history/{id}', [App\Http\Controllers\Backend\Subscriber\TopicController::class, 'messageHistory'])->name('topic.message-history');
        Route::get('view-attachments/{id}', [App\Http\Controllers\Backend\Subscriber\TopicController::class, 'viewAttachment'])->name('topic.view-attachment');

        // Notification Topic Routes
        Route::get('notification-topic/{brandId}', [App\Http\Controllers\Backend\Subscriber\NotificationTopicController::class, 'index'])->name('notification-topic.index');
        Route::get('notification-topic/create/{brandId}', [App\Http\Controllers\Backend\Subscriber\NotificationTopicController::class, 'create'])->name('notification-topic.create');
        Route::get('notification-topic/{topic}/{brandId}/edit', [App\Http\Controllers\Backend\Subscriber\NotificationTopicController::class, 'edit'])->name('notification-topic.edit');
        Route::delete('notification-topic/{id}', [App\Http\Controllers\Backend\Subscriber\NotificationTopicController::class, 'destroy'])->name('notification-topic.destroy');
        Route::post('notification-topic/{brandId}', [App\Http\Controllers\Backend\Subscriber\NotificationTopicController::class, 'store'])->name('notification-topic.store');
        Route::post('update-notification-topic/{id}',[App\Http\Controllers\Backend\Subscriber\NotificationTopicController::class, 'update'])->name('notification-topic.update');
        Route::get('view-images/{id}', [App\Http\Controllers\Backend\Subscriber\NotificationTopicController::class, 'viewImages'])->name('notification-topic.view-images');
        Route::get('user-notification/{id}', [App\Http\Controllers\Backend\Subscriber\NotificationTopicController::class, 'userNotification'])->name('notification-topic.user-notification');
        Route::get('notification-topic-history/{id}/{brandId}/history', [App\Http\Controllers\Backend\Subscriber\NotificationTopicController::class, 'notificationTopicHistory'])->name('notification-topic.history');

        // Buy Routes
        Route::get('buy/{brandId}', [App\Http\Controllers\Backend\Subscriber\BuyController::class, 'index'])->name('buy.index');
        Route::get('buy/create/{brandId}', [App\Http\Controllers\Backend\Subscriber\BuyController::class, 'create'])->name('buy.create');
        Route::get('buy/{topic}/{brandId}/edit', [App\Http\Controllers\Backend\Subscriber\BuyController::class, 'edit'])->name('buy.edit');
        Route::delete('buy/{id}', [App\Http\Controllers\Backend\Subscriber\BuyController::class, 'destroy'])->name('buy.destroy');
        Route::post('buy/{brandId}', [App\Http\Controllers\Backend\Subscriber\BuyController::class, 'store'])->name('buy.store');
        Route::post('update-buy/{id}', [App\Http\Controllers\Backend\Subscriber\BuyController::class, 'update'])->name('buy.update');

        // Win Routes
        Route::get('win/{brandId}', [App\Http\Controllers\Backend\Subscriber\WinController::class, 'index'])->name('win.index');
        Route::get('win/create/{brandId}', [App\Http\Controllers\Backend\Subscriber\WinController::class, 'create'])->name('win.create');
        Route::get('win/{topic}/{brandId}/edit', [App\Http\Controllers\Backend\Subscriber\WinController::class, 'edit'])->name('win.edit');
        Route::delete('win/{id}', [App\Http\Controllers\Backend\Subscriber\WinController::class, 'destroy'])->name('win.destroy');
        Route::post('win/{brandId}', [App\Http\Controllers\Backend\Subscriber\WinController::class, 'store'])->name('win.store');
        Route::post('update-win/{id}', [App\Http\Controllers\Backend\Subscriber\WinController::class, 'update'])->name('win.update');

        // Poll Routes

        /* Poll Title index */
        Route::get('poll-title/{brandId}', [App\Http\Controllers\Backend\Subscriber\PollController::class, 'polltitleindex'])->name('polltitle.index');
        /* Poll Title create */
        Route::any('poll-create/{brandId}', [App\Http\Controllers\Backend\Subscriber\PollController::class, 'polltitlecreate'])->name('polltitle.create');
         /* Poll Title update */
         Route::any('poll-update/{brandId}', [App\Http\Controllers\Backend\Subscriber\PollController::class, 'polltitleupdate'])->name('polltitle.update');
        /* Delet main Poll Title */
        Route::post('poll-delete', [App\Http\Controllers\Backend\Subscriber\PollController::class, 'polltitledelete'])->name('polltitle.delete');


         /* new flow */
        /* poll create */
        Route::get('create-new-poll/{brandID}', [App\Http\Controllers\Backend\Subscriber\PollController::class, 'createNewPoll'])->name('create-New-Poll');

        /* poll total quantity */
        Route::post('poll-total-quantity', [App\Http\Controllers\Backend\Subscriber\PollController::class, 'pollquantity'])->name('polltotal.quantity');



        Route::get('poll/{brandId}', [App\Http\Controllers\Backend\Subscriber\PollController::class, 'index'])->name('poll.index');
        Route::get('poll/create/{pollID}', [App\Http\Controllers\Backend\Subscriber\PollController::class, 'create'])->name('poll.create');
        Route::get('poll/{topic}/{brandId}/edit', [App\Http\Controllers\Backend\Subscriber\PollController::class, 'edit'])->name('poll.edit');
        Route::delete('poll/{id}', [App\Http\Controllers\Backend\Subscriber\PollController::class, 'destroy'])->name('poll.destroy');
        Route::post('poll/{brandId?}', [App\Http\Controllers\Backend\Subscriber\PollController::class, 'store'])->name('poll.store');
        Route::post('update-poll/{id}', [App\Http\Controllers\Backend\Subscriber\PollController::class, 'update'])->name('poll.update');
        Route::get('change-poll-status', [App\Http\Controllers\Backend\Subscriber\PollController::class, 'changePollStatus'])->name('poll.change-status');
        Route::get('poll/{id}/history', [App\Http\Controllers\Backend\Subscriber\PollController::class, 'pollHistory'])->name('poll.history');
        Route::get('poll/{id}/result', [App\Http\Controllers\Backend\Subscriber\PollController::class, 'pollResult'])->name('poll.result');

        // Topup Routes
        Route::get('topup', [App\Http\Controllers\Backend\Subscriber\TopupController::class, 'index'])->name('topup.index');
        Route::get('topup/create/', [App\Http\Controllers\Backend\Subscriber\TopupController::class, 'create'])->name('topup.create');
        Route::post('topup', [App\Http\Controllers\Backend\Subscriber\TopupController::class, 'store'])->name('topup.store');

        Route::post('generateurl', [App\Http\Controllers\Backend\Subscriber\TopupController::class, 'generateurl'])->name('generateurl');
        Route::get('newtopupcreate', [App\Http\Controllers\Backend\Subscriber\TopupController::class, 'newtopupcreate'])->name('newtopupcreate');

        // Withdraw Routes
        Route::get('withdraw', [App\Http\Controllers\Backend\Subscriber\WithdrawController::class, 'index'])->name('withdraw.index');
        Route::get('withdraw/create/', [App\Http\Controllers\Backend\Subscriber\WithdrawController::class, 'create'])->name('withdraw.create');
        Route::post('withdraw', [App\Http\Controllers\Backend\Subscriber\WithdrawController::class, 'store'])->name('withdraw.store');
        });
       
    });
    Route::any('account/verify/{token}', [App\Http\Controllers\Backend\Subscriber\SubscriberRegisterController::class, 'verifyAccount'])->name('subscriber.verify');
});

// Super Admin
Route::prefix('admin')->group(function () {
    Route::middleware(['guest:superadmins'])->group(function () {
        Route::get('/', [App\Http\Controllers\Backend\Superadmin\SuperadminLoginController::class, 'showLoginForm'])->name('superadmin.login');
        Route::get('login', [App\Http\Controllers\Backend\Superadmin\SuperadminLoginController::class, 'showLoginForm'])->name('superadmin.login');
        Route::post('login', [App\Http\Controllers\Backend\Superadmin\SuperadminLoginController::class, 'login']);
    });

    Route::middleware(['auth:superadmins'])->group(function () {
        Route::post('logout', [App\Http\Controllers\Backend\Superadmin\SuperadminLoginController::class, 'logout'])->name('superadmin.logout');
        Route::get('dashboard', [App\Http\Controllers\Backend\Superadmin\DashboardController::class, 'index'])->name('superadmin.dashboard');
        Route::resource('subscriber', Backend\Superadmin\SubscriberController::class);
        Route::post('/subscriber-search', [App\Http\Controllers\Backend\Superadmin\SubscriberController::class, 'searchSubscriber'])->name('subscriber.search');
        Route::get('change-subscriber-status', [App\Http\Controllers\Backend\Superadmin\SubscriberController::class, 'changeSubscriberStatus'])->name('subscriber.change-status');
        Route::post('change-subscriber-password', [App\Http\Controllers\Backend\Superadmin\SubscriberController::class, 'changePassword'])->name('subscriber.change-password');
        Route::get('login-using-id/{id}', [App\Http\Controllers\Backend\Superadmin\SubscriberController::class, 'loginUsingId'])->name('subscriber.login-using-id');

        Route::get('price-setting', [App\Http\Controllers\Backend\Superadmin\PriceSettingController::class, 'index'])->name('price-setting.index');
        Route::post('price-setting', [App\Http\Controllers\Backend\Superadmin\PriceSettingController::class, 'store'])->name('price-setting.store');

        // Withdraw Routes
        Route::get('withdrawal-request', [App\Http\Controllers\Backend\Superadmin\WithdrawalController::class, 'index'])->name('withdrawal-request.index');
        Route::get('withdrawal-request/create/', [App\Http\Controllers\Backend\Superadmin\WithdrawalController::class, 'create'])->name('withdrawal-request.create');
        Route::post('withdrawal-request', [App\Http\Controllers\Backend\Superadmin\WithdrawalController::class, 'store'])->name('withdrawal-request.store');
        Route::get('withdrawal.change-status', [App\Http\Controllers\Backend\Superadmin\WithdrawalController::class, 'changeWithdrawalStatus'])->name('withdrawal.change-status');


        // Currency
        Route::resource('currency', Backend\Superadmin\CurrencyController::class);
        Route::any('/currency-rate', [App\Http\Controllers\Backend\Superadmin\CurrencyController::class, 'currencyRateEdit'])->name('currencyRateEdit');
        Route::get('/currency-api', [App\Http\Controllers\Backend\Superadmin\CurrencyController::class, 'currencyRateApi'])->name('currencyRateApi');

        /* user withdrawal controller */
        Route::resource('user_withdraw_requests', Backend\Superadmin\UserWithdrawalController::class);
        Route::get('user-withdrawal-change-status', [App\Http\Controllers\Backend\Superadmin\UserWithdrawalController::class, 'changeWithdrawalStatus'])->name('userwithdrawal.changestatus');
    });
});
Route::post('get-country-based-city', [App\Http\Controllers\Backend\CommonController::class, 'getCountryBasedCity'])->name('get-countryBased-city');

// API Forgot Password
Route::get('password/reset/{token}', 'Api\V1\AuthController@showResetPasswordForm')->name('password.reset');
Route::post('reset-password', 'Api\V1\AuthController@submitResetPasswordForm')->name('password.reset.post');

Route::get('change-password/{token}', 'Backend\Superadmin\SubscriberController@showChangePasswordForm')->name('password.change');
Route::post('change-password', 'Backend\Superadmin\SubscriberController@submitChangePasswordForm')->name('password.change.post');