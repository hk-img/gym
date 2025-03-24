<?php

use App\Http\Controllers\Admin\AssignPlanController;
use App\Http\Controllers\Admin\AttendanceController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\OptionController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\DietPlanController;
use App\Http\Controllers\Admin\PlanController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\GymController;
use App\Http\Controllers\Admin\WorkoutController;
use App\Http\Controllers\ImageController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('admin.login');
});

Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth', 'verified','verify_admin','revalidate'])->name('dashboard');

Route::middleware(['auth','verify_admin','revalidate','check_status'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    /** Roles */
    Route::resource('roles', RoleController::class);

    /** User */
    Route::resource('users', UserController::class);
    Route::get('users/change-status/{id}/{status}', [UserController::class,'changeStatus'])->name('users.changeStatus');
    Route::get('users/info/{id}', [UserController::class,'userInfo'])->name('users.info');
    Route::get('users/user-renewal-history/{id}', [UserController::class,'userRenewalHistory'])->name('users.userRenewalHistory');

    /**Gym Manager */
    Route::resource('gym', GymController::class);
    Route::get('gym/change-status/{id}/{status}', [GymController::class,'changeStatus'])->name('gym.changeStatus');
    
    /** Plan */
    Route::resource('plan', PlanController::class);
    Route::get('plan/change-status/{id}/{status}', [PlanController::class,'changeStatus'])->name('plan.changeStatus');
    Route::get('plan/info/{id}', [PlanController::class,'planInfo'])->name('plan.info');

    /** Assign Plan */
    Route::resource('assign-plan', AssignPlanController::class);
    Route::get('assign-plan/change-status/{id}/{status}', [AssignPlanController::class,'changeStatus'])->name('assign-plan.changeStatus');

    /** Reports */
    Route::get('reports/membership-renewals', [ReportController::class,'membershipRenewals'])->name('reports.renewals');
    Route::get('/admin/reports/revenue', [ReportController::class, 'getMonthlyRevenue'])->name('reports.revenue');
    Route::get('reports/membership-expired', [ReportController::class,'membershipExpired'])->name('reports.expired');

    /** Workout */
    Route::resource('workout', WorkoutController::class);

    /** Diet Plan */
    Route::resource('diet-plan', DietPlanController::class);

    /** Attendance */
    Route::resource('attendance', AttendanceController::class);

    Route::delete('admin/images/remove/{id}', [ImageController::class, 'destroy'])->name('images.destroy');    

    /** Option */
    Route::get('user-list', [OptionController::class,'userList'])->name('option.userlist');
    Route::get('plan-list', [OptionController::class,'planList'])->name('option.planlist');
    Route::get('brand-listt', [OptionController::class,'brandList'])->name('option.brandlist');
});


require __DIR__.'/auth.php';