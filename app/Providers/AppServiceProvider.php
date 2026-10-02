<?php

namespace App\Providers;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;

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
        // パスワード再設定は管理者のみ。メールのリンクを管理画面のルートに向ける
        ResetPassword::createUrlUsing(fn ($notifiable, string $token) => route('admin.password.reset', [
            'token' => $token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ]));
        // env() は config:cache 有効時に null を返すため app()->environment() を使う
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }
    }
}
