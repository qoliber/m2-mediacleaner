<?php
/**
 * Created by Qoliber
 *
 * @category    Qoliber
 * @package     Qoliber_MediaCleaner
 * @author      qoliber <info@qoliber.com>
 */

declare(strict_types = 1);

namespace Qoliber\MediaCleaner\Cron;

use Magento\Store\Model\StoreManagerInterface;
use Qoliber\MediaCleaner\Model\CleanerService;
use Qoliber\MediaCleaner\Model\Config;

class Cleanup
{
    /**
     * @param \Qoliber\MediaCleaner\Model\CleanerService $cleanerService
     * @param \Qoliber\MediaCleaner\Model\Config $config
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        protected CleanerService        $cleanerService,
        protected Config                $config,
        protected StoreManagerInterface $storeManager
    ) {
    }

    /**
     * Execute
     *
     * @return void
     */
    public function execute(): void
    {
        $this->cleanerService->setAllowedStoreIds($this->getStoreIdsAllowedInCron());
        $this->cleanerService->run();
    }

    /**
     * Get store ids allowed in cron
     *
     * @return array<int>
     */
    protected function getStoreIdsAllowedInCron(): array
    {
        $storeIds = [];
        foreach ($this->storeManager->getStores(true) as $store) {
            if ($this->config->isCronEnabled((int)$store->getId())) {
                $storeIds[] = (int)$store->getId();
            }
        }

        return $storeIds;
    }
}
