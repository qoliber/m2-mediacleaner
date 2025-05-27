<?php
/**
 * Created by Qoliber
 *
 * @category    Qoliber
 * @package     Qoliber_MediaCleaner
 * @author      qoliber <info@qoliber.com>
 */

declare(strict_types = 1);

namespace Qoliber\MediaCleaner\Cleaner;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Magento\Store\Api\Data\StoreInterface;
use Qoliber\MediaCleaner\Api\Cleaner\CleanerInterface;
use Qoliber\MediaCleaner\Model\Config;
use Qoliber\MediaCleaner\Model\OutputInfo;

class ProductImageCache implements CleanerInterface
{
    /**
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \Qoliber\MediaCleaner\Model\Config $config
     */
    public function __construct(
        protected Filesystem $filesystem,
        protected Config     $config
    ) {}

    /**
     * Clean product images
     *
     *
     * @param \Magento\Store\Api\Data\StoreInterface $store
     * @param bool $dryRun *
     *
     * @return \Qoliber\MediaCleaner\Model\OutputInfo
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function clean(
        StoreInterface $store,
        bool           $dryRun
    ): OutputInfo {
        $outputInfo = new OutputInfo();
        if (!$this->config->shouldCleanCacheDirectory()) {
            return $outputInfo;
        }

        $mainDir = $this->filesystem->getDirectoryWrite(DirectoryList::MEDIA);
        $subPath = 'catalog' . DIRECTORY_SEPARATOR . 'product'
            . DIRECTORY_SEPARATOR . 'cache';

        $outputInfo->add(new \SplFileInfo($mainDir->getAbsolutePath($subPath)));
        if (!$dryRun) {
            $this->filesystem->getDirectoryWrite(DirectoryList::MEDIA)->delete(
                $subPath
                );
        }

        return $outputInfo;
    }

    /**
     * Use only in default store.
     *
     * Set to true if you want to use this cleaner only
     * in store view with ID = 0, otherwise set to false.
     *
     * @return bool
     */
    public function useOnlyInDefaultStore(): bool
    {
        return true;
    }
}
