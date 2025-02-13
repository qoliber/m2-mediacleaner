<?php
/**
 * Created by Qoliber
 *
 * @category    Qoliber
 * @package     Qoliber_MediaCleaner
 * @author      Lukasz Owczarczuk <lowczarczuk@qoliber.com>
 */

declare(strict_types = 1);

namespace Qoliber\MediaCleaner\Model;

use Qoliber\MediaCleaner\Api\Cleaner\CleanerInterface;
use Qoliber\MediaCleaner\Api\CleanerProviderInterface;

class CleanerProvider implements CleanerProviderInterface
{
    /**
     * @param array<mixed> $cleaners
     */
    public function __construct(protected array $cleaners = [])
    {
        foreach ($this->cleaners as $i => $cleaner) {
            if (!$cleaner instanceof CleanerInterface) {
                unset($this->cleaners[$i]);
            }
        }
    }

    /**
     * Gets list of cleaner instances
     *
     * @return \Qoliber\MediaCleaner\Api\Cleaner\CleanerInterface[]
     */
    public function getCleaners(): array
    {
        return $this->cleaners;
    }
}
