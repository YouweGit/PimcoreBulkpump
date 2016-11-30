<?php

namespace PimcoreBulkpump\Console\Command;

use Pimcore\Console\AbstractCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;


class ImporterCommand extends AbstractCommand
{
    /** @var mixed|null|\Zend_Db_Adapter_Abstract  */
    private $db = null;

    public function __construct()
    {
        $this->db = \Pimcore\Db::get();

        parent::__construct();

        define("PIMCORE_ADMIN", true);
    }

    protected function configure()
    {
        $this->setName('bulkpump:import')
            ->setDescription('Import by profile id')
            ->addOption('profileId', 'p', InputOption::VALUE_REQUIRED, 'The id of the profile you want to import', null);


    }


    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $profileId = $input->getOption('profileId');

        if(!isset($profileId))
        {
            $output->writeln('<info>No profileId has been set</info>');
            exit();
        }

        try {
            \CsvDataMapper_Import_Profile::run($profileId);
        } catch( Exception $e) {
            die($e);
        }
    }

}