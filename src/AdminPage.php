<?php

namespace WPDevPlugins;

defined( 'ABSPATH' ) || exit;

final class AdminPage {

    public static function register(): void {
        add_management_page(
            __( 'Dev Plugins', 'wp-dev-plugins-installer' ),
            __( 'Dev Plugins', 'wp-dev-plugins-installer' ),
            'install_plugins',
            'wp-dev-plugins',
            [ self::class, 'render' ]
        );

        add_action( 'admin_post_wp_dev_plugins_install', [ self::class, 'install_selected' ] );
    }

    public static function render(): void {
        if ( ! current_user_can( 'install_plugins' ) ) {
            wp_die( esc_html__( 'Nemate dozvolu za ovu akciju.', 'wp-dev-plugins-installer' ) );
        }

        $catalog  = PluginCatalog::all();
        $preset   = isset( $_GET['preset'] ) ? sanitize_key( wp_unslash( $_GET['preset'] ) ) : 'core';
        $selected = Presets::exists( $preset ) ? Presets::get( $preset ) : Presets::get( 'core' );

        if ( isset( $_GET['installed'] ) ) {
            self::render_notice(
                'success',
                __( 'Izabrani pluginovi su obrađeni.', 'wp-dev-plugins-installer' )
            );
        }

        if ( isset( $_GET['error'] ) ) {
            self::render_notice(
                'error',
                sanitize_text_field( wp_unslash( $_GET['error'] ) )
            );
        }

        ?>
        <div class="wrap">
            <h1><?php echo esc_html__( 'WP Dev Plugins', 'wp-dev-plugins-installer' ); ?></h1>

            <p>
                <?php
                echo esc_html__(
                    'Izaberite development preset ili ručno označite pluginove koje želite da instalirate i aktivirate.',
                    'wp-dev-plugins-installer'
                );
                ?>
            </p>

            <form method="get" style="margin: 20px 0;">
                <input type="hidden" name="page" value="wp-dev-plugins">

                <label for="wp-dev-plugins-preset">
                    <strong><?php echo esc_html__( 'Preset', 'wp-dev-plugins-installer' ); ?></strong>
                </label>

                <select id="wp-dev-plugins-preset" name="preset" onchange="this.form.submit()">
                    <?php foreach ( array_keys( Presets::all() ) as $name ) : ?>
                        <option value="<?php echo esc_attr( $name ); ?>" <?php selected( $preset, $name ); ?>>
                            <?php echo esc_html( ucfirst( $name ) ); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </form>

            <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
                <input type="hidden" name="action" value="wp_dev_plugins_install">
                <input type="hidden" name="preset" value="<?php echo esc_attr( $preset ); ?>">
                <?php wp_nonce_field( 'wp_dev_plugins_install', 'wp_dev_plugins_nonce' ); ?>

                <table class="widefat striped" style="max-width: 900px;">
                    <thead>
                        <tr>
                            <th style="width: 40px;"></th>
                            <th><?php echo esc_html__( 'Plugin', 'wp-dev-plugins-installer' ); ?></th>
                            <th><?php echo esc_html__( 'Status', 'wp-dev-plugins-installer' ); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ( $catalog as $slug => $plugin ) : ?>
                            <?php
                            $installed = PluginInstaller::is_installed( $slug );
                            $active    = PluginInstaller::is_active( $slug );
                            ?>
                            <tr>
                                <td>
                                    <input
                                        type="checkbox"
                                        name="plugins[]"
                                        value="<?php echo esc_attr( $slug ); ?>"
                                        <?php checked( in_array( $slug, $selected, true ) && ! $active ); ?>
                                        <?php disabled( $active ); ?>
                                    >
                                </td>
                                <td>
                                    <strong><?php echo esc_html( $plugin['name'] ); ?></strong>
                                    <code><?php echo esc_html( $slug ); ?></code>
                                </td>
                                <td>
                                    <?php if ( $active ) : ?>
                                        <span style="color: #008a20;">
                                            <?php echo esc_html__( 'Aktivan', 'wp-dev-plugins-installer' ); ?>
                                        </span>
                                    <?php elseif ( $installed ) : ?>
                                        <?php echo esc_html__( 'Instaliran', 'wp-dev-plugins-installer' ); ?>
                                    <?php else : ?>
                                        <?php echo esc_html__( 'Nije instaliran', 'wp-dev-plugins-installer' ); ?>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <?php submit_button( __( 'Instaliraj i aktiviraj izabrane', 'wp-dev-plugins-installer' ) ); ?>
            </form>
        </div>
        <?php
    }

    public static function install_selected(): void {
        if ( ! current_user_can( 'install_plugins' ) ) {
            wp_die( esc_html__( 'Nemate dozvolu za ovu akciju.', 'wp-dev-plugins-installer' ) );
        }

        check_admin_referer( 'wp_dev_plugins_install', 'wp_dev_plugins_nonce' );

        $slugs = isset( $_POST['plugins'] ) && is_array( $_POST['plugins'] )
            ? array_map( 'sanitize_key', wp_unslash( $_POST['plugins'] ) )
            : [];

        $result = PluginInstaller::install_and_activate( $slugs );

        $args = [
            'page'      => 'wp-dev-plugins',
            'installed' => count( $result['success'] ),
        ];

        if ( ! empty( $result['errors'] ) ) {
            $args['error'] = implode( ' ', $result['errors'] );
        }

        wp_safe_redirect( add_query_arg( $args, admin_url( 'tools.php' ) ) );
        exit;
    }

    private static function render_notice( string $type, string $message ): void {
        printf(
            '<div class="notice notice-%1$s is-dismissible"><p>%2$s</p></div>',
            esc_attr( $type ),
            esc_html( $message )
        );
    }
}
