<?php

use App\Http\Controllers\Backend\ChangeLogController;
use App\Http\Controllers\Backend\CustomNotificationController;
use App\Http\Controllers\Backend\FeeController;
use App\Http\Controllers\Backend\IncomeSummaryController;
use App\Http\Controllers\Backend\SystemController;
use App\Http\Controllers\Backend\TransactionController;
use App\Http\Controllers\Backend\TripController;
use App\Http\Controllers\Backend\UserController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Auth::routes(['register' => false]);

Route::middleware(['auth', 'role:admin|staff'])->group(function () {
    Route::get('/', [HomeController::class, 'dashboard'])->name('dashboard');
    Route::resource('users', UserController::class);
    Route::get('/users/{id}/chart/{range}', [UserController::class, 'userChart'])->name('user.Chart');
    Route::get('/admin/profile', [UserController::class, 'adminProfile'])->name('admin.profile');
    Route::post('/admin/profile/{user}', [UserController::class, 'adminUpdate'])->name('admin.update');
    Route::get('/users-active', [UserController::class, 'activeUser'])->name('users.active');
    Route::put('/users-active/{user}', [UserController::class, 'turnActive'])->name('users.turn-active');
    Route::get('/users-pending', [UserController::class, 'pendingUser'])->name('users.pending');
    Route::put('/users-pending/{user}', [UserController::class, 'turnPending'])->name('users.turn-pending');
    Route::get('/users-role', [UserController::class, 'userManagement'])->middleware(['role:admin'])->name('users.role');
    Route::post('/users-role/{user}', [UserController::class, 'changeRole'])->middleware(['role:admin'])->name('users.role.change');
    Route::get('/users-search', [UserController::class, 'search'])->name('users.search');
    Route::get('/users-search/active', [UserController::class, 'searchForActive'])->name('users.search.active');
    Route::get('/users-search/pending', [UserController::class, 'searchForPending'])->name('users.search.pending');
    Route::get('/users-search/role', [UserController::class, 'searchForRole'])->name('users.search.role');
    Route::get('/users-sort', [UserController::class, 'usersSummaryPage'])->name('users.summary');
    Route::get('/users-search/sort', [UserController::class, 'searchForUsersSummary'])->name('users.summary.search');



    Route::resource('fee', FeeController::class)->only([
        'index', 'store', 'update', 'destroy'
    ]);
    Route::resource('trip', TripController::class)->only([
        'index', 'update', 'destroy'
    ]);

    Route::get('/trips/search', [TripController::class, 'search'])->name('trips.search');

    Route::get('/topup', [TransactionController::class, 'index'])->name('topup.index');
    Route::post('/topup', [TransactionController::class, 'topup'])->name('topup.add');
    Route::get('/topup/search', [TransactionController::class, 'search'])->name('topup.search');
    Route::get('/topup/history', [TransactionController::class, 'transactionHistory'])->name('topup.history');
    Route::get('/topup/history/search', [TransactionController::class, 'historySearch'])->name('topup.history.search');
    Route::get('/topup/notification', [TransactionController::class, 'notification'])->name('topup.notification');
    Route::get('/topup/notification/{id}', [TransactionController::class, 'notification_show'])->name('topup.notification.show');
    Route::post('/topup/notification/{notifiaction}', [TransactionController::class, 'topupDone'])->name('topup.done');
    Route::delete('/topup/notification/', [TransactionController::class, 'notificationDestroy'])->name('notifiaction.destroy');
    
        Route::get('/transactionsfix', [TransactionController::class, 'transactionFxi']);

    
    // Getters
    Route::get('/system/balance', [SystemController::class, 'getBalance'])->name('system.balence');
    Route::get('/system/normal-fee', [SystemController::class, 'getNormalFee'])->name('system.normalFee');
    Route::get('/system/initial-fee', [SystemController::class, 'getInitialFee'])->name('system.initialFee');
    Route::get('/system/waiting-fee', [SystemController::class, 'getWaitingFee'])->name('system.waitingFee');
    Route::get('/system/commission-fee', [SystemController::class, 'getCommissionFee'])->name('system.CommissionFee');

    // Updates
    Route::post('/system/balance', [SystemController::class, 'updateBalance'])->name('system.update.balence');
    Route::post('/system/normal-fee', [SystemController::class, 'updateNormalFee'])->name('system.update.normalFee');
    Route::post('/system/initial-fee', [SystemController::class, 'updateInitialFee'])->name('system.update.initialFee');
    Route::post('/system/waiting-fee', [SystemController::class, 'updateWaitingFee'])->name('system.update.waitingFee');
    Route::post('/system/commission-fee', [SystemController::class, 'updateCommissionFee'])->name('system.update.CommissionFee');

    Route::get('/commission-chart/{range}', [HomeController::class, 'commissionChat'])->name('income.chart');
    Route::get('/topup-chart/{range}', [HomeController::class, 'topupChat'])->name('topup.chart');
    Route::get('/trip-chart/{range}', [HomeController::class, 'tripChat'])->name('trip.chart');
    Route::get('/income-summary/{type}', [IncomeSummaryController::class, 'incomeSummary'])->name('income.summary');

    Route::get('/change-log', [ChangeLogController::class, 'index'])->name('changeLog');
    Route::post('/change-log', [ChangeLogController::class, 'store'])->name('changeLog.add');
    Route::put('/change-log', [ChangeLogController::class, 'updateAppLink'])->name('appLink.update');

    Route::get('/notification', [CustomNotificationController::class, 'index'])->name('notification.index');
    Route::post('/notification', [CustomNotificationController::class, 'sendCustomNotification'])->name('notification.send');
});
