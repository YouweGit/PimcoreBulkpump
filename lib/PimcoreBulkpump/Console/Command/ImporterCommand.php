<?php

namespace PimcoreBulkpump\Console\Command;

use Pimcore\Console;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;

class ImporterCommand extends Console\AbstractCommand
{
    /** @var mixed|null|\Zend_Db_Adapter_Abstract  */
    private $db = null;

    public function __construct()
    {
        $this->db = \Pimcore\Db::get();

        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('bulkpump:import')
            ->setDescription('Import by profile id')
            ->addOption('profileId', 'p', InputOption::VALUE_REQUIRED, 'The id of the profile you want to import', null);


    }


    protected function execute(InputInterface $input, OutputInterface $output)
    {

    }

}