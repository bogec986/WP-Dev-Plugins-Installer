<?php

namespace WPDevPlugins;

defined( 'ABSPATH' ) || exit;

final class PluginInstaller {

    public static function register(): void {
        add_action( 'tgmpa_register', [ self::class, 'register_plugins' ] );
    }

    public static function register_plugins(): void {
        $plugins = [];

        foreach ( PluginCatalog::all() as $plugin ) {
            $plugins[] = [
                'name'     => $plugin['name'],
                'slug'     => $plugin['slug'],
                'required' => false,
            ];
        }

        tgmpa(
            $plugins,
            [
                'id'           => 'wp-dev-plugins-installer',
                'default_path' => '',
                'menu'         => 'wp-dev-plugins',
                'has_notices'  => true,
                'dismissable'  => true,
                'dismiss_msg'  => '',
                'is_automatic' => false,
                'message'      => '',
            ]
        );
    }
}
