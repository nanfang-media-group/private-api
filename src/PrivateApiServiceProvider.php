<?php

namespace SouthCN\PrivateApi;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class PrivateApiServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config.php' => config_path('private-api.php'),
        ]);

        $this->mapRoutes();
    }

    protected function mapRoutes()
    {
        foreach (config('private-api') as $appName => $appDefinition) {
            foreach ($appDefinition as $apiName => $apiDefinition) {
                if (!is_array($apiDefinition)) {
                    continue;
                }

                if (array_has($apiDefinition, 'route')) {
                    $path = $apiDefinition['route'];
                    $privateApi = [
                        'app' => $appName,
                        'api' => $apiName,
                    ];

                    Cache::forever("private-api:route:$path", $privateApi);

                    $this->route($path);
                }
            }
        }
    }

    protected function route($path)
    {
        $middleware = array_merge(
            ['api'],
            config('private-api._.middleware', [])
        );

        Route::middleware($middleware)->any($path, AutoGeneratorController::class . '@generateRoute');
    }
}
