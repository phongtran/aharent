<?php

namespace FcfVendor\WPDesk\Composer\Codeception;

use FcfVendor\WPDesk\Composer\Codeception\Commands\CreateCodeceptionTests;
use FcfVendor\WPDesk\Composer\Codeception\Commands\RunCodeceptionTests;
use FcfVendor\WPDesk\Composer\Codeception\Commands\RunLocalCodeceptionTests;
/**
 * Links plugin commands handlers to composer.
 */
class CommandProvider implements \FcfVendor\Composer\Plugin\Capability\CommandProvider
{
    public function getCommands()
    {
        return [new \FcfVendor\WPDesk\Composer\Codeception\Commands\CreateCodeceptionTests(), new \FcfVendor\WPDesk\Composer\Codeception\Commands\RunCodeceptionTests(), new \FcfVendor\WPDesk\Composer\Codeception\Commands\RunLocalCodeceptionTests()];
    }
}
