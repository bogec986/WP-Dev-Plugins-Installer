<?php

namespace WPDevPlugins;

defined( 'ABSPATH' ) || exit;

final class Presets {

    public static function all(): array {
        return [
            'core' => [
                'query-monitor',
                'fakerpress',
            ],
            'theme' => [
                'query-monitor',
                'fakerpress',
                'theme-check',
                'what-the-file',
                'regenerate-thumbnails',
            ],
            'plugin' => [
                'query-monitor',
                'fakerpress',
                'debug-bar',
            ],
            'content' => [
                'query-monitor',
                'fakerpress',
                'custom-post-type-ui',
                'regenerate-thumbnails',
                'contact-form-7',
            ],
            'legacy' => [
                'query-monitor',
                'fakerpress',
                'classic-editor',
                'customizer-disabler',
            ],
        ];
    }

    public static function get( string $preset ): array {
        return self::all()[ $preset ] ?? [];
    }

    public static function exists( string $preset ): bool {
        return array_key_exists( $preset, self::all() );
    }
}
