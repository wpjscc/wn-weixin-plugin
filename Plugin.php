<?php namespace Wpjscc\Weixin;

use Backend;
use Backend\Models\UserRole;
use System\Classes\PluginBase;
use Illuminate\Foundation\AliasLoader;
use Config;
use App;

/**
 * weixin Plugin Information File
 */
class Plugin extends PluginBase
{
    /**
     * Returns information about this plugin.
     */
    public function pluginDetails(): array
    {
        return [
            'name'        => 'wpjscc.weixin::lang.plugin.name',
            'description' => 'wpjscc.weixin::lang.plugin.description',
            'author'      => 'wpjscc',
            'icon'        => 'icon-leaf'
        ];
    }

    /**
     * Register method, called when the plugin is first registered.
     */
    public function register(): void
    {
        $this->bootPackages();

        $this->app->bind('mini_crypt', function($app){
            return new \WeMini\Crypt(config('xcx'));
        });
        $this->app->bind('mini_qrcode', function($app){
            return new \WeMini\Qrcode(config('xcx'));
        });
        $this->app->bind('wechat_oauth', function($app){
            return new \WeChat\Oauth(config('wechat'));
        });
    }

    /**
     * Boot method, called right before the request route.
     */
    public function boot(): void
    {

    }

    /**
     * Registers any frontend components implemented in this plugin.
     */
    public function registerComponents(): array
    {
        return []; // Remove this line to activate

        return [
            'Wpjscc\Weixin\Components\MyComponent' => 'myComponent',
        ];
    }

    /**
     * Registers any backend permissions used by this plugin.
     */
    public function registerPermissions(): array
    {
        return []; // Remove this line to activate

        return [
            'wpjscc.weixin.some_permission' => [
                'tab' => 'wpjscc.weixin::lang.plugin.name',
                'label' => 'wpjscc.weixin::lang.permissions.some_permission',
                'roles' => [UserRole::CODE_DEVELOPER, UserRole::CODE_PUBLISHER],
            ],
        ];
    }

    /**
     * Registers backend navigation items for this plugin.
     */
    public function registerNavigation(): array
    {
        return []; // Remove this line to activate

        return [
            'weixin' => [
                'label'       => 'wpjscc.weixin::lang.plugin.name',
                'url'         => Backend::url('wpjscc/weixin/mycontroller'),
                'icon'        => 'icon-leaf',
                'permissions' => ['wpjscc.weixin.*'],
                'order'       => 500,
            ],
        ];
    }

    public function bootPackages()
    {
        // Get the namespace of the current plugin to use in accessing the Config of the plugin
        $pluginNamespace = str_replace('\\', '.', strtolower(__NAMESPACE__));

        // Instantiate the AliasLoader for any aliases that will be loaded
        $aliasLoader = AliasLoader::getInstance();

        // Get the packages to boot
        $packages = Config::get($pluginNamespace . '::packages');

        // Boot each package
        foreach ($packages as $name => $options) {
            // Setup the configuration for the package, pulling from this plugin's config
            if (!empty($options['config']) && !empty($options['config_namespace'])) {
                Config::set($options['config_namespace'], $options['config']);
            }

            // Register any Service Providers for the package
            if (!empty($options['providers'])) {
                foreach ($options['providers'] as $provider) {
                    App::register($provider);
                }
            }

            // Register any Aliases for the package
            if (!empty($options['aliases'])) {
                foreach ($options['aliases'] as $alias => $path) {
                    $aliasLoader->alias($alias, $path);
                }
            }
        }
    }
}
