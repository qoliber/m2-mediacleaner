<?php
/**
 * Created by Qoliber
 *
 * @category    Qoliber
 * @package     Qoliber_MediaCleaner
 * @author      Lukasz Owczarczuk <lowczarczuk@qoliber.com>
 */

declare(strict_types = 1);

namespace Qoliber\MediaCleaner\Api\Cleaner;

use Magento\Store\Api\Data\StoreInterface;
use Qoliber\MediaCleaner\Model\OutputInfo;

interface CleanerInterface
{
    /**
     * Clean media
     *
     * @param \Magento\Store\Api\Data\StoreInterface $store
     * @param bool $dryRun
     *
     * @return \Qoliber\MediaCleaner\Model\OutputInfo
     * @throws \Qoliber\MediaCleaner\Exception\CleaningException
     */
    public function clean(StoreInterface $store, bool $dryRun): OutputInfo;

    /**
     * Use only in default store.
     *
     * Set to true if you want to use this cleaner only
     * in store view with ID = 0, otherwise set to false.
     *
     * @return bool
     */
    public function useOnlyInDefaultStore(): bool;
}
