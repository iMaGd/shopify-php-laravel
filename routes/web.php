<?php

use App\Http\Controllers\ShopifyController;
use Illuminate\Support\Facades\Route;


Route::get('/', function () {
    return view('welcome');
});


Route::middleware([
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('dashboard', [ ShopifyController::class, 'dashboard'])->name('dashboard');
    Route::get('editor'   , [ ShopifyController::class, 'editor'   ])->name('editor'   );
});


// shopify auth endpoints
Route::get( config('shopify.route.auth_redirect'), [ ShopifyController::class, 'redirect' ]);
Route::get( config('shopify.route.auth_callback'), [ ShopifyController::class, 'callback' ]);
Route::get('exitiframe', [ ShopifyController::class, 'exitIframe' ]);
