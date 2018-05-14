<?php
namespace App\Entity;

use Psr\Log\LoggerInterface;

trait LoggerTrait
{
    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @param LoggerInterface $logger
     * @return $this
     */
    protected function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;

        return $this;
    }
}
