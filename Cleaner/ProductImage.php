<?php
/**
 * Created by Qoliber
 *
 * @category    Qoliber
 * @package     Qoliber_MediaCleaner
 * @author      Lukasz Owczarczuk <lowczarczuk@qoliber.com>
 */

declare(strict_types = 1);

namespace Qoliber\MediaCleaner\Cleaner;

use FilesystemIterator;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Select;
use Magento\Framework\Filesystem;
use Magento\Store\Api\Data\StoreInterface;
use Qoliber\MediaCleaner\Api\Cleaner\CleanerInterface;
use Qoliber\MediaCleaner\Model\Config;
use Qoliber\MediaCleaner\Model\OutputInfo;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class ProductImage implements CleanerInterface
{
    /**
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \Magento\Framework\App\ResourceConnection $resourceConnection
     * @param \Qoliber\MediaCleaner\Model\Config $config
     */
    public function __construct(
        protected Filesystem         $filesystem,
        protected ResourceConnection $resourceConnection,
        protected Config             $config
    ) {
    }

    /**
     * Clean product images
     *
     * @param \Magento\Store\Api\Data\StoreInterface $store
     * @param bool $dryRun
     *
     * @return \Qoliber\MediaCleaner\Model\OutputInfo
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function clean(StoreInterface $store, bool $dryRun): OutputInfo
    {
        $writer = $this->filesystem->getDirectoryWrite(DirectoryList::MEDIA);
        $imageDir =
            $writer->getAbsolutePath() . 'catalog' . DIRECTORY_SEPARATOR
            . 'product';
        $connection = $this->resourceConnection->getConnection();
        $directoryIterator = new RecursiveDirectoryIterator(
            $imageDir,
            FilesystemIterator::SKIP_DOTS | FilesystemIterator::FOLLOW_SYMLINKS
        );
        $files = new RecursiveIteratorIterator(
            $directoryIterator,
            RecursiveIteratorIterator::CHILD_FIRST
        );
        $imagesToKeep = $connection->fetchCol($this->getMediaSql());

        $outputInfo = new OutputInfo();

        /** @var \SplFileInfo $file */
        foreach ($files as $file) {
            if ($file->isDir()) {
                continue;
            }
            $filePath = $file->getPathname();
            $filePath = str_replace($imageDir, '', $filePath);
            if (empty($filePath)
                || str_contains($file->getPathname(), '/cache')
                || str_contains($file->getPathname(), '/placeholder')
                || in_array($filePath, $imagesToKeep, true)) {
                continue;
            }

            $outputInfo->add($file);
            if (!$dryRun) {
                $writer->delete($file->getPathname());
            }
        }

        return $outputInfo;
    }

    /**
     * Get media gallery SQL
     *
     * @return \Magento\Framework\DB\Select
     */
    protected function getMediaSql(): Select
    {
        $table = $this->resourceConnection->getTableName(
            'catalog_product_entity_media_gallery'
        );

        return $this->resourceConnection->getConnection()->select()->from(
            $table,
            ['value']
        );
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
