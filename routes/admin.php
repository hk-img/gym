<?php

use App\Http\Controllers\Admin\AttributeController;
use App\Http\Controllers\Admin\BrandController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\TypeController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\VehicleController;
use App\Http\Controllers\Admin\VehicleTypeController;
use App\Http\Controllers\OptionController;
use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\BlogController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ExShowroomPriceController;
use App\Http\Controllers\Admin\HelpController;
use App\Http\Controllers\Admin\PlanController;
use App\Http\Controllers\Admin\SendMailController;
use App\Http\Controllers\Admin\SiteSettingController;
use App\Http\Controllers\Admin\TagController;
use App\Http\Controllers\Admin\TestimonialController;
use App\Http\Controllers\Admin\UsedVehicleController;
use App\Http\Controllers\Admin\VendorController;
use App\Http\Controllers\ImageController;
use Illuminate\Support\Facades\Route;

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
    
    /** Type */
    Route::resource('type', TypeController::class);
    Route::get('type/change-status/{id}/{status}', [TypeController::class,'changeStatus'])->name('type.changeStatus');
    Route::get('type-list', [TypeController::class,'typeList'])->name('type.list');

    /** Brands */
    Route::resource('brands', BrandController::class);
    Route::get('brands/change-status/{id}/{status}', [BrandController::class,'changeStatus'])->name('brands.changeStatus');
    Route::get('brands/change-popular-status/{id}/{status}', [BrandController::class,'changePopularStatus'])->name('brands.changePopularStatus');
    Route::get('brand-list/{typeId?}', [BrandController::class,'brandList'])->name('brand.list');
    
    /** Vehicle Type */
    Route::resource('vehicle-type', VehicleTypeController::class);
    Route::get('vehicle-type/change-status/{id}/{status}', [VehicleTypeController::class,'changeStatus'])->name('vehicle-type.changeStatus');
    Route::get('vehicle-type-list/{typeId?}', [VehicleTypeController::class,'vehicleTypeList'])->name('vehicle-type.list');

    /** Vehicle */
    Route::resource('vehicle', vehicleController::class);
    Route::get('vehicle/change-status/{id}/{status}', [VehicleController::class,'changeStatus'])->name('vehicle.changeStatus');
    Route::post('update-popular-status/', [VehicleController::class,'isPopular'])->name('isPopular');
    
    /** Vehicle Variant */
    Route::get('vehicle/variant/{id}', [VehicleController::class,'variantList'])->name('vehicle.variantList');
    Route::get('vehicle/create-variant/{id}', [VehicleController::class,'createVariant'])->name('vehicle.createVariant');
    Route::post('vehicle/add-variant/{id}', [VehicleController::class,'addVariant'])->name('vehicle.addVariant');
    Route::get('vehicle/edit-variant/{id}', [VehicleController::class,'editVariant'])->name('vehicle.editVariant');
    Route::patch('vehicle/update-variant/{id}', [VehicleController::class,'updateVariant'])->name('vehicle.updateVariant');
    Route::delete('vehicle/delete-variant/{id}', [VehicleController::class,'deleteVariant'])->name('vehicle.deleteVariant');
    Route::get('vehicle/change-variant-status/{id}/{status}', [VehicleController::class,'changeVariantStatus'])->name('vehicle.changeVariantStatus');

    /** Vehicle Color */
    Route::get('vehicle/color/{id}', [VehicleController::class,'colorList'])->name('vehicle.colorList');
    Route::get('vehicle/create-color/{id}', [VehicleController::class,'createColor'])->name('vehicle.createColor');
    Route::post('vehicle/add-color/{id}', [VehicleController::class,'addColor'])->name('vehicle.addColor');
    Route::get('vehicle/edit-color/{id}', [VehicleController::class,'editColor'])->name('vehicle.editColor');
    Route::patch('vehicle/update-color/{id}', [VehicleController::class,'updateColor'])->name('vehicle.updateColor');
    Route::delete('vehicle/delete-color/{id}', [VehicleController::class,'deleteColor'])->name('vehicle.deleteColor');
    Route::get('vehicle/change-color-status/{id}/{status}', [VehicleController::class,'changeColorStatus'])->name('vehicle.changeColorStatus');

    /** Banner */
    Route::resource('banner', BannerController::class);
    Route::get('banner/change-status/{id}/{status}',[BannerController::class,'changeStatus'])->name('banner.changeStatus');

    /** Attributes */
    Route::resource('attributes', AttributeController::class);
    Route::get('attributes/change-status/{id}/{status}',[AttributeController::class,'changeStatus'])->name('attributes.changeStatus');
    Route::get('attributes-list/{type}', [AttributeController::class,'AttributeList'])->name('attributes.list');
    Route::get('parent-attributes-list/{typeId?}', [AttributeController::class,'getParent'])->name('attributes.getParent');
    Route::get('/attributes/{parentId}/children', [AttributeController::class, 'getChildren'])->name('attributes.getChild');

    /** Ex Showroon Prices */
    Route::resource('showroom-prices', ExShowroomPriceController::class);

    /** Used Vehicle */
    Route::resource('used-vehicle',UsedVehicleController::class);
    Route::get('used-vehicle/change-status/{id}/{status}', [UsedVehicleController::class,'changeStatus'])->name('used-vehicle.changeStatus');

    /** Vendor */
    Route::resource('vendor',VendorController::class);
    Route::get('vendor/change-status/{id}/{status}', [VendorController::class,'changeStatus'])->name('vendor.changeStatus');

    /** Site Settings */
    Route::get('site-settings', [SiteSettingController::class, 'index'])->name('site-settings.index');
    Route::post('site-settings', [SiteSettingController::class, 'update'])->name('site-settings.update');
    Route::get('info/terms_and_condition', [SiteSettingController::class, 'termAndCondition'])->name('site-settings.terms');
    Route::get('info/privacy_policy', [SiteSettingController::class, 'privacyPolicy'])->name('site-settings.policy');
    Route::put('update-info/{slug?}', [SiteSettingController::class, 'updateInfo'])->name('site-settings.updateinfo');

    /** Help */
    Route::resource('help',HelpController::class);
    Route::get('help/change-status/{id}/{status}', [HelpController::class,'changeStatus'])->name('help.changeStatus');
    Route::put('help/send-message/{id}', [HelpController::class,'sendMessage'])->name('help.sendMessage');
    Route::get('help/get-message/{id}', [HelpController::class,'getMessages'])->name('help.getMessage');
    
    /** Send Mail */
    Route::get('mail/send-mail-form', [SendMailController::class,'sendMailForm'])->name('mail.sendMailForm');
    Route::post('mail/send-mail', [SendMailController::class,'sendMail'])->name('mail.sendMail');
    Route::get('mail/template-list', [SendMailController::class,'templateList'])->name('mail.templateList');
    Route::get('mail/create-template', [SendMailController::class,'createTemplate'])->name('mail.createTemplate');
    Route::post('mail/store-template', [SendMailController::class,'storeTemplate'])->name('mail.storeTemplate');
    Route::get('mail/edit-template/{id}', [SendMailController::class,'editTemplate'])->name('mail.editTemplate');
    Route::put('mail/update-template/{id}', [SendMailController::class,'updateTemplate'])->name('mail.updateTemplate');
    Route::get('mail/change-status/{id}/{status}', [SendMailController::class,'changeStatus'])->name('mail.changeStatus');
    Route::delete('mail/delete-template/{id}', [SendMailController::class,'deleteTemplate'])->name('mail.deleteTemplate');


    /** Blog */
    Route::resource('blog',BlogController::class);
    Route::get('blog/change-status/{id}/{status}', [BlogController::class,'changeStatus'])->name('blog.changeStatus');

    /** Tag */
    Route::resource('tag',TagController::class);
    Route::get('tag/change-status/{id}/{status}', [TagController::class,'changeStatus'])->name('tag.changeStatus');

    /** Testimonial */
    Route::resource('testimonial',TestimonialController::class);
    Route::get('testimonial/change-status/{id}/{status}', [TestimonialController::class,'changeStatus'])->name('testimonial.changeStatus');

    Route::delete('admin/images/remove/{id}', [ImageController::class, 'destroy'])->name('images.destroy');    
});

/** Option */
Route::get('fuel-list', [OptionController::class,'fuelList'])->name('option.fuellist');
Route::get('transmission-list', [OptionController::class,'transmissionList'])->name('option.transmissionlist');
Route::get('role-list', [OptionController::class,'roleList'])->name('option.rolelist');
Route::get('city-list/{stateId?}', [OptionController::class,'cityList'])->name('option.citylist');
Route::get('state-list', [OptionController::class,'stateList'])->name('option.statelist');
Route::get('model-list/{typeId?}', [OptionController::class,'modelList'])->name('option.modellist');
Route::get('variant-list/{vehicleId?}', [OptionController::class,'variantList'])->name('option.variantlist');
Route::get('user-list', [OptionController::class,'userList'])->name('option.userlist');
Route::get('template-list', [OptionController::class,'templateList'])->name('option.templatelist');
Route::get('get-single-template/{templateId?}', [OptionController::class,'getSingleTemplate'])->name('option.getsingletemplate');
Route::get('tag-list', [OptionController::class,'tagList'])->name('option.taglist');
Route::get('brand-listt', [OptionController::class,'brandList'])->name('option.brandlist');

Route::resource('plan', PlanController::class);
Route::get('plan/change-status/{id}/{status}', [PlanController::class,'changeStatus'])->name('plan.changeStatus');

require __DIR__.'/auth.php';