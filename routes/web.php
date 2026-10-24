<?php

use App\Http\Controllers\SiteController;
use Illuminate\Support\Facades\Route;

Route::get('/', [SiteController::class, 'home'])->name('home');
Route::get('/about', [SiteController::class, 'about'])->name('about');
Route::get('/services', [SiteController::class, 'services'])->name('services');
Route::get('/projects', [SiteController::class, 'projects'])->name('projects');
Route::get('/contact', [SiteController::class, 'contact'])->name('contact');
Route::get('/company-profile', [SiteController::class, 'profile'])->name('profile');

Route::post('/contact', [SiteController::class, 'submitContact'])->middleware('throttle:5,1')->name('contact.submit');
