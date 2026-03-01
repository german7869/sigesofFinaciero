<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    /**
     * Ignore missing .env so tests rely only on phpunit.xml env values.
     */
    protected $loadEnvironmentVariables = false;
}
