<?php

namespace WPDevPlugins;

defined( 'ABSPATH' ) || exit;

final class Plugin {

    public static function boot(): void {
        if ( ! is_admin() ) {
            return;
        }

        require_once __DIR__ . '/../includes/class-tgm-plugin-activation.php';

        PluginInstaller::register();
    }
}
