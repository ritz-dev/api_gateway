<?php

use App\Http\Controllers\ApiGatewayController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\APIs\StudentController;
use App\Http\Controllers\APIs\TeacherController;
use App\Http\Controllers\APIs\EmployeeController;
use App\Http\Controllers\APIs\PersonalController;



Route::get('/', function () {
    return view('welcome');
});

Route::post('/login',[AuthController::class,'login']);

Route::post('/register',[AuthController::class,'register']);

Route::middleware('auth:employee')->group(function(){

    Route::prefix('user-management')->group(function () {
        Route::any('{endpoint}', [ApiGatewayController::class, 'handleUserManagementService'])->where('endpoint', '.*');
    });

    Route::prefix('academic')->group(function () {
        Route::any('{endpoint}', [ApiGatewayController::class, 'handleAcademicService'])->where('endpoint', '.*');
    });

    Route::post('/logout',[AuthController::class,'logout']);

    Route::get('/me',[AuthController::class,'me']);

    Route::get('/validate-token', [AuthController::class,'validateToken']);

//     Route::resource('/users',EmployeeController::class);

//     Route::resource('/permissions',PermissionController::class);

//     Route::resource('/roles',RoleController::class);

//     Route::resource('/personals',PersonalController::class);

//     Route::resource('/employees',EmployeeController::class);

//     Route::resource('/students',StudentController::class);

//     Route::resource('/teachers',TeacherController::class);

});
