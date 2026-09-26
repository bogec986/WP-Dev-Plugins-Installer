<?php
/**
 * Plugin Name: WP Dev Plugins Installer
 * Description: Curated development plugin installer for WordPress.
 * Version: 2.0.0
 * Author: Bogec
 * License: MIT
 * Requires at least: 6.5
 * Requires PHP: 8.1
 * Text Domain: wp-dev-plugins-installer
 */

defined( 'ABSPATH' ) || exit;

$autoload = __DIR__ . '/vendor/autoload.php';

if ( is_readable( $autoload ) ) {
    require_once $autoload;
} else {
    spl_autoload_register(
        static function ( string $class ): void {
            $prefix = 'WPDevPlugins\\';

            if ( 0 !== strpos( $class, $prefix ) ) {
                return;
            }

            $relative = substr( $class, strlen( $prefix ) );
            $file     = __DIR__ . '/src/' . str_replace( '\\', '/', $relative ) . '.php';

            if ( is_readable( $file ) ) {
                require_once $file;
            }
        }
    );
}

if ( class_exists( '\WPDevPlugins\Plugin' ) ) {
    \WPDevPlugins\Plugin::boot();
}
