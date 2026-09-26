<?php

namespace WPDevPlugins\Tests;

use PHPUnit\Framework\TestCase;
use WPDevPlugins\PluginCatalog;

final class PluginCatalogTest extends TestCase {

    public function test_catalog_contains_expected_plugins(): void {
        $catalog = PluginCatalog::all();

        $this->assertArrayHasKey( 'query-monitor', $catalog );
        $this->assertArrayHasKey( 'fakerpress', $catalog );
        $this->assertArrayHasKey( 'theme-check', $catalog );
    }

    public function test_catalog_entries_have_required_metadata(): void {
        foreach ( PluginCatalog::all() as $plugin ) {
            $this->assertNotEmpty( $plugin['name'] );
            $this->assertNotEmpty( $plugin['slug'] );
        }
    }

    public function test_unknown_plugin_does_not_exist(): void {
        $this->assertFalse( PluginCatalog::exists( 'does-not-exist' ) );
        $this->assertNull( PluginCatalog::get( 'does-not-exist' ) );
    }
}
