<?php

namespace FcfVendor\WPDesk\Composer\Codeception\Commands;

use FcfVendor\Symfony\Component\Console\Input\InputArgument;
use FcfVendor\Symfony\Component\Console\Input\InputInterface;
use FcfVendor\Symfony\Component\Console\Output\OutputInterface;
/**
 * Codeception tests run command.
 *
 * @package WPDesk\Composer\Codeception\Commands
 */
class RunLocalCodeceptionTests extends \FcfVendor\WPDesk\Composer\Codeception\Commands\RunCodeceptionTests
{
    /**
     * Configure command.
     */
    protected function configure()
    {
        parent::configure();
        $this->setName('run-local-codeception-tests')->setDescription('Run local codeception tests.')->setDefinition(array(new \FcfVendor\Symfony\Component\Console\Input\InputArgument(self::SINGLE, \FcfVendor\Symfony\Component\Console\Input\InputArgument::OPTIONAL, 'Name of Single test to run.', ' '), new \FcfVendor\Symfony\Component\Console\Input\InputArgument(self::WOOCOMMERCE_VERSION, \FcfVendor\Symfony\Component\Console\Input\InputArgument::OPTIONAL, 'WooCommerce version to install.', '')));
    }
    /**
     * Execute command.
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return void
     */
    protected function execute(\FcfVendor\Symfony\Component\Console\Input\InputInterface $input, \FcfVendor\Symfony\Component\Console\Output\OutputInterface $output)
    {
        $singleTest = $input->getArgument(self::SINGLE);
        $wooVersion = $input->getArgument(self::WOOCOMMERCE_VERSION);
        $runLocalTests = 'sh ./vendor/wpdesk/wp-codeception/scripts/run_local_tests.sh ' . $singleTest . ' ' . $wooVersion;
        $this->execAndOutput($runLocalTests, $output);
    }
}
