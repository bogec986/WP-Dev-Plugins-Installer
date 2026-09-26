<?php

namespace WPDevPlugins;

defined( 'ABSPATH' ) || exit;

final class Plugin {

    public static function boot(): void {
        if ( ! is_admin() ) {
            return;
        }

        AdminPage::register();
    }
}
