<?php

namespace WPDevPlugins;

defined( 'ABSPATH' ) || exit;

final class PluginInstaller {

    public static function is_installed( string $slug ): bool {
        self::load_plugin_api();

        return null !== self::find_plugin_file( $slug );
    }

    public static function is_active( string $slug ): bool {
        self::load_plugin_api();

        $file = self::find_plugin_file( $slug );

        return $file ? is_plugin_active( $file ) : false;
    }

    /**
     * @return array{success: array<int, string>, errors: array<int, string>}
     */
    public static function install_and_activate( array $slugs ): array {
        self::load_plugin_api();
        require_once ABSPATH . 'wp-admin/includes/file.php';
        require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';

        $success = [];
        $errors  = [];

        if ( empty( $slugs ) ) {
            return compact( 'success', 'errors' );
        }

        if ( 'direct' !== get_filesystem_method() || ! WP_Filesystem() ) {
            $errors[] = __(
                'Instalacija zahteva direktan filesystem pristup.',
                'wp-dev-plugins-installer'
            );

            return compact( 'success', 'errors' );
        }

        foreach ( array_unique( $slugs ) as $slug ) {
            if ( ! PluginCatalog::exists( $slug ) ) {
                continue;
            }

            $plugin = PluginCatalog::get( $slug );

            if ( ! self::is_installed( $slug ) ) {
                $api = plugins_api(
                    'plugin_information',
                    [
                        'slug'   => $slug,
                        'fields' => [ 'sections' => false ],
                    ]
                );

                if ( is_wp_error( $api ) || empty( $api->download_link ) ) {
                    $errors[] = sprintf(
                        __( '%s: nije moguće pronaći download paket.', 'wp-dev-plugins-installer' ),
                        $plugin['name']
                    );
                    continue;
                }

                $skin      = new \Automatic_Upgrader_Skin();
                $upgrader  = new \Plugin_Upgrader( $skin );
                $installed = $upgrader->install( $api->download_link );

                if ( ! $installed || is_wp_error( $installed ) ) {
                    $errors[] = sprintf(
                        __( '%s: instalacija nije uspela.', 'wp-dev-plugins-installer' ),
                        $plugin['name']
                    );
                    continue;
                }
            }

            $file = self::find_plugin_file( $slug );

            if ( $file && ! is_plugin_active( $file ) ) {
                $activation = activate_plugin( $file );

                if ( is_wp_error( $activation ) ) {
                    $errors[] = sprintf(
                        __( '%s: aktivacija nije uspela — %s', 'wp-dev-plugins-installer' ),
                        $plugin['name'],
                        $activation->get_error_message()
                    );
                    continue;
                }
            }

            $success[] = $slug;
        }

        return compact( 'success', 'errors' );
    }

    private static function load_plugin_api(): void {
        require_once ABSPATH . 'wp-admin/includes/plugin.php';
        require_once ABSPATH . 'wp-admin/includes/plugin-install.php';
    }

    private static function find_plugin_file( string $slug ): ?string {
        foreach ( get_plugins() as $file => $plugin ) {
            if ( dirname( $file ) === $slug || basename( $file, '.php' ) === $slug ) {
                return $file;
            }
        }

        return null;
    }
}
