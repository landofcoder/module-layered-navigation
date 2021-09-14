<?php
/**
 * Landofcoder
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * https://landofcoder.com/license
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category   Landofcoder
 * @package    Lof_LayeredNavigation
 * @copyright  Copyright (c) 2021 Landofcoder (https://landofcoder.com/)
 * @license    https://landofcoder.com/LICENSE-1.0.html
 */

namespace Lof\LayeredNavigation\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Magento\Framework\App\Filesystem\DirectoryList;

class UpdateStockFilter extends Command
{

    const NAME_ARGUMENT = "name";
    const NAME_OPTION = "option";

    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $_resource;
    protected $_cronService;

    /**
     * Constructor.
     *
     * @param \Magento\Framework\App\ResourceConnection $resource 
     * @param \Lof\LayeredNavigation\Cron\UpdateStockFilter $cronService
     * 
     */
    public function __construct(
        \Magento\Framework\App\ResourceConnection $resource,
        \Lof\LayeredNavigation\Cron\UpdateStockFilter $cronService
        ) {
        $this->_resource = $resource;
        $this->_cronService = $cronService;
        parent::__construct();
        
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(
        InputInterface $input,
        OutputInterface $output
    ) {

        try {
            $results = $this->_cronService->execute();
            $output->writeln("Updated Stock Filter sucessfully.");
        } catch (\Exception $e) {
            $output->writeln("Some issues when run update stock filter.");
            $output->writeln("Trace:");
            $output->writeln($e->getMessage());
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName("lof_layerednavigation:updateStock");
        $this->setDescription("Update Stock Filter for Layered Navigation.");
        $this->setDefinition([
            new InputArgument(self::NAME_ARGUMENT, InputArgument::OPTIONAL, "Name"),
            new InputOption(self::NAME_OPTION, "-a", InputOption::VALUE_NONE, "Option functionality")
        ]);
        parent::configure();
    }
}
