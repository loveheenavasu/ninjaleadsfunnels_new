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

$to = '/connections';

Route::redirect('/', $to);

Route::middleware(['auth:sanctum', 'verified'])->group(function () use ($to) {
    Route::redirect('/dashboard', $to)->name('dashboard');

    Route::get('test/generate-emails', [\App\Http\Controllers\TestController::class, 'generateEmails']);
    Route::resource('connections', \App\Http\Controllers\ConnectionController::class)->only(['index', 'create', 'show']);
    Route::resource('listings', \App\Http\Controllers\ListingController::class)->only(['index', 'create', 'show']);
    Route::get('notes', [\App\Http\Controllers\ListingController::class, 'savenotesvalue']);
    Route::get('refresh-mautic', [\App\Http\Controllers\RuleController::class, 'mauticStages']);
    Route::get('notes', [\App\Http\Controllers\ListingController::class, 'savenotesvalue']);
    Route::resource('rules', \App\Http\Controllers\RuleController::class)->only(['index', 'create', 'show']);
    Route::get('emailSyncedList', [\App\Http\Controllers\RuleController::class, 'emailSyncedList']);
    Route::resource('invalidemail', \App\Http\Controllers\InvalidEmailController::class)->only(['index']);
    //Mautic logs

    Route::resource('mauticlogs', \App\Http\Controllers\MauticLogsController::class)->only(['index']);

    //invalid
    Route::resource('emaillogs', \App\Http\Controllers\EmailLogsController::class)->only(['index']);

    Route::post('emailfilter', [\App\Http\Livewire\EmailLogsList::class, 'getEmaillogsProperty']);
    Route::resource('templates', \App\Http\Controllers\TemplateController::class)->only(['index', 'create', 'show', 'edit']);
    
    Route::get('DeleteEmailLogs', [\App\Http\Controllers\EmailLogsController::class, 'DeleteEmailLogs']);
    Route::get('DeleteEmailLogs_invalid_email', [\App\Http\Controllers\EmailLogsController::class, 'DeleteEmailLogs_invalid_email']);

    Route::get('SetLogsDeleteCron', [\App\Http\Controllers\EmailLogsController::class, 'SetLogsDeleteCron']);
    Route::get('Delete-logs-manaully', [\App\Http\Controllers\EmailLogsController::class, 'DeletelogsManaully']);
    Route::resource('cron', \App\Http\Controllers\CronController::class)->only(['index']);
    Route::get('SetLogsResetCron',[\App\Http\Controllers\CronController::class, 'SetLogsResetCron']);

    //User Management
    Route::resource('users', \App\Http\Controllers\UserController::class)->only(['index', 'create', 'show','edit']);
    Route::post('changepassword', [\App\Http\Controllers\UserController::class, 'update_password']);
    Route::resource('globalsetting', \App\Http\Controllers\GlobalSettingController::class)->only(['index', 'create', 'show', 'edit']);
    Route::resource('useremailsetting', \App\Http\Controllers\UserEmailSettingController::class)->only(['index', 'create', 'show', 'edit']);
    
  
});
