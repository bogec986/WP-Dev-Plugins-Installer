# WP Dev Plugins Installer

A curated development utility for quickly installing WordPress development plugins.

## Requirements

- WordPress 6.5+
- PHP 8.1+

## Development presets

- Core — Query Monitor, FakerPress
- Theme — Query Monitor, FakerPress, Theme Check, What The File, Regenerate Thumbnails
- Plugin — Query Monitor, FakerPress, Debug Bar
- Content — Query Monitor, FakerPress, Custom Post Type UI, Regenerate Thumbnails, Contact Form 7
- Legacy — Query Monitor, FakerPress, Classic Editor, Disable Customizer

Presets are a curated starting point; plugins are not WordPress dependencies.

## Installation

Copy the plugin to the WordPress plugins directory and activate it from the WordPress admin.

The installer is intended for development environments.

## Architecture

The plugin catalog lives in config/plugins.php, presets live in src/Presets.php, and TGMPA integration is isolated in src/PluginInstaller.php.

The bundled TGMPA library is third-party code and is intentionally kept separate from the plugin's application code.

## License

MIT
