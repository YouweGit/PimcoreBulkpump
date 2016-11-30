<?php

namespace PimcoreBulkpump\Console\Command;

use Pimcore\Console;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

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
            ->setDescription('Import by profile id');
    }


    protected function execute(InputInterface $input, OutputInterface $output)
    {

    }

}