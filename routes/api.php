<?php


use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ColorController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\FrameController;
use App\Http\Controllers\FrameLensController;
use App\Http\Controllers\FrameShapeController;
use App\Http\Controllers\FramesShapeController;
use App\Http\Controllers\LensController;
use App\Http\Controllers\LensTypeController;
use App\Http\Controllers\RolePermissionController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SizeController;
use App\Http\Controllers\VendorController;
use App\Http\Controllers\FrameColorController;
use Illuminate\Support\Facades\Route;


Route::get("/all-users", [AuthController::class, 'getAllUsers']);
Route::post('/user/create', [AuthController::class, 'createAccount']);
Route::post('/login', [AuthController::class, 'login']);

Route::prefix('/roles')->middleware(['auth:api'])->group(function () {
    Route::post('/create', [RolePermissionController::class, 'createRole']);
    Route::put('/update/{id}', [RolePermissionController::class, 'updateRole']);
    Route::delete('/delete/{id}', [RolePermissionController::class, 'deleteRole']);
});

Route::get('/roles', [RolePermissionController::class, 'getRoles']);
Route::get('/permissions', [RolePermissionController::class, 'getPermissions']);

Route::middleware(['auth:api'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/profile', [AuthController::class, 'profile']);
});

Route::prefix('vendors')->middleware(['auth:api'])->group(function () {
    Route::get('/', [VendorController::class, 'index']);
    Route::post('/', [VendorController::class, 'store']);
    Route::get('/{id}', [VendorController::class, 'show']);
    Route::put('/{id}', [VendorController::class, 'update']);
    Route::delete('/{id}', [VendorController::class, 'destroy']);
});

Route::prefix('customers')->middleware(['auth:api'])->group(function () {
    Route::get('/', [CustomerController::class, 'index']);
    Route::post('/', [CustomerController::class, 'store']);
    Route::get('/{id}', [CustomerController::class, 'show']);
    Route::put('/{id}', [CustomerController::class, 'update']);
    Route::delete('/{id}', [CustomerController::class, 'destroy']);
});

Route::prefix('categories')->middleware(['auth:api'])->group(function () {
    Route::get('/', [CategoryController::class, 'index']);
    Route::post('/', [CategoryController::class, 'store']);
    Route::get('/{id}', [CategoryController::class, 'show']);
    Route::put('/{id}', [CategoryController::class, 'update']);
    Route::delete('/{id}', [CategoryController::class, 'destroy']);
});

Route::prefix('lens-types')->middleware(['auth:api'])->group(function () {
    Route::get('/', [LensTypeController::class, 'index']);
    Route::post('/', [LensTypeController::class, 'store']);
    Route::get('/{id}', [LensTypeController::class, 'show']);
    Route::put('/{id}', [LensTypeController::class, 'update']);
    Route::delete('/{id}', [LensTypeController::class, 'destroy']);
});


Route::prefix('lenses')->middleware(['auth:api'])->group(function () {
    Route::get('/', [LensController::class, 'index']);
    Route::post('/', [LensController::class, 'store']);
    Route::get('/{id}', [LensController::class, 'show']);
    Route::put('/{id}', [LensController::class, 'update']);
    Route::delete('/{id}', [LensController::class, 'destroy']);
});

Route::prefix('frame-shapes')->middleware(['auth:api'])->group(function () {
    Route::get('/', [FrameShapeController::class, 'index']);
    Route::post('/', [FrameShapeController::class, 'store']);
    Route::get('/{id}', [FrameShapeController::class, 'show']);
    Route::put('/{id}', [FrameShapeController::class, 'update']);
    Route::delete('/{id}', [FrameShapeController::class, 'destroy']);
});

Route::prefix('colors')->group(function () {
    Route::get('/', [ColorController::class, 'index']);
    Route::post('/', [ColorController::class, 'store']);
    Route::get('/{id}', [ColorController::class, 'show']);
    Route::put('/{id}', [ColorController::class, 'update']);
    Route::delete('/{id}', [ColorController::class, 'destroy']);
});

Route::prefix('sizes')->middleware(['auth:api'])->group(function () {
    Route::get('/', [SizeController::class, 'index']);
    Route::post('/', [SizeController::class, 'store']);
    Route::get('/{id}', [SizeController::class, 'show']);
    Route::put('/{id}', [SizeController::class, 'update']);
    Route::delete('/{id}', [SizeController::class, 'destroy']);
});

Route::prefix('frames')->middleware(['auth:api'])->group(function () {
    Route::get('/', [FrameController::class, 'index']);
    Route::post('/', [FrameController::class, 'store']);
    Route::get('/{id}', [FrameController::class, 'show']);
    Route::put('/{id}', [FrameController::class, 'update']);
    Route::delete('/{id}', [FrameController::class, 'destroy']);
});

Route::prefix('frame-lenses')->middleware(['auth:api'])->group(function () {
    Route::post('/', [FrameLensController::class, 'store']);
    Route::get('/', [FrameLensController::class, 'index']);
    Route::get('{id}', [FrameLensController::class, 'show']);
    Route::put('{id}', [FrameLensController::class, 'update']);
    Route::delete('{id}', [FrameLensController::class, 'destroy']);
});

Route::prefix('frames-shapes')->middleware(['auth:api'])->group(function () {
    Route::get('/', [FramesShapeController::class, 'index']);
    Route::post('/', [FramesShapeController::class, 'store']);
    Route::get('/{id}', [FramesShapeController::class, 'show']);
    Route::put('/{id}', [FramesShapeController::class, 'update']);
    Route::delete('/{id}', [FramesShapeController::class, 'destroy']);
});

Route::prefix('frame-colors')->middleware(['auth:api'])->group(function () {
    Route::get('/', [FrameColorController::class, 'index']);
    Route::post('/', [FrameColorController::class, 'store']);
    Route::get('{id}', [FrameColorController::class, 'show']);
    Route::put('{id}', [FrameColorController::class, 'update']);
    Route::delete('{id}', [FrameColorController::class, 'destroy']);
});