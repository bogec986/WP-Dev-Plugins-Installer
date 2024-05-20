<?php
/*
Plugin Name: WP Dev Plugins Installer
Description: This plugin installs required WP dev plugins using TGM Plugin Activation.
Version: 1.0
Author: Bogec
*/
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

// Include TGM Plugin Activation library
require_once plugin_dir_path( __FILE__ ) . 'includes/class-tgm-plugin-activation.php';

// Define required plugins
function my_plugin_required_plugins() {
    $plugins = array(
        array(
            'name'     => 'Contact Form 7',
            'slug'     => 'contact-form-7',
            'required' => true,
        ),
        array(
            'name'     => 'Query Monitor',
            'slug'     => 'query-monitor',
            'required' => true,
        ),
        array(
            'name'     => 'Clear Cache for Me',
            'slug'     => 'clear-cache-for-widgets',
            'required' => true,
        ),
        array(
            'name'     => 'Custom Post Type UI',
            'slug'     => 'custom-post-type-ui',
            'required' => true,
        ),
        array(
            'name'     => 'Debug Bar',
            'slug'     => 'debug-bar',
            'required' => true,
        ),
        array(
            'name'     => 'What The File',
            'slug'     => 'what-the-file',
            'required' => true,
        ),
        array(
            'name'     => 'Disable Customizer',
            'slug'     => 'customizer-disabler',
            'required' => true,
        ),
        array(
            'name'     => 'FakerPress',
            'slug'     => 'fakerpress',
            'required' => true,
        ),
        array(
            'name'     => 'Regenerate Thumbnails',
            'slug'     => 'regenerate-thumbnails',
            'required' => true,
        ),
        array(
            'name'     => 'Theme Check',
            'slug'     => 'theme-check',
            'required' => true,
        ),
        array(
            'name'     => 'Classic Editor',
            'slug'     => 'classic-editor',
            'required' => true,
        ),
        // Add more plugins as needed
    );

    return $plugins;
}

// Register required plugins using TGM Plugin Activation
function my_plugin_register_required_plugins() {
    $plugins = my_plugin_required_plugins();

    $config = array(
        'id'           => 'plugin-installer-tgmpa',
        'default_path' => '',
        'menu'         => 'tgmpa-install-plugins',
        'has_notices'  => true,
        'dismissable'  => true,
        'dismiss_msg'  => '',
        'is_automatic' => true,
        'message'      => '',
    );

    tgmpa( $plugins, $config );
}
add_action( 'tgmpa_register', 'my_plugin_register_required_plugins' );