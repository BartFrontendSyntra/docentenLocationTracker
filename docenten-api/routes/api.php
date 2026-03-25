<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\TeacherController;
use App\Http\Controllers\Api\CityController;
use App\Http\Controllers\Api\CourseTypeController;
use App\Http\Controllers\Api\CertificateController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\CourseController;
use App\Http\Controllers\Api\AddressController;
use App\Http\Controllers\Api\UserController;


Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout']);


    Route::apiResources([
        'teachers' => TeacherController::class,
        'cities' => CityController::class,
        'course-types' => CourseTypeController::class,
        'certificates' => CertificateController::class,
        'roles' => RoleController::class,
        'courses' => CourseController::class,
        'addresses' => AddressController::class,
        'users' => UserController::class,
    ]);
});
