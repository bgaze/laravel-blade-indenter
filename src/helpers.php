<?php

use Bgaze\BladeIndenter\BladeIndenter;

if (!function_exists('indent_blade_string')) {
    /**
     * Indent a Blade string.
     *
     * @param  string  $string  The string to indent
     *
     * @return string The indented string
     */
    function indent_blade_string($string)
    {
        return resolve(BladeIndenter::class)->indent($string);
    }
}


if (!function_exists('indent_blade_file')) {
    /**
     * Indent a Blade string.
     *
     * @param  string  $path  The path of the file to indent
     * @param  bool  $write  Overwrite provided file ?
     *
     * @return string The indented file content
     */
    function indent_blade_file($path, $write = true)
    {
        $string = indent_blade_file(file_get_contents($path));

        if ($write) {
            file_put_contents($path, $string);
        }

        return $string;
    }
}