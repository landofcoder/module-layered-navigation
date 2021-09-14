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
use Lof\LayeredNavigation\Setup\InstallData;
use Lof\LayeredNavigation\Helper\StockFilter;
use Lof\LayeredNavigation\Model\Config\Source\Options;

class SetStockFilter extends Command
{

    const NAME_ARGUMENT = "name";
    const NAME_OPTION = "option";

    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $_resource;
    protected $_cronService;
    /**
     * @var StockFilter
     */
    protected $stockFilterHelper;

    /**
     * Constructor.
     *
     * @param \Magento\Framework\App\ResourceConnection $resource 
     * @param StockFilter $stockFilterHelper
     * 
     */
    public function __construct(
        \Magento\Framework\App\ResourceConnection $resource,
        StockFilter $stockFilterHelper
        ) {
        $this->_resource = $resource;
        $this->stockFilterHelper = $stockFilterHelper;
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
            $results = $this->stockFilterHelper->setAttributeData(Options::INSTOCK_ID, true);
            $output->writeln("Updated Stock Filter for all Products sucessfully.");
        } catch (\Exception $e) {
            $output->writeln("Some issues when run update stock filter for products.");
            $output->writeln("Trace:");
            $output->writeln($e->getMessage());
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName("lof_layerednavigation:setStock");
        $this->setDescription("Update Stock Filter for Products.");
        $this->setDefinition([
            new InputArgument(self::NAME_ARGUMENT, InputArgument::OPTIONAL, "Name"),
            new InputOption(self::NAME_OPTION, "-a", InputOption::VALUE_NONE, "Option functionality")
        ]);
        parent::configure();
    }
}
