<?php

namespace Spatie\Skeleton\Commands;

use Illuminate\Console\Command;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;

class SkeletonCommand extends Command
{
    public $signature = 'make:restfulsac {path}';

    public $description = 'Create a folder of restful single action controllers';
    private $path = "/";

    public function handle()
    {
        $methodsSingular = ['create', 'store', 'show', 'update', 'edit', 'destroy'];
        $methodsPlural = ['index'];
        $controllers = collect();
        $pathArray = collect(explode('/', $this->argument('path')));
        $pathArray->transform(function ($item, $key) {
            return Str::studly($item);
        });
        $this->path = $pathArray->join('/');
        $this->line($this->path);
        $last = $pathArray->last();
        foreach ($methodsSingular as $ms) {
            $pathArray->add(Str::studly(join(" ", [
                $ms, Str::singular($last),
                'Controller',
            ])));
            $controllerName = $pathArray->join('/');
            $this->info($controllerName);
            $controllers->push($controllerName);
            $pathArray->pop();
        }
        foreach ($methodsPlural as $mp) {
            $pathArray->add(Str::studly(join(" ", [
                $mp, Str::plural($last),
                'Controller',
            ])));
            $controllerName = $pathArray->join('/');
            $controllers->push($controllerName);
            $pathArray->pop();
        }

        $this->makeRessourceView($last);
        $this->call('make:request', ['name' => sprintf("%sRequest", Str::studly($last))]);
        $this->call('make:policy', ['name' => sprintf("%sPolicy", Str::studly($last)), '--model' => $this->argument('path')]);
        foreach ($controllers as $cn) {
            $this->call("make:controller", ['-i' => true, 'name' => $cn]);
        }
    }

    private function makeRessourceView(string $name): void
    {
        $views = ['index', 'create', 'show', 'edit'];
        foreach ($views as $v) {
            $path = $this->viewPath(sprintf("%s/%s", Str::lower($this->path), $v));
            $this->createDir($path);

            if (File::exists($path)) {
                $this->error("File {$path} already exists!");
                return;
            }

            File::put($path, $path);

            $this->info("File {$path} created.");
        }

    }

    private function viewPath($view)
    {
        $view = str_replace('.', '/', $view).'.blade.php';

        $path = "resources/views/{$view}";

        return $path;
    }

    private function createDir($path)
    {
        $dir = dirname($path);

        if (!file_exists($dir)) {
            mkdir($dir, 0777, true);
        }
    }
}
