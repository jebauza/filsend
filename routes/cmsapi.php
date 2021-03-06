<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['ajax', 'auth'])->name('cmsapi.')->group(function () {

    /* AUTH */
    Route::prefix('auth')->name('auth.')->group(function () {
        Route::post('/login', 'CMS\Api\Auth\LoginCmsApiController@login')->name('login')->withoutMiddleware(['auth']);
        Route::get('/logout', 'CMS\Api\Auth\LoginCmsApiController@logout')->name('logout');
        Route::get('/get-refresh-auth-user', 'CMS\Api\Auth\LoginCmsApiController@refreshUserAuth')->name('get-refresh-auth-user');
    });

    /* ADMINISTRATION */
    Route::prefix('administration')->group(function () {

        /* USERS */
        Route::prefix('users')->name('user.')->group(function () {
            Route::get('/', 'CMS\Api\UserCmsApiController@index')->middleware('permission:users.index')->name('index');
            Route::post('/store', 'CMS\Api\UserCmsApiController@store')->middleware('permission:users.store')->name('store');
            Route::post('/{user_id}/update', 'CMS\Api\UserCmsApiController@update')->name('update');
            Route::put('/{user_id}/set-state', 'CMS\Api\UserCmsApiController@setState')->middleware('permission:users.activate|users.deactivate')->name('set-state');
            Route::get('/{user_id}/show', 'CMS\Api\UserCmsApiController@show')->name('show');
            Route::get('/{user_id}/get-permissions', 'CMS\Api\UserCmsApiController@getPermissions')->name('get-permissions');
        });

        /* PERMISSIONS */
        Route::prefix('permissions')->name('permissions.')->group(function () {
            Route::get('/', 'CMS\Api\PermissionCmsApiController@index')->name('index');
            Route::get('/get-all-permissions', 'CMS\Api\PermissionCmsApiController@getAllPermissions')->name('get-all-permissions');
            Route::get('/auth-user/get-all-permissions', 'CMS\Api\PermissionCmsApiController@authUserAllPermissions')->name('auth-user.get-all-permissions');
        });

        /* ROLES */
        Route::prefix('roles')->name('roles.')->group(function () {
            Route::get('/', 'CMS\Api\RoleCmsApiController@index')->middleware('permission:roles.index')->name('index');
            Route::get('/get-all-roles', 'CMS\Api\RoleCmsApiController@getAllRoles')->name('get-all-roles');
            Route::get('/{role_id}/permissions-by-role', 'CMS\Api\RoleCmsApiController@getPermissionsByRole')->name('get-permissions-by-role');
            Route::post('/store', 'CMS\Api\RoleCmsApiController@store')->middleware('permission:roles.store')->name('store');
            Route::put('/{role_id}/update', 'CMS\Api\RoleCmsApiController@update')->middleware('permission:roles.update')->name('update');
        });
    });

    /* Files */
    Route::prefix('files')->group(function () {

        Route::get('/', 'CMS\Api\FileCmsApiController@index')->name('index');
        Route::get('/{file_id}/download', 'CMS\Api\FileCmsApiController@download')->name('download');
        Route::get('/received', 'CMS\Api\SendingCmsApiController@received')->name('received');

        Route::prefix('sendings')->name('sendings.')->group(function () {
            Route::get('/', 'CMS\Api\SendingCmsApiController@index')->name('index');
            Route::get('/can-send-users', 'CMS\Api\SendingCmsApiController@can_send_users')->name('can_send_users');
            Route::get('/users-not-blocked', 'CMS\Api\SendingCmsApiController@users_not_blocked')->name('users_not_blocked');
            Route::post('/store', 'CMS\Api\SendingCmsApiController@store')->name('store');
            Route::get('/blocked-users', 'CMS\Api\SendingCmsApiController@blocked_users')->name('blocked_users');
            Route::post('/lock-unlock/user', 'CMS\Api\SendingCmsApiController@lock_unlock')->name('lock_unlock.user');
        });
    });

});

