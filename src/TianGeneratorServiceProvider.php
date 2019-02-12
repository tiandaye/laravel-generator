<?php
/**
 * Created by PhpStorm.
 * User: tianwangchong
 * Date: 2019/2/12
 * Time: 22:34
 */

namespace Tian\Generator;

use Illuminate\Support\ServiceProvider;

use Tian\Generator\Console\Commands\Scaffold\GenerateFieldJsonCommand;
use Tian\Generator\Console\Commands\Scaffold\GenerateCommand;
use Tian\Generator\Console\Commands\Scaffold\RollbackCommand;
use Tian\Generator\Console\Commands\Scaffold\PublishCommand;
use Tian\Generator\Console\Commands\Scaffold\FillDataCommand;
use Tian\Generator\Console\Commands\Scaffold\DropTableCommand;
use Tian\Generator\Console\Commands\Scaffold\ScaffoldGeneratorCommand;

class TianGeneratorServiceProvider extends ServiceProvider
{
    /**
     * Boot the service provider.
     *
     * @return void
     */
    public function boot()
    {
        // 发布配置文件
        $configPath = __DIR__ . '/../config/generator.php';
        $this->publishes([
            $configPath => config_path('tian/generator.php'),
        ], 'tian-generator-config');
    }
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        // 用 `Ioc` 容器的 `singleton` 方法和 `bind` 方法都是返回一个类的实例, 不同的是 `singleton` 是单例模式，而 `bind` 是每次返回一个新的实例

        // 批量产生 `field.json` , 通过 `model.csv` 文件生成 `fields.json`
        $this->app->singleton('generate.generateFieldJson', function ($app) {
            return new GenerateFieldJsonCommand();
        });
        // 批量产生脚手架, 通过遍历目录的 `fields.json` 和 `model.json`
        $this->app->singleton('generate.generate', function ($app) {
            return new GenerateCommand();
        });
        // 批量回滚, 通过遍历目录的 `fields.json` 和 `model.json`
        $this->app->singleton('generate.rollback', function ($app) {
            return new RollbackCommand();
        });
        // 发布命令
        $this->app->singleton('generate.publish', function ($app) {
            return new PublishCommand();
        });
        // 批量填充数据
        $this->app->singleton('generate.fillData', function ($app) {
            return new FillDataCommand();
        });
        // 批量删表
        $this->app->singleton('generate.dropTable', function ($app) {
            return new DropTableCommand();
        });
        // 生成脚手架
        $this->app->singleton('generate.scaffold', function ($app) {
            return new ScaffoldGeneratorCommand();
        });
        /**
         * 引入命令
         */
        $this->commands([
            'generate.generateFieldJson',
            'generate.generate',
            'generate.rollback',
            'generate.publish',
            'generate.fillData',
            'generate.dropTable',
            'generate.scaffold',
        ]);
        /**
         * laravel-admin
         */
        // $this->loadAdminAuthConfig();
        // $this->registerRouteMiddleware();
        // $this->commands($this->commands);
    }

    /**
     * Setup auth configuration.
     *
     * @return void
     */
    protected function loadAdminAuthConfig()
    {
        config(array_dot(config('admin.auth', []), 'auth.'));
    }

    /**
     * Register the route middleware.
     *
     * @return void
     */
    protected function registerRouteMiddleware()
    {
        // register route middleware.
        foreach ($this->routeMiddleware as $key => $middleware) {
            app('router')->aliasMiddleware($key, $middleware);
        }
        // register middleware group.
        foreach ($this->middlewareGroups as $key => $middleware) {
            app('router')->middlewareGroup($key, $middleware);
        }
    }
}