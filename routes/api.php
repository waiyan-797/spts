<?php


use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\FeeController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\SystemController;
use App\Http\Controllers\Api\TransactionController;
use App\Http\Controllers\Api\TripController;
use App\Http\Controllers\Backend\ChangeLogController;
use App\Http\Controllers\Backend\CustomNotificationController;
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

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/sign-up', [AuthController::class, 'register']);
Route::get('/app-link', [ChangeLogController::class, 'appLink']);
Route::middleware(['auth:sanctum'])->group(function () {
    // our routes to be protected will go in here
    Route::post('/logout', [AuthController::class, 'logout']);

    //routes for user
    Route::get('/user/profile', [UserController::class, 'profile']);
    Route::post('/user/update', [UserController::class, 'update']);
    Route::delete('/user/{user}', [UserController::class, 'destroy']);
    Route::get('user/trip', [UserController::class, 'usertrip']);
    Route::get('user/transactions', [UserController::class, 'userNoti']);

    //routes for fee
    Route::get('/fee', [FeeController::class, 'index']);

    //routes for trip
    Route::apiResource('trip', TripController::class, array("as" => "api"));
    Route::post('/trip/start', [TripController::class, 'tripStart']);
    //routes for transactions
    Route::get('/user/transactions', [TransactionController::class, 'index']);
    Route::get('/transactions/{id}', [TransactionController::class, 'show']);
    Route::get('/notification', [NotificationController::class, 'index'])->name('api.notification');
    Route::post('/notification', [NotificationController::class, 'store']);

    //routes for balance,initial-fee,commission-fee
    Route::get('/initial-fee', [SystemController::class, 'getInitialFee']);
    Route::get('/normal-fee', [SystemController::class, 'getNormalFee']);
    Route::get('/commission-fee', [SystemController::class, 'getCommissionFee']);
    Route::get('/waiting-fee', [SystemController::class, 'getWaitingFee']);

    Route::post('/store-token', [CustomNotificationController::class, 'storeToken']);
    Route::get('/custom-notification', [NotificationController::class, 'custom_notification']);
});
