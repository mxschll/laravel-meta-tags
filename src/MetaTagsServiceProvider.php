<?php

namespace mxschll\MetaTags;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class MetaTagsServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/config/meta-tags.php' => config_path('meta-tags.php')
        ], 'config');

        // Print all meta tags
        Blade::directive('meta', function () {
            return '<?php Meta::toHtml(); ?>';
        });

        // Print given meta tag
        Blade::directive('meta_get', function ($expression) {
            return '<?php Meta::toHtml(' . $expression . '); ?>';
        });

        // Set meta tags in blade by passing a json string
        Blade::directive('meta_set', function ($expression) {
            // Prepare expression for json decoding
            $expression = trim(preg_replace('/\s\s+/', ' ', $expression));
            $arguments = json_decode($expression, true);

            $php = '<?php ';
            foreach ($arguments as $key => $value) {
                $php .= "Meta::set('$key', '$value');";
            }
            $php .= ' ?>';

            return $php;
        });
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        // If config is not published, load it from path
        if ($this->app['config']->get('meta-tags') === null) {
            $this->app['config']->set('meta-tags', require __DIR__ . '/config/meta-tags.php');
        }

        $this->app->singleton('meta-tags', function ($app) {
            return new MetaTags(
                $app['request']->url(),
                $app['config']['meta-tags']
            );
        });
    }
}
