<?php
/**
 * PSR-4 Autoloader for Sweet Addons
 *
 * @link       https://websweetstudio.com
 * @since      3.1.0
 * @package    sweetaddons
 */

spl_autoload_register(function ($class) {
    $prefixes = [
        'Sweetaddons\\Admin\\' => __DIR__ . '/../admin/',
        'Sweetaddons\\Public\\' => __DIR__ . '/../public/',
        'Sweetaddons\\'        => __DIR__ . '/',
    ];

    foreach ($prefixes as $prefix => $base_dir) {
        $len = strlen($prefix);
        if (strncmp($prefix, $class, $len) !== 0) {
            continue;
        }

        $relative_class = substr($class, $len);
        $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

        if (file_exists($file)) {
            require $file;
            return;
        }
    }
});
