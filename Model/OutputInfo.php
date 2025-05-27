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

use FilesystemIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileInfo;

class OutputInfo
{
    /** @var int */
    protected int $totalSize = 0;
    /** @var int */
    protected int $totalFiles = 0;

    /**
     * Get size in GB
     *
     * @return float
     */
    public function getSizeInGb(): float
    {
        return round($this->getTotalSize() / (1024 * 1024 * 1024), 2);
    }

    /**
     * Get size in bytes
     *
     * @return int
     */
    public function getTotalSize(): int
    {
        return $this->totalSize;
    }

    /**
     * Get total files count
     *
     * @return int
     */
    public function getFilesCount(): int
    {
        return $this->totalFiles;
    }

    /**
     * Add file
     *
     * @param \SplFileInfo $file
     *
     * @return void
     */
    protected function addFile(SplFileInfo $file): void
    {
        $this->addSize((int)$file->getSize());
        $this->addFilesCount(1);
    }

    /**
     * Add Files Count
     *
     * @param int $additionalCount
     *
     * @return void
     */
    public function addFilesCount(int $additionalCount): void
    {
        $this->totalFiles += $additionalCount;
    }

    /**
     * Add Files Size
     *
     * @param int $additionalSize
     *
     * @return void
     */
    public function addSize(int $additionalSize): void
    {
        $this->totalSize += $additionalSize;
    }

    /**
     * Add path
     *
     * @param \SplFileInfo $path
     *
     * @return void
     */
    public function add(SplFileInfo $path): void
    {
        if ($path->isDir()) {
            $this->addDirectory($path);
        } else if ($path->isFile()) {
            $this->addFile($path);
        }
    }

    /**
     * Add directory
     *
     * @param \SplFileInfo $directory
     *
     * @return void
     */
    protected function addDirectory(SplFileInfo $directory): void
    {
        $it = new RecursiveDirectoryIterator(
            $directory->getPath(), FilesystemIterator::FOLLOW_SYMLINKS
        );
        $ri = new RecursiveIteratorIterator(
            $it, RecursiveIteratorIterator::CHILD_FIRST
        );

        /** @var SplFileInfo $file */
        foreach ($ri as $file) {
            if ($file->isFile()) {
                $this->addFile($file);
            }
        }
    }
}
