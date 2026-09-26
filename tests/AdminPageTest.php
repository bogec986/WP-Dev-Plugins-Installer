<?php

namespace WPDevPlugins\Tests;

use PHPUnit\Framework\TestCase;
use WPDevPlugins\AdminPage;

final class AdminPageTest extends TestCase {

    public function test_admin_page_class_exists(): void {
        $this->assertTrue( class_exists( AdminPage::class ) );
    }
}
