<?php

use App\Http\Controllers\Dashboard\AdminsController;
use App\Http\Controllers\Dashboard\CategoriesController;
use App\Http\Controllers\Dashboard\dashbordController;
use App\Http\Controllers\Dashboard\ProductsController;
use App\Http\Controllers\Dashboard\ProfileController;
use App\Http\Controllers\Dashboard\RolesController;
use App\Http\Controllers\Dashboard\UsersController;
use App\Http\Controllers\HeaderController;
use Illuminate\Support\Facades\Route;

Route::group([
    'middleware'=>['auth:admin'],
    'as'=>'dashboard.', //prefix
    'prefix'=>'admin/dashboard'
],function(){


    Route::get('/profile',[ProfileController::class,'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('/', [dashbordController::class, 'index'])
        ->middleware(['auth'])
        ->name('dashboard');
    Route::get('/categories/trash',[CategoriesController::class,'trash'])
    ->name('categories.trash');
    Route::put('/categories/{category}/restore', [CategoriesController::class, 'restore'])
    ->name('categories.restore');
    Route::delete('/categories/{category}/force-delete', [CategoriesController::class, 'forceDelete'])
    ->name('categories.force-delete');

    Route::resource('/categories', CategoriesController::class)
        ->middleware(['auth']);
    Route::resource('/products', ProductsController::class);
    Route::resource('/roles', RolesController::class);
    Route::resource('/admins', AdminsController::class);
    Route::resource('/users', UsersController::class);

    // Route::resources([
    //     'roles' => RolesController::class,
    //     'users' => UsersController::class,
    // ]);

});

// Route::middleware('auth')->as('dashboard.')->prefix('dashboard')->group(function(){

// });
