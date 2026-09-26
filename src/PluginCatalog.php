<?php

namespace WPDevPlugins;

defined( 'ABSPATH' ) || exit;

final class PluginCatalog {

    public static function all(): array {
        static $plugins = null;

        if ( null === $plugins ) {
            $plugins = require __DIR__ . '/../config/plugins.php';
        }

        return $plugins;
    }

    public static function get( string $slug ): ?array {
        return self::all()[ $slug ] ?? null;
    }

    public static function exists( string $slug ): bool {
        return null !== self::get( $slug );
    }
}
