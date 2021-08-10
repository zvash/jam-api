<?php

use App\Http\Controllers\Api\V1\DriverCountyController;
use App\Http\Controllers\Api\V1\DriverOrderController;
use App\Http\Controllers\Api\V1\ItemController;
use App\Http\Controllers\Api\V1\LocationController;
use App\Http\Controllers\Api\V1\OrderController;
use App\Http\Controllers\Api\V1\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::group(['prefix' => 'v1'], function ($router) {

    $router->group(['namespace' => 'Api\V1'], function ($router) {

        $router->group(['prefix' => 'users'], function ($router) {

//            $router->post('password/reset/email', [ForgotPasswordController::class, 'sendEmail'])->name('password.reset.email');
//            $router->get('password/reset/{token}', [ForgotPasswordController::class, 'resetForm'])->name('password.reset');
//            $router->post('password/verify-token', 'ForgotPasswordController@verifyToken')->name('password.verify-token');

            $router->post('/register', [UserController::class, 'register']);
            $router->post('/login', [UserController::class, 'login']);
            $router->group(['middleware' => 'throttle:1,2'], function ($router) {
                $router->post('/password/forgot/code', [UserController::class, 'sendForgotPasswordRecoveryCode']);
                $router->post('/password/forgot/reset', [UserController::class, 'resetPasswordUsingRecoveryCode']);
            });

//            $router->get('/verify/{user}', 'VerificationController@verify')->name('verification.verify');

        });

        $router->group(['middleware' => 'auth:api'], function ($router) {

            $router->group(['middleware' => 'throttle:5'], function ($router) {
                $router->post('/users/activate', [UserController::class, 'verifyPhone']);
            });
            $router->group(['middleware' => 'throttle:1,2'], function ($router) {
                $router->get('/users/activation/resend', [UserController::class, 'resendPhoneActivation']);
            });

            $router->group(['middleware' => 'phone_verified'], function ($router) {

                $router->group(['prefix' => 'users'], function ($router) {

                    $router->get('/authenticated', [UserController::class, 'me']);
                    $router->post('/password/change', [UserController::class, 'changePassword']);

                    $router->post('/profile/update', [UserController::class, 'update']);

                });

                $router->group(['prefix' => 'orders'], function ($router) {

                    $router->post('/register', [OrderController::class, 'store']);

                    $router->get('{order}/get', [OrderController::class, 'get']);

                    $router->get('states/{state}/get', [OrderController::class, 'getOrdersByLogicalState']);

                    $router->post('/{order}/cancel', [OrderController::class, 'cancel']);
                    $router->post('/{order}/accept', [OrderController::class, 'accept']);

                });

                $router->group(['prefix' => 'items'], function ($router) {

                    $router->get('/all', [ItemController::class, 'getItems']);

                });

                $router->group(['prefix' => 'locations'], function ($router) {

                    $router->get('states', [LocationController::class, 'getStates']);
                    $router->get('states/{state}/counties', [LocationController::class, 'getCounties']);

                    $router->post('/store', [LocationController::class, 'store']);
                    $router->post('/remove', [LocationController::class, 'destroy']);
                    $router->post('/{location}/update', [LocationController::class, 'update']);
                    $router->get('/all', [LocationController::class, 'getAllForUser']);
                    $router->get('/{location}', [LocationController::class, 'get']);
                    $router->post('/{location}/default', [LocationController::class, 'makeDefaultForUser']);
                    $router->post('/remove-default', [LocationController::class, 'unsetDefaultForUser']);


                });

            });

            $router->group(['middleware' => 'drivers'], function ($router) {

                $router->group(['prefix' => 'drivers'], function ($router) {
                    $router->post('/locations/reset', [DriverCountyController::class, 'resetDriverCounties']);

                    $router->get('/orders/suggestions', [DriverOrderController::class, 'suggestions']);
                    $router->post('/orders/accept', [DriverOrderController::class, 'acceptOrder']);
                    $router->post('/orders/moving', [DriverOrderController::class, 'announceMoving']);
                    $router->post('/orders/picked-up', [DriverOrderController::class, 'announcePickingUp']);
                    $router->post('/orders/delivered', [DriverOrderController::class, 'announceDelivered']);

                    $router->post('/orders/{order}/uploads/waybill', [DriverOrderController::class, 'uploadWaybill']);
                    $router->post('/orders/{order}/uploads/evacuation', [DriverOrderController::class, 'uploadEvacuationPermit']);
                });
            });

        });

    });

});