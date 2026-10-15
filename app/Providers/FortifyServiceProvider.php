<?php
namespace App\Providers;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Laravel\Fortify\Fortify;
class FortifyServiceProvider extends ServiceProvider {
    public function boot(): void {
        Fortify::loginView(fn () => Inertia::render('Auth/Login', ['status'=>session('status')]));
        RateLimiter::for('login', fn (Request $request) => Limit::perMinute(5)->by(Str::lower($request->input('email','')).'|'.$request->ip()));
    }
}
