<?php

namespace App\Providers;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Schema::defaultStringLength(191);

        View::composer('*', function ($view) {
            $cartCount = 0;

            if (!session()->has('NguoiDungID')) {
                $view->with('cartCount', $cartCount);
                return;
            }

            $cartCount = (int) DB::table('ChiTietGioHang')
                ->join(
                    'GioHang',
                    'ChiTietGioHang.GioHangID',
                    '=',
                    'GioHang.GioHangID'
                )
                ->where('GioHang.NguoiDungID', session('NguoiDungID'))
                ->sum('ChiTietGioHang.SoLuong');

            $view->with('cartCount', $cartCount);
        });
    }
}
