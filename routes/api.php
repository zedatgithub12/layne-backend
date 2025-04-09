<?php


use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ColorController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\FrameController;
use App\Http\Controllers\FrameLensController;
use App\Http\Controllers\FrameShapeController;
use App\Http\Controllers\FrameSizeController;
use App\Http\Controllers\FramesShapeController;
use App\Http\Controllers\LensController;
use App\Http\Controllers\LensTypeController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\OrderItemController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\RolePermissionController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SizeController;
use App\Http\Controllers\TestimonialController;
use App\Http\Controllers\VariantController;
use App\Http\Controllers\VendorController;
use App\Http\Controllers\FrameColorController;
use App\Http\Controllers\WishListController;
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
    Route::patch('/{id}', [CategoryController::class, 'update']);
    Route::delete('/{id}', [CategoryController::class, 'destroy']);
});

Route::prefix('lens-types')->middleware(['auth:api'])->group(function () {
    Route::get('/', [LensTypeController::class, 'index']);
    Route::post('/', [LensTypeController::class, 'store']);
    Route::get('/{id}', [LensTypeController::class, 'show']);
    Route::patch('/{id}', [LensTypeController::class, 'update']);
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
    Route::patch('/{id}', [FrameShapeController::class, 'update']);
    Route::delete('/{id}', [FrameShapeController::class, 'destroy']);
});

Route::prefix('colors')->group(function () {
    Route::get('/', [ColorController::class, 'index']);
    Route::post('/', [ColorController::class, 'store']);
    Route::get('/{id}', [ColorController::class, 'show']);
    Route::patch('/{id}', [ColorController::class, 'update']);
    Route::delete('/{id}', [ColorController::class, 'destroy']);
});

Route::prefix('sizes')->middleware(['auth:api'])->group(function () {
    Route::get('/', [SizeController::class, 'index']);
    Route::post('/', [SizeController::class, 'store']);
    Route::get('/{id}', [SizeController::class, 'show']);
    Route::patch('/{id}', [SizeController::class, 'update']);
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

Route::prefix('frame-sizes')->middleware(['auth:api'])->group(function () {
    Route::get('/', [FrameSizeController::class, 'index']);
    Route::post('/', [FrameSizeController::class, 'store']);
    Route::get('/{id}', [FrameSizeController::class, 'show']);
    Route::put('/{id}', [FrameSizeController::class, 'update']);
    Route::delete('/{id}', [FrameSizeController::class, 'destroy']);
});

Route::prefix('products')->group(function () {
    Route::post('/', [ProductController::class, 'store']);
    Route::get('/', [ProductController::class, 'index']);
    Route::get('/{id}', [ProductController::class, 'show']);
    Route::patch('/{id}', [ProductController::class, 'update']);
    Route::put('/status/{id}', [ProductController::class, 'changeStatus']);
    Route::delete('/{id}', [ProductController::class, 'destroy']);
});

Route::prefix('variants')->group(function () {
    Route::post('/', [VariantController::class, 'store']);
    Route::get('/', [VariantController::class, 'index']);
    Route::get('/{id}', [VariantController::class, 'show']);
    Route::patch('/{id}', [VariantController::class, 'update']);
    Route::put('/status/{id}', [VariantController::class, 'changeStatus']);
    Route::delete('/{id}', [VariantController::class, 'destroy']);
});

Route::prefix('orders')->middleware(['auth:api'])->group(function () {
    Route::get('/', [OrderController::class, 'index']);
    Route::post('/', [OrderController::class, 'store']);
    Route::get('/{id}', [OrderController::class, 'show']);
    Route::put('/{id}', [OrderController::class, 'update']);
    Route::delete('/{id}', [OrderController::class, 'destroy']);
});

Route::prefix('order-items')->middleware(['auth:api'])->group(function () {
    Route::get('/', [OrderItemController::class, 'index']);
    Route::post('/', [OrderItemController::class, 'store']);
    Route::put('/{orderItem}', [OrderItemController::class, 'update']);
    Route::get('/{orderItem}', [OrderItemController::class, 'show']);
    Route::delete('/{orderItem}', [OrderItemController::class, 'destroy']);
});

Route::prefix('payments')->middleware(['auth:api'])->group(function () {
    Route::get('/', [PaymentController::class, 'index']);
    Route::post('/', [PaymentController::class, 'store']);
    Route::get('/{id}', [PaymentController::class, 'show']);
    Route::put('/{id}', [PaymentController::class, 'update']);
    Route::delete('/{id}', [PaymentController::class, 'destroy']);
});

Route::prefix('reviews')->middleware(['auth:api'])->group(function () {
    Route::get('/', [ReviewController::class, 'index']);
    Route::post('/', [ReviewController::class, 'store']);
    Route::get('/{id}', [ReviewController::class, 'show']);
    Route::put('/{id}', [ReviewController::class, 'update']);
    Route::delete('/{id}', [ReviewController::class, 'destroy']);
});

Route::prefix('testimonials')->middleware(['auth:api'])->group(function () {
    Route::get('/', [TestimonialController::class, 'index']);
    Route::post('/', [TestimonialController::class, 'store']);
    Route::get('/{id}', [TestimonialController::class, 'show']);
    Route::put('/{id}', [TestimonialController::class, 'update']);
    Route::delete('/{id}', [TestimonialController::class, 'destroy']);
});

Route::prefix('wish-list')->middleware(['auth:api'])->group(function () {
    Route::get('/', [WishListController::class, 'index']);
    Route::post('/', [WishListController::class, 'store']);
    Route::get('/{id}', [WishListController::class, 'show']);
    Route::delete('/{id}', [WishListController::class, 'destroy']);
});