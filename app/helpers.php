<?php

namespace App;

use Roots\Sage\Container;

/**
 * Get the sage container.
 *
 * @param string $abstract
 * @param array $parameters
 * @param Container $container
 * @return Container|mixed
 */
function sage($abstract = null, $parameters = [], Container $container = null)
{
    $container = $container ?: Container::getInstance();
    if (!$abstract) {
        return $container;
    }
    return $container->bound($abstract)
        ? $container->makeWith($abstract, $parameters)
        : $container->makeWith("sage.{$abstract}", $parameters);
}

/**
 * Get / set the specified configuration value.
 *
 * If an array is passed as the key, we will assume you want to set an array of values.
 *
 * @param array|string $key
 * @param mixed $default
 * @return mixed|\Roots\Sage\Config
 * @copyright Taylor Otwell
 * @link https://github.com/laravel/framework/blob/c0970285/src/Illuminate/Foundation/helpers.php#L254-L265
 */
function config($key = null, $default = null)
{
    if (is_null($key)) {
        return sage('config');
    }
    if (is_array($key)) {
        return sage('config')->set($key);
    }
    return sage('config')->get($key, $default);
}

/**
 * @param string $file
 * @param array $data
 * @return string
 */
function template($file, $data = [])
{
    if (remove_action('wp_head', 'wp_enqueue_scripts', 1)) {
        wp_enqueue_scripts();
    }
    return sage('blade')->render($file, $data);
}

/**
 * Retrieve path to a compiled blade view
 * @param $file
 * @param array $data
 * @return string
 */
function template_path($file, $data = [])
{
    return sage('blade')->compiledPath($file, $data);
}

/**
 * @param $asset
 * @return string
 *
 * Note: modified from the Sage's original method to work with manfiest generated by 'gulp-rev'
 */
function asset_path($asset)
{
    return join(
        [
            config('assets')['uri'],
            sage('assets')->get('/'. $asset),
        ]
    );
}

/**
 * @param string|string[] $templates Possible template files
 * @return array
 */
function filter_templates($templates)
{
    $paths = apply_filters('sage/filter_templates/paths', [
        'views',
        'resources/views'
    ]);
    $paths_pattern = "#^(" . implode('|', $paths) . ")/#";
    return collect($templates)
        ->map(function ($template) use ($paths_pattern) {
            /** Remove .blade.php/.blade/.php from template names */
            $template = preg_replace('#\.(blade\.?)?(php)?$#', '', ltrim($template));
            /** Remove partial $paths from the beginning of template names */
            if (strpos($template, '/')) {
                $template = preg_replace($paths_pattern, '', $template);
            }
            return $template;
        })
        ->flatMap(function ($template) use ($paths) {
            return collect($paths)
                ->flatMap(function ($path) use ($template) {
                    return [
                        "{$path}/{$template}.blade.php",
                        "{$path}/{$template}.php"
                    ];
                })
                ->concat([
                    "{$template}.blade.php",
                    "{$template}.php",
                ]);
        })
        ->filter()
        ->unique()
        ->all();
}

/**
 * @param string|string[] $templates Relative path to possible template files
 * @return string Location of the template
 */
function locate_template($templates)
{
    return \locate_template(filter_templates($templates));
}

/**
 * Render the specified blade template
 * @param string $templatePath The template path. Can use dot notation
 * @param $data
 * @param bool $echo Should echo or just return?
 * @return null
 */
function render_template($templatePath, $data = [], $echo = true)
{
    $templatePath = str_replace('.', '/', $templatePath);
    $template = template(
        config('theme')['dir'] . "/resources/views/{$templatePath}.blade.php",
        $data
    );
    if ($echo) {
        echo $template;
    }
    return $template;
}

/**
 * Dumps a variable in the php stdout
 * @param $data
 * @param bool $onlyLogged
 * @return null
 */
function dump($data, $onlyLogged = true)
{
    echo '<pre style="white-space: pre-wrap;">' . htmlspecialchars(@var_export($data, true)) . '</pre>';
    return $data;
}

/**
 * Dumps an variable in the console
 * @param $data
 * @param bool $onlyLogged
 * @return null
 */
function console($data, $onlyLogged = true)
{
    echo '<script>console.log(' . json_encode($data) . ');</script>';
    return $data;
}

/**
 * Get Image URL
 * @param $data
 * @return string
 */
function get_image($data, $onlyLogged = true)
{
    return asset_path("images/{$data}");
}

/**
 * Remove the protocol (http/https) from a specific URL
 * @param string $url URL to remove the protocol
 * @return string
 */
function get_url_without_protocol($url)
{
    return preg_replace('(https?://)', '//', $url);
}
