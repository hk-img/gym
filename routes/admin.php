<?php

use App\Http\Controllers\Admin\AssignPlanController;
use App\Http\Controllers\Admin\AttendanceController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\DietPlanController;
use App\Http\Controllers\Admin\EquipmentController;
use App\Http\Controllers\Admin\PlanController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\TransactionController;
use App\Http\Controllers\Admin\TrainerController;
use App\Http\Controllers\Admin\ActivityController;
use App\Http\Controllers\Admin\VideoPTController;
use App\Http\Controllers\Admin\AssignPTController;
use App\Http\Controllers\Admin\GymController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\WorkoutController;
use App\Http\Controllers\OptionController;
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

    Route::post('/update-gym-hours', [ProfileController::class, 'updateGymHours'])->name('profile.updateGymHours');
    Route::post('/update-social-links', [ProfileController::class, 'updateSocialLinks'])->name('profile.updateSocialLinks');

    /** Roles */
    Route::resource('roles', RoleController::class);

    /** User */
    Route::resource('users', UserController::class);
    Route::get('users/change-status/{id}/{status}', [UserController::class,'changeStatus'])->name('users.changeStatus');
    Route::get('users/info/{id}', [UserController::class,'userInfo'])->name('users.info');
    Route::get('users/user-renewal-history/{id}', [UserController::class,'userRenewalHistory'])->name('users.userRenewalHistory');
    Route::get('users/user-pt-details/{id}', [UserController::class,'userPTDetail'])->name('users.userPTDetail');
    Route::get('users/user-package-details/{id}', [UserController::class,'userOackageDetail'])->name('users.userOackageDetail');
    Route::get('users/user-transactions-details/{id}', [UserController::class,'transactions'])->name('users.transactions');
    Route::post('users/user-pay', [UserController::class,'paymentUsers'])->name('users.pay');

    /** User */
    Route::get('transactions', [TransactionController::class, 'index'])->name('transactions.index');

    /** Trainers */
    Route::resource('trainers', TrainerController::class);
    
    /** video */
    Route::resource('video', VideoPTController::class);
    
    /** Assign Plan */
    Route::resource('assign-pt', AssignPTController::class);
    Route::get('trainer/info/{id}', [AssignPTController::class,'trainerInfo'])->name('trainer.info');

    /** Gym Manager */
    Route::resource('gym', GymController::class);
    Route::get('gym/change-status/{id}/{status}', [GymController::class,'changeStatus'])->name('gym.changeStatus');
    Route::get('gymList',[GymController::class,'gymlisting'])->name('gym.gymlist');
    
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
    Route::get('/admin/reports/pt-revenue', [ReportController::class, 'getMonthlyPTRevenue'])->name('reports.pt-revenue');
    Route::get('reports/membership-expired', [ReportController::class,'membershipExpired'])->name('reports.expired');
    Route::get('reports/personal-training', [ReportController::class,'personalTraining'])->name('reports.pt');

    /** Workout */
    Route::resource('workout', WorkoutController::class);

    /** category */
    Route::resource('category', CategoryController::class);
    
    /** Activity */
    Route::resource('activity', ActivityController::class);
    
    /** Assign Activity */
    Route::get('activity-assign', [ActivityController::class,'activityAssign'])->name('activity-assign');
    Route::get('activity-assign-list', [ActivityController::class,'assignList'])->name('activity-assign-list');
    Route::post('activity-assign-store', [ActivityController::class,'assignStore'])->name('assign-store');

    /** Diet Plan */
    Route::resource('diet-plan', DietPlanController::class);

    /** Attendance */
    Route::resource('attendance', AttendanceController::class);

    /** Equipment */
    Route::resource('equipment', EquipmentController::class);

    /** Send Notification */
    Route::get('send-notification', [NotificationController::class,'sendForm'])->name('notifications.form');
    Route::post('send-notification', [NotificationController::class,'sendNotification'])->name('notifications.send');

    Route::delete('admin/images/remove/{id}', [ImageController::class, 'destroy'])->name('images.destroy');    

    /** Option */
    Route::get('user-list', [OptionController::class,'userList'])->name('option.userlist');
    Route::get('plan-list', [OptionController::class,'planList'])->name('option.planlist');
    Route::get('trainers-list', [OptionController::class,'trainers'])->name('option.trainers');
    Route::get('category-list', [OptionController::class,'categoryList'])->name('option.categoryList');
    Route::get('package-list', [OptionController::class,'packagelist'])->name('option.packagelist');

    Route::get('usergetdata', [WorkoutController::class,'getdata'])->name('workout.getdata');
    Route::get('dietdata',[DietPlanController::class,'dietdata'])->name('diet.getdata');
});


require __DIR__.'/auth.php';