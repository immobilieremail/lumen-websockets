<?php

namespace App\Providers;

use Illuminate\Support\Facades\App;
use Laravel\Lumen\Routing\Router as LumenRouter;
use BeyondCode\LaravelWebSockets\WebSocketsServiceProvider;

class LumenWebSocketsServiceProvider extends WebSocketsServiceProvider
{
    protected function registerRoutes()
    {
        $router = new LumenRouter(new App());
        $router->group([
            'prefix' => config('websockets.path')
        ], function () use ($router) {
            $router->group([
                'middleware' => config('websockets.middleware', [AuthorizeDashboard::class])
            ], function () use ($router) {
                $router->get('/', ShowDashboard::class);
                $router->get('/api/{appId}/statistics', [DashboardApiController::class,  'getStatistics']);
                $router->post('auth', AuthenticateDashboard::class);
                $router->post('event', SendMessage::class);
            });

            $router->group([
                'middleware' => AuthorizeStatistics::class
            ], function () use ($router) {
                $router->post('statistics', [WebSocketStatisticsEntriesController::class, 'store']);
            });
        });
        return $this;
    }
}
