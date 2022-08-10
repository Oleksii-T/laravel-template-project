<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\SubscriptionPlanController;
use App\Http\Controllers\FeedbackController;
use App\Http\Controllers\SocialAuthController;
use App\Http\Controllers\StripeController;

/*
 *
 * Routes for client side
 *
 */

Route::prefix('tmp')->group(function () {
    Route::get('test', function () {
        $feedback = \App\Models\Feedback::find(17);
        return new \App\Mail\FeedbackMail($feedback);
    });
});

// Guest
Route::get('/', [PageController::class, 'index'])->name('index');
Route::get('blog', [PageController::class, 'blog'])->name('blog');
Route::get('faq', [PageController::class, 'faq'])->name('faq');
Route::get('auth/social/{provider}', [SocialAuthController::class, 'redirect'])->name('auth.social');
Route::get('auth/callback/{provider}', [SocialAuthController::class, 'callback']);

// Logged in
Route::middleware('auth:web')->group(function () {
    Route::get('logout', function () {
        auth()->logout();
        return redirect()->route('index');
    })->name('logout');

    Route::middleware('verified')->group(function () {
        // Verified email

        // Profile
        Route::get('profile', [ProfileController::class, 'index'])->name('profile.index');
        Route::put('profile', [ProfileController::class, 'update'])->name('profile.update');

        // Feedbacks
        Route::get('contact-us', [FeedbackController::class, 'index'])->name('feedbacks.index');
        Route::post('feedbacks', [FeedbackController::class, 'store'])->name('feedbacks.store');

        // Subscription Plans
        Route::prefix('subscription-plans')->name('subscription-plans.')->group(function () {
            Route::get('/', [SubscriptionPlanController::class, 'index'])->name('index');
            Route::get('{subscriptionPlan}/show', [SubscriptionPlanController::class, 'show'])->name('show');
            Route::post('{subscriptionPlan}/subscribe', [SubscriptionPlanController::class, 'subscribe'])->name('subscribe');
        });

        // Subscriptions
        Route::prefix('subscriptions')->name('subscriptions.')->group(function () {
            Route::post('cancel', [SubscriptionController::class, 'cancel'])->name('cancel');
            Route::post('', [SubscriptionController::class, 'store'])->name('store');
            Route::post('payment-method', [SubscriptionController::class, 'paymentMethod'])->name('payment-method');
        });

        // Subscription Stripe
        Route::prefix('stripe')->group(function () {
            Route::post('setup-intent', [StripeController::class, 'setupIntent']);
        });
    });

});

Route::get('{url}', [PageController::class, 'page'])->name('page');