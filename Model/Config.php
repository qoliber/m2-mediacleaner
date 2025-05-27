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

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class Config
{
    /**
     * @var string
     */
    public const ENABLED = 'qoliber_media_cleaner/settings/enabled';
    /**
     * @var string
     */
    public const CRON_ENABLED = 'qoliber_media_cleaner/settings/cron_enabled';
    /**
     * @var string
     */
    public const CRON_EXPRESSION = 'qoliber_media_cleaner/settings/cron_expression';
    /**
     * @var string
     */
    public const CLEAN_CACHE_DIRECTORY = 'qoliber_media_cleaner/settings/clean_cache_directory';

    /**
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        private readonly ScopeConfigInterface $scopeConfig
    ) {
    }

    /**
     * Is Module Enabled
     *
     * @param int $storeId
     *
     * @return bool
     */
    public function isModuleEnabled(int $storeId): bool
    {
        return $this->scopeConfig->isSetFlag(
            self::ENABLED,
            ScopeInterface::SCOPE_STORES,
            $storeId,
        );
    }

    /**
     * Get Cron Expression
     *
     * @return string
     */
    public function getCronExpression(): string
    {
        return (string)$this->scopeConfig->getValue(
            self::CRON_EXPRESSION
        );
    }

    /**
     * Get Is Cron Enabled
     *
     * @param int $storeId
     *
     * @return bool
     */
    public function isCronEnabled(int $storeId): bool
    {
        return $this->scopeConfig->isSetFlag(
            self::CRON_ENABLED,
            ScopeInterface::SCOPE_STORES,
            $storeId,
        );
    }

    /**
     * Should Clean Cache Directory
     *
     * @return bool
     */
    public function shouldCleanCacheDirectory(): bool
    {
        return $this->scopeConfig->isSetFlag(
            self::CLEAN_CACHE_DIRECTORY,
            \Magento\Framework\App\ScopeInterface::SCOPE_DEFAULT,
        );
    }
}
