<?php

namespace Vigilant\GptSpellCorrector;

use Statamic\Providers\AddonServiceProvider;

class ServiceProvider extends AddonServiceProvider
{
    protected $viewNamespace = 'gpt-spell-corrector';

    protected $vite = [
        'input' => [
            'resources/js/addon.js',
            'resources/css/addon.css',
        ],
        'publicDirectory' => 'resources/dist',
    ];

    protected $routes = [
        'cp' => __DIR__.'/../routes/cp.php',
    ];

    public function bootAddon(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/gpt-spell-corrector.php', 'gpt-spell-corrector'
        );

        // Publishable config, we use Forma to populate this properly within the CP
        $this->publishes([
            __DIR__.'/../config/gpt-spell-corrector.php' => config_path('gpt-spell-corrector.php')
        ], 'gpt-spell-corrector');
    }
}
