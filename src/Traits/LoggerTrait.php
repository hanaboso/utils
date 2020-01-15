<?php declare(strict_types=1);

namespace Hanaboso\Utils\Traits;

use Psr\Log\LoggerInterface;

/**
 * Trait LoggerTrait
 *
 * @package Hanaboso\Utils\Traits
 */
trait LoggerTrait
{

    /**
     * @var LoggerInterface
     */
    protected LoggerInterface $logger;

    /**
     * @param LoggerInterface $logger
     */
    public function setLogger(LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }

}
