<?php

namespace PimcoreBulkpump\Console\Command;

use Pimcore\Console;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ImportListCommand extends Console\AbstractCommand
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
        $this->setName('bulkpump:list')
            ->setDescription('Shows the profiles that can be imported');
    }


    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $model = new \CsvImport_Mapper_Profile();
        $profiles = $model->read()->toArray();

        $line = array();
        $line[] = "Following profiles are available";

        foreach ($profiles as $profile)
        {
            $line[] = str_pad($profile['id'], 4," " ,STR_PAD_LEFT).' | '.$profile['profile_name'];
        }
        $line[] = "\n";

        $output->writeln($line);
    }

}
