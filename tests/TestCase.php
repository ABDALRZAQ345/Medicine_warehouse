<?php

namespace Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use RefreshDatabase;

    // This method will run before every test in the entire test suite
    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();

    }
    //
}
