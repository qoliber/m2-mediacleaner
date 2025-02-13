<?php
/**
 * Created by Qoliber
 *
 * @category    Qoliber
 * @package     Qoliber_MediaCleaner
 * @author      Lukasz Owczarczuk <lowczarczuk@qoliber.com>
 */

declare(strict_types = 1);

namespace Qoliber\MediaCleaner\Api;

interface CleanerProviderInterface
{
    /**
     * Gets list of cleaner instances
     *
     * @return \Qoliber\MediaCleaner\Api\Cleaner\CleanerInterface[]
     */
    public function getCleaners(): array;
}
