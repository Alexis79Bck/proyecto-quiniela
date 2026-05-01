<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Ensure logging is disabled in tests to prevent memory issues
        $this->app['config']->set('logging.default', 'null');
    }
}
