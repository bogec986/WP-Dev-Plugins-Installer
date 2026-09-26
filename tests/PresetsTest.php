<?php

namespace WPDevPlugins\Tests;

use PHPUnit\Framework\TestCase;
use WPDevPlugins\PluginCatalog;
use WPDevPlugins\Presets;

final class PresetsTest extends TestCase {

    public function test_expected_presets_exist(): void {
        foreach ( [ 'core', 'theme', 'plugin', 'content', 'legacy' ] as $preset ) {
            $this->assertTrue( Presets::exists( $preset ) );
        }
    }

    public function test_every_preset_plugin_exists_in_catalog(): void {
        foreach ( Presets::all() as $plugins ) {
            foreach ( $plugins as $slug ) {
                $this->assertTrue(
                    PluginCatalog::exists( $slug ),
                    sprintf( 'Preset references unknown plugin: %s', $slug )
                );
            }
        }
    }
}
