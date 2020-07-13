<?php

namespace Spatie\Skeleton;

use Illuminate\Support\ServiceProvider;
use Spatie\Skeleton\Commands\SkeletonCommand;

class SkeletonServiceProvider extends ServiceProvider
{
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                SkeletonCommand::class,
            ]);
        }
    }
    
    public function register()
    {
    }
}
