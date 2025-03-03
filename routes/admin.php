<?php

use App\Http\Controllers\Admin\AssignPlanController;
use App\Http\Controllers\Admin\BrandController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\OptionController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\ImageController;
use Illuminate\Support\Facades\Route;
use PhpParser\Node\Expr\Assign;

Route::get('/', function () {
    return redirect()->route('admin.login');
});

Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth', 'verified','verify_admin','revalidate'])->name('dashboard');

Route::middleware(['auth','verify_admin','revalidate'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    /** Roles */
    Route::resource('roles', RoleController::class);

    /** User */
    Route::resource('users', UserController::class);
    Route::get('users/change-status/{id}/{status}', [UserController::class,'changeStatus'])->name('users.changeStatus');
    Route::get('users/info/{id}', [UserController::class,'userInfo'])->name('users.info');

    /** Assign Plan */
    Route::resource('assign-plan', AssignPlanController::class);
    Route::get('assign-plan/change-status/{id}/{status}', [AssignPlanController::class,'changeStatus'])->name('assign-plan.changeStatus');


    /** Brands */
    Route::resource('brands', BrandController::class);
    Route::get('brands/change-status/{id}/{status}', [BrandController::class,'changeStatus'])->name('brands.changeStatus');
    Route::get('brands/change-popular-status/{id}/{status}', [BrandController::class,'changePopularStatus'])->name('brands.changePopularStatus');
    Route::get('brand-list/{typeId?}', [BrandController::class,'brandList'])->name('brand.list');
    
    Route::delete('admin/images/remove/{id}', [ImageController::class, 'destroy'])->name('images.destroy');    
});

/** Option */
Route::get('user-list', [OptionController::class,'userList'])->name('option.userlist');
Route::get('plan-list', [OptionController::class,'planList'])->name('option.planlist');
Route::get('brand-listt', [OptionController::class,'brandList'])->name('option.brandlist');

require __DIR__.'/auth.php';