<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\TeacherController;
use App\Http\Controllers\Api\CityController;
use App\Http\Controllers\Api\CourseTypeController;
use App\Http\Controllers\Api\CertificateController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\CourseController;
use App\Http\Controllers\Api\AddressController;
use App\Http\Controllers\Api\UserController;
use App\Http\Resources\UserResource;


Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout']);

    Route::get('/user', function (Request $request) {
        $user = $request->user()->load('role');

        return new UserResource($user);
    });

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
