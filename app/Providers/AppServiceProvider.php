<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(
            'App\Repositories\UserRepositoryInterface',
            'App\Repositories\Eloquent\UserRepository'
        );

        $this->app->bind(
            'App\Repositories\ProjectRepositoryInterface',
            'App\Repositories\Eloquent\ProjectRepository'
        );

        $this->app->bind(
            'App\Repositories\TaskRepositoryInterface',
            'App\Repositories\Eloquent\TaskRepository'
        );

        $this->app->bind(
            'App\Repositories\CommentRepositoryInterface',
            'App\Repositories\Eloquent\CommentRepository'
        );
    }
}
