<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InquiryController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public site
|--------------------------------------------------------------------------
*/

Route::get('/', HomeController::class)->name('home');

Route::post('/inquiries', [InquiryController::class, 'store'])
    ->middleware('throttle:5,1')
    ->name('inquiries.store');

// Dedicated direct admin login URL (not linked publicly)
Route::redirect('/login', '/admin-login');
Route::redirect('/admin/login', '/admin-login');

Route::middleware('guest')->group(function () {
    Route::get('/admin-login', [Admin\AuthController::class, 'create'])->name('admin.login');
    Route::post('/admin-login', [Admin\AuthController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name('admin.login.store');
});

/*
|--------------------------------------------------------------------------
| Admin panel
|--------------------------------------------------------------------------
*/

Route::prefix('admin')->name('admin.')->group(function () {
    // Helpful route aliases to prevent 404s
    Route::redirect('site-settings', '/admin/settings');
    Route::redirect('music-videos', '/admin/videos');
    Route::redirect('setting', '/admin/settings');
    Route::redirect('video', '/admin/videos');
    Route::redirect('testimonial', '/admin/testimonials');
    Route::redirect('inquiry', '/admin/inquiries');

    Route::middleware('auth')->group(function () {
        Route::post('logout', [Admin\AuthController::class, 'destroy'])->name('logout');

        Route::get('/', Admin\DashboardController::class)->name('dashboard');

        Route::get('settings', [Admin\SettingController::class, 'edit'])->name('settings.edit');
        Route::post('settings', [Admin\SettingController::class, 'update'])->name('settings.update');

        Route::post('videos/reorder', [Admin\MusicVideoController::class, 'reorder'])->name('videos.reorder');
        Route::resource('videos', Admin\MusicVideoController::class)->except('show');

        Route::post('gallery/reorder', [Admin\GalleryController::class, 'reorder'])->name('gallery.reorder');
        Route::get('gallery', [Admin\GalleryController::class, 'index'])->name('gallery.index');
        Route::post('gallery', [Admin\GalleryController::class, 'store'])->name('gallery.store');
        Route::put('gallery/{item}', [Admin\GalleryController::class, 'update'])->name('gallery.update');
        Route::delete('gallery/{item}', [Admin\GalleryController::class, 'destroy'])->name('gallery.destroy');

        Route::post('testimonials/reorder', [Admin\TestimonialController::class, 'reorder'])->name('testimonials.reorder');
        Route::resource('testimonials', Admin\TestimonialController::class)->except('show');

        Route::get('inquiries', [Admin\InquiryController::class, 'index'])->name('inquiries.index');
        Route::get('inquiries/{inquiry}', [Admin\InquiryController::class, 'show'])->name('inquiries.show');
        Route::patch('inquiries/{inquiry}/unread', [Admin\InquiryController::class, 'unread'])->name('inquiries.unread');
        Route::delete('inquiries/{inquiry}', [Admin\InquiryController::class, 'destroy'])->name('inquiries.destroy');

        Route::get('account', [Admin\AccountController::class, 'edit'])->name('account.edit');
        Route::put('account', [Admin\AccountController::class, 'update'])->name('account.update');
    });
});
