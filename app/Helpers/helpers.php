<?php

use Carbon\Carbon;

/**
 * Function active menu
 *
 * @param string $uri
 * @return mixed
 */
if (!function_exists('active_menu')) {
    function active_menu($routes = [], $class = 'kt-menu__item--active ', $callback = null)
    {
        $activeClass = request()->route()->named($routes) ? $class : '';
        if (is_null($callback)) {
            return $activeClass;
        }
        return $callback && $activeClass;
    }
}

if (!function_exists('get_sql_with_bindings')) {
    /**
     * Get QSL Raw for debug
     *
     * @param $query
     * @return string
     *
     */
    function get_sql_with_bindings($query): string
    {
        return vsprintf(
            str_replace('?', '%s', $query->toSql()),
            collect(
                $query->getBindings()
            )
                ->map(function ($binding) {
                    return is_numeric($binding) ? $binding : "'{$binding}'";
                })->toArray()
        );
    }
}

if (!function_exists('carbon_parse')) {
    /**
     * Convert datetime string to carbon instance
     *
     * @param $dateTime
     * @return Carbon
     */
    function carbon_parse($dateTime)
    {
        return Carbon::parse($dateTime);
    }
}
