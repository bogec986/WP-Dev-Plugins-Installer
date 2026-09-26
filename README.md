# WP Dev Plugins Installer

A curated WordPress development toolkit for installing and activating commonly used development plugins.

## Requirements

- WordPress 6.5+
- PHP 8.1+

## Development presets

| Preset | Plugins |
| --- | --- |
| Core | Query Monitor, FakerPress |
| Theme | Query Monitor, FakerPress, Theme Check, What The File, Regenerate Thumbnails |
| Plugin | Query Monitor, FakerPress, Debug Bar |
| Content | Query Monitor, FakerPress, Custom Post Type UI, Regenerate Thumbnails, Contact Form 7 |
| Legacy | Query Monitor, FakerPress, Classic Editor, Disable Customizer |

Presets are starting points. Every plugin can also be selected manually.

## Workflow

1. Activate **WP Dev Plugins Installer**.
2. Open **Tools → Dev Plugins**.
3. Choose a preset or select plugins manually.
4. Review each plugin's installation/activation status.
5. Click **Install & Activate Selected**.

The installer never treats development plugins as WordPress dependencies and does not automatically install or activate them on plugin activation.

## Architecture

```
plugin-installer.php
├── config/
│   └── plugins.php
├── src/
│   ├── AdminPage.php
│   ├── Plugin.php
│   ├── PluginCatalog.php
│   ├── PluginInstaller.php
│   └── Presets.php
├── tests/
└── .github/
    └── workflows/
        └── tests.yml
```

Application code uses the `WPDevPlugins` namespace and PSR-4 autoloading. The installer uses WordPress' native plugin API and `Plugin_Upgrader`.

## Development

Install Composer dependencies:

```bash
composer install
```

Run PHPUnit:

```bash
composer test
```

Run WordPress Coding Standards:

```bash
composer lint
```

## License

MIT
