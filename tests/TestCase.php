<?php

namespace Govigilant\StatamicGptSpellCorrector\Tests;

use Govigilant\StatamicGptSpellCorrector\ServiceProvider;
use Statamic\Testing\AddonTestCase;

abstract class TestCase extends AddonTestCase
{
    protected string $addonServiceProvider = ServiceProvider::class;
}
