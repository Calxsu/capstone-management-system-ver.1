<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
            \App\Repositories\SchoolYearRepositoryInterface::class,
            \App\Repositories\SchoolYearRepository::class
        );

        $this->app->bind(
            \App\Repositories\PanelMemberRepositoryInterface::class,
            \App\Repositories\PanelMemberRepository::class
        );

        $this->app->bind(
            \App\Repositories\StudentRepositoryInterface::class,
            \App\Repositories\StudentRepository::class
        );

        $this->app->bind(
            \App\Repositories\GroupRepositoryInterface::class,
            \App\Repositories\GroupRepository::class
        );

        $this->app->bind(
            \App\Actions\UpdateGroupStatusAction::class,
            function ($app) {
                return new \App\Actions\UpdateGroupStatusAction(
                    $app->make(\App\Repositories\GroupRepositoryInterface::class)
                );
            }
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register DomPDF alias
        $this->app->alias('dompdf.wrapper', 'PDF');
    }
}
