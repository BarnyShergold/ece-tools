<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\MagentoCloud\Test\Unit\Docker;

use Magento\MagentoCloud\App\ContainerInterface;
use Magento\MagentoCloud\Docker\Builder;
use Magento\MagentoCloud\Docker\BuilderFactory;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @inheritdoc
 */
class BuilderFactoryTest extends TestCase
{
    /**
     * @var BuilderFactory
     */
    private $builderFactory;

    /**
     * @var ContainerInterface|MockObject
     */
    private $containerMock;

    /**
     * @inheritdoc
     */
    protected function setUp()
    {
        $this->containerMock = $this->getMockForAbstractClass(ContainerInterface::class);

        $this->builderFactory = new BuilderFactory(
            $this->containerMock
        );
    }

    public function testCreate()
    {
        $this->containerMock->expects($this->once())
            ->method('create')
            ->willReturn($this->createMock(Builder::class));

        $this->builderFactory->create();
    }
}