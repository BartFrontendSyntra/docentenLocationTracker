<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AddressController;
use App\Http\Controllers\Api\CertificateController;
use App\Http\Controllers\Api\CityController;
use App\Http\Controllers\Api\CourseController;
use App\Http\Controllers\Api\CourseTypeController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\TeacherController;
use App\Http\Controllers\Api\UserController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::apiResources([
    'addresses' => AddressController::class,
    'certificates' => CertificateController::class,
    'cities' => CityController::class,
    'courses' => CourseController::class,
    'course-types' => CourseTypeController::class,
    'teachers' => TeacherController::class,
    'users' => UserController::class,
]);

Route::get('/roles', [RoleController::class, 'index']);
