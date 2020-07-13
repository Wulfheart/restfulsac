<?php

namespace Spatie\Skeleton\Commands;

use Illuminate\Console\Command;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;

class SkeletonCommand extends Command
{
    public $signature = 'make:restfulsac {path}';

    public $description = 'Create a folder of restful single action controllers';

    public function handle()
    {
        $methodsSingular = ['create', 'store', 'show', 'update', 'destroy'];
        $methodsPlural = ['index'];
        $controllers = collect();
        $pathArray = collect(explode('/', $this->argument('path')));
        $pathArray->transform(function ($item, $key){
            return Str::studly($item);
        });
        $last = $pathArray->last();
        foreach ($methodsSingular as $ms) {
            $pathArray->add(Str::studly(join(" ", [$ms, Str::singular($last),
                'Controller'])));
            $controllerName = $pathArray->join('/');
            $this->info($controllerName);
            $controllers->push($controllerName);
            $pathArray->pop();
        }
        foreach ($methodsPlural as $mp){
            $pathArray->add(Str::studly(join(" ", [$mp, Str::plural($last),
                'Controller'])));
            $controllerName = $pathArray->join('/');
            $this->info($controllerName);
            $controllers->push($controllerName);
            $pathArray->pop();
        }

        foreach ($controllers as $cn){
            $this->warn($cn);
            $this->call("make:controller", ['-i' => true, 'name' => $cn]);
        }
    }
}
