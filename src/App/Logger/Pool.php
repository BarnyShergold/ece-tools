<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\MagentoCloud\App\Logger;

use Monolog\Handler\HandlerInterface;
use Magento\MagentoCloud\Config\Log as LogConfig;

/**
 * The pool of handlers.
 */
class Pool
{
    /**
     * @var HandlerInterface[]
     */
    private $handlers;

    /**
     * @var LogConfig
     */
    private $logConfig;

    /**
     * @var LineFormatterFactory
     */
    private $lineFormatterFactory;

    /**
     * @var HandlerFactory
     */
    private $handlerFactory;

    /**
     * @param LogConfig $logConfig
     * @param LineFormatterFactory $lineFormatterFactory
     * @param HandlerFactory $handlerFactory
     */
    public function __construct(
        LogConfig $logConfig,
        LineFormatterFactory $lineFormatterFactory,
        HandlerFactory $handlerFactory
    ) {
        $this->logConfig = $logConfig;
        $this->lineFormatterFactory = $lineFormatterFactory;
        $this->handlerFactory = $handlerFactory;
    }

    /**
     * @return HandlerInterface[]
     */
    public function getHandlers(): array
    {
        if (null === $this->handlers) {
            foreach ($this->logConfig->getHandlers() as $handler) {
                $this->handlers[] = $this->handlerFactory->create($handler)
                    ->setFormatter($this->lineFormatterFactory->create());
            }
        }

        return $this->handlers;
    }
}