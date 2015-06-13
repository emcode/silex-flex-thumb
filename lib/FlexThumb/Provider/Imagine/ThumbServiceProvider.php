<?php

namespace FlexThumb\Provider\Imagine;

use FlexThumb\ThumbService;
use Imagine\Gd\Imagine;
use Silex\Application;
use Silex\ServiceProviderInterface;

class ThumbServiceProvider implements ServiceProviderInterface
{
    /**
     * Registers services on the given app.
     *
     * This method should only be used to configure services and parameters.
     * It should not get services.
     */
    public function register(Application $app)
    {
        $app['thumb.imagine'] = $app->share(function() use ($app) {
            return new Imagine();
        });

        $app['thumb.generator'] = $app->share(function() use ($app) {
            $generator = new DefaultThumbGenerator($app['thumb.imagine'], $app['thumb.types']);
            return $generator;
        });

        $app['thumb'] = $app->share(function() use ($app) {
            $thumbService = new ThumbService($app['thumb.generator']);
            $delimiter = isset($app['thumb.token_delimiter']) ? $app['thumb.token_delimiter'] : '%';
            $thumbService->setSourcePattern($app['thumb.source'], $delimiter);
            $thumbService->setTargetPattern($app['thumb.target'], $delimiter);
            return $thumbService;
        });
    }

    /**
     * Bootstraps the application.
     *
     * This method is called after all services are registered
     * and should be used for "dynamic" configuration (whenever
     * a service must be requested).
     */
    public function boot(Application $app)
    {
        // TODO: Implement boot() method.
    }
}
