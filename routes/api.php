<?php

use App\Enums\RoleEnum;
use App\Http\Controllers\API\AdminController;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\GuestController;
use App\Http\Controllers\API\ResidentController;
use App\Http\Controllers\API\SuperAdminController;
use App\Mail\TestMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;


Route::group(['prefix' => 'v1'], function() {

    Route::group(['prefix' => 'auth'], function() {
        Route::post('/resident/login', [AuthController::class, 'residentLogin'])->middleware('tenant');
        Route::post('/admin/login', [AuthController::class, 'adminLogin'])->middleware('tenant');
        Route::post('/super-admin/login', [AuthController::class, 'superAdminLogin']);

        Route::post('/email/verify', [AuthController::class, 'residentVerifyEmail'])->middleware('tenant');
    });

    Route::group(['middleware' => ['tenant']], function() {

        Route::group(['prefix' => 'guest', 'middleware' => ['guest']], function() {
            Route::post('/invite/accept', [GuestController::class, 'acceptInvite']);
            Route::post('/exit-estate', [GuestController::class, 'exitEstate']);
            Route::post('/current/location/save', [GuestController::class, 'saveCurrentLocation']);
            Route::post('/message/save', [GuestController::class, 'saveMessage']);
            Route::get('/message/fetch', [ResidentController::class, 'fetchMessages']);
        });

        Route::group(['prefix' => 'resident', 'middleware' => ['auth:sanctum', 'resident']], function() {

            Route::get('/profile/fetch', [ResidentController::class, 'profile']);
            Route::post('/avatar/update', [ResidentController::class, 'updateAvatar']);
            Route::get('/locations/list', [ResidentController::class, 'listLocations']);

            Route::prefix('guests')->group(function() {
                Route::post('/invite', [ResidentController::class, 'inviteGuest']);
                Route::post('/code/status/update', [ResidentController::class, 'updateGuestCodeStatus']);
                Route::patch('/{guest}/update-valid-date-and-time', [ResidentController::class, 'updateGuestValidDateAndTime']);
                Route::delete('/{guest}/remove', [ResidentController::class, 'removeGuest']);

                Route::get('/list', [ResidentController::class, 'listGuests']);
                Route::get('/location/history', [ResidentController::class, 'listGuestsLocationHistory']);
                Route::get('/{guest}/location/history', [ResidentController::class, 'listGuestLocationHistory']);
                Route::get('/cards/details', [GuestController::class, 'totalActiveCodesAndGuests']);
            });

            Route::prefix('messages')->group(function() {
                Route::post('/save', [ResidentController::class, 'saveMessage']);
                Route::get('/fetch', [ResidentController::class, 'fetchMessages']);
            });

            Route::prefix('notifications')->group(function() {
                Route::post('/settings', [ResidentController::class, 'updateNotificationSettings']);
                Route::get('/settings/fetch', [ResidentController::class, 'fetchNotificationSettings']);
                Route::get('/fetch', [ResidentController::class, 'getNotifications']);
                Route::post('/{notification}/read', [ResidentController::class, 'readNotification']);
                Route::post('/message/store', [ResidentController::class, 'storeMessageNotification'])->withoutMiddleware('auth:sanctum');
            });

        });

        Route::group(['prefix' => 'admin'], function() {

            Route::group(['middleware' => ['auth:sanctum', 'admin']], function() {

                Route::prefix('estate-locations')->group(function() {
                    Route::post('/', [AdminController::class, 'createEstateLocation']);
                    Route::get('/', [AdminController::class, 'listEstateLocation']);
                });

                Route::prefix('residents')->group(function() {
                    Route::get('/list', [AdminController::class, 'listResidents']);
                    Route::get('/{resident}/qr/scan', [AdminController::class, 'fetchResidentByQR']);
                    Route::post('/create', [AdminController::class, 'createResident']);
                    Route::post('/assign-to-location', [AdminController::class, 'assignResidentLocation']);
                    Route::delete('/{resident}/remove', [AdminController::class, 'removeResident']);
                    Route::post('/status/update', [AdminController::class, 'updateResidentStatus']);
                });

                Route::prefix('guests')->group(function() {
                    Route::post('/fetch-with-code', [AdminController::class, 'getGuestDetailsWithCode']);
                    Route::post('/fetch-with-qr', [AdminController::class, 'getGuestByQRCode']);
                    Route::post('/check-in-or-out', [AdminController::class, 'guestCheckInOrOut']);
                    Route::post('/entry-permission/update', [AdminController::class, 'updateGuestEntryPermission']);
                    Route::get('/location/history/{guest}', [AdminController::class, 'getGuestLocationHistory']);
                    Route::get('/cards/details', [AdminController::class, 'getTotalActiveCodesAndGuests']);
                    Route::get('/', [AdminController::class, 'listGuests']);
                    Route::get('location/{estate}/guests', [AdminController::class, 'getGuestsByLocation']);
                    Route::get('recent/activities', [AdminController::class, 'recentActivities']);
                });
            });
        });

    });

    Route::group(['prefix' => 'super-admin'], function() {

        Route::group(['middleware' => ['auth:sanctum', 'super-admin']], function() {
            Route::get('/list/admins', [SuperAdminController::class, 'listAdmins']);
            Route::post('/create/admin', [SuperAdminController::class, 'createAdmin']);
            Route::delete('/remove/{admin}/admin', [SuperAdminController::class, 'removeAdmin']);

            Route::prefix('estates')->group(function() {
                Route::get('/list', [SuperAdminController::class, 'listEstates']);
                Route::post('/create', [SuperAdminController::class, 'createEstate']);
                Route::delete('/remove/{estate}', [SuperAdminController::class, 'removeEstate']);
            });
        });

    });
});
