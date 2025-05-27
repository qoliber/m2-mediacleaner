<?php
/**
 * Created by Qoliber
 *
 * @category    Qoliber
 * @package     Qoliber_MediaCleaner
 * @author      qoliber <info@qoliber.com>
 */

declare(strict_types = 1);

namespace Qoliber\MediaCleaner\Model;

use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use Qoliber\MediaCleaner\Api\CleanerProviderInterface;

class CleanerService
{
    /**
     * @var array<int>
     */
    protected array $allowedStoreIds = [];
    /**
     * @var null|\Magento\Store\Api\Data\StoreInterface[]
     */
    protected ?array $stores = null;
    /** @var \Qoliber\MediaCleaner\Model\OutputInfo|null */
    private ?OutputInfo $outputInfo = null;

    /**
     * @param \Qoliber\MediaCleaner\Model\Config $config
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Qoliber\MediaCleaner\Api\CleanerProviderInterface $cleanerProvider
     */
    public function __construct(
        protected Config                   $config,
        protected StoreManagerInterface    $storeManager,
        protected CleanerProviderInterface $cleanerProvider
    ) {
    }

    /**
     * Set allowed store ids
     *
     * @param array<int> $allowedStoreIds
     *
     * @return void
     */
    public function setAllowedStoreIds(array $allowedStoreIds): void
    {
        $this->allowedStoreIds = $allowedStoreIds;
        $this->setStores();
    }

    /**
     * Run
     *
     * @param bool $dryRun
     *
     * @return \Qoliber\MediaCleaner\Model\OutputInfo
     * @throws \Qoliber\MediaCleaner\Exception\CleaningException
     */
    public function run(bool $dryRun = false): OutputInfo
    {
        $this->outputInfo = new OutputInfo();
        foreach ($this->getStores() as $store) {
            $this->runForStore($store, $dryRun);
        }

        return $this->outputInfo;
    }

    /**
     * Get stores
     *
     * @return \Magento\Store\Api\Data\StoreInterface[]
     */
    protected function getStores(): array
    {
        if (null === $this->stores) {
            $this->stores = $this->storeManager->getStores(true);
        }

        return $this->stores;
    }

    /**
     * Set stores
     *
     * @return void
     */
    protected function setStores(): void
    {
        $this->stores = $this->storeManager->getStores(true);
        if (empty($this->allowedStoreIds)) {
            return;
        }

        $this->stores = array_filter(
            $this->stores,
            fn($key) => in_array($key, $this->allowedStoreIds),
            ARRAY_FILTER_USE_KEY
        );
    }

    /**
     * Run for store
     *
     * @param \Magento\Store\Api\Data\StoreInterface $store
     * @param bool $dryRun
     *
     * @return void
     * @throws \Qoliber\MediaCleaner\Exception\CleaningException
     */
    protected function runForStore(
        StoreInterface $store,
        bool $dryRun
    ): void {
        if (!$this->config->isModuleEnabled((int)$store->getId())) {
            return;
        }
        foreach ($this->cleanerProvider->getCleaners() as $cleaner) {
            if ($cleaner->useOnlyInDefaultStore()
                && (int)$store->getId() !== Store::DEFAULT_STORE_ID) {
                continue;
            }
            $cleanerOutput = $cleaner->clean($store, $dryRun);
            $this->outputInfo->addSize($cleanerOutput->getTotalSize());
            $this->outputInfo->addFilesCount($cleanerOutput->getFilesCount());
        }
    }
}
