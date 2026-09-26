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
                'menu'         => 'wp-dev-plugins-tgmpa',
                'parent_slug'  => 'tools.php',
                'has_notices'  => true,
                'dismissable'  => true,
                'dismiss_msg'  => '',
                'is_automatic' => false,
                'message'      => '',
            ]
        );
    }

    public static function is_installed( string $slug ): bool {
        require_once ABSPATH . 'wp-admin/includes/plugin.php';

        return null !== self::find_plugin_file( $slug );
    }

    public static function is_active( string $slug ): bool {
        require_once ABSPATH . 'wp-admin/includes/plugin.php';

        $file = self::find_plugin_file( $slug );

        return $file ? is_plugin_active( $file ) : false;
    }

    public static function install_and_activate( array $slugs ): array {
        require_once ABSPATH . 'wp-admin/includes/file.php';
        require_once ABSPATH . 'wp-admin/includes/plugin.php';
        require_once ABSPATH . 'wp-admin/includes/plugin-install.php';
        require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';

        $success = [];
        $errors  = [];

        if ( empty( $slugs ) ) {
            return compact( 'success', 'errors' );
        }

        $method = get_filesystem_method();

        if ( 'direct' !== $method ) {
            $errors[] = __(
                'Instalacija zahteva direktan filesystem pristup. Podesite odgovarajući filesystem metod ili koristite TGMPA ekran za ručnu instalaciju.',
                'wp-dev-plugins-installer'
            );

            return compact( 'success', 'errors' );
        }

        if ( ! WP_Filesystem() ) {
            $errors[] = __(
                'Nije moguće uspostaviti direktan pristup filesystemu.',
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
                        'fields' => [
                            'sections' => false,
                        ],
                    ]
                );

                if ( is_wp_error( $api ) || empty( $api->download_link ) ) {
                    $errors[] = sprintf(
                        __( '%s: nije moguće pronaći download paket.', 'wp-dev-plugins-installer' ),
                        $plugin['name']
                    );

                    continue;
                }

                $skin     = new \Automatic_Upgrader_Skin();
                $upgrader = new \Plugin_Upgrader( $skin );
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

    private static function find_plugin_file( string $slug ): ?string {
        foreach ( get_plugins() as $file => $plugin ) {
            if ( dirname( $file ) === $slug || basename( $file, '.php' ) === $slug ) {
                return $file;
            }
        }

        return null;
    }
}
