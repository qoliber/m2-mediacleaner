<?php
/**
 * Created by Qoliber
 *
 * @category    Qoliber
 * @package     Qoliber_MediaCleaner
 * @author      Lukasz Owczarczuk <lowczarczuk@qoliber.com>
 */

declare(strict_types = 1);

namespace Qoliber\MediaCleaner\Command;

use Qoliber\MediaCleaner\Model\CleanerService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class CleanMedia extends Command
{
    /** @var string */
    protected const STORE_ID = 'store_id';
    /** @var string */
    protected const DRY_RUN = 'dry-run';

    /**
     * @param \Qoliber\MediaCleaner\Model\CleanerService $cleanerService
     * @param string|null $name
     */
    public function __construct(
        protected CleanerService $cleanerService,
        ?string                  $name = null
    ) {
        parent::__construct($name);
    }

    /**
     * Configure
     *
     * @return void
     */
    protected function configure(): void
    {
        $this->setName('qoliber:media:clean');
        $this->setDescription('Clean media.');
        $this->addOption(
            self::STORE_ID,
            null,
            InputOption::VALUE_IS_ARRAY | InputOption::VALUE_OPTIONAL,
            'Store ID. If not set, all stores will be processed. 
            Store ID = 0 is always included. 
            The option accepts multiple values (e.g. --store_id=1 --store_id=2)'
        );
        $this->addOption(
            self::DRY_RUN,
            null,
            InputOption::VALUE_NONE,
            'Use this flag to run in dry run mode - without removing the files'
        );

        parent::configure();
    }

    /**
     * Execute the command
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int
     * @throws \Qoliber\MediaCleaner\Exception\CleaningException
     */
    protected function execute(
        InputInterface  $input,
        OutputInterface $output
    ): int {
        $storeIds = $input->getOption(self::STORE_ID);
        $storeIds = array_map('intval', $storeIds);

        if (!in_array(0, $storeIds)) {
            $storeIds[] = 0;
        }
        $dryRun = $input->getOption(self::DRY_RUN);

        $this->cleanerService->setAllowedStoreIds($storeIds);
        $outputInfo = $this->cleanerService->run($dryRun);
        if ($dryRun) {
            $output->writeln(sprintf("<info>Files to remove: %d\n</info>", $outputInfo->getFilesCount()));
            $output->writeln(sprintf("<info>Possible disk space to recover: %.2f GB (%d bytes)\n</info>",
                $outputInfo->getSizeInGb(), $outputInfo->getTotalSize()));
        } else {
            $output->writeln(sprintf("<info>Removed files count: %d\n</info>",
                $outputInfo->getFilesCount()));
            $output->writeln(sprintf("<info>Recovered disk space: %.2f GB (%d bytes)\n</info>",
                $outputInfo->getSizeInGb(), $outputInfo->getTotalSize()));
        }

        return static::SUCCESS;
    }
}
