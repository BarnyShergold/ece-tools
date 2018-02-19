<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\MagentoCloud\Test\Unit\StaticContent;

use Magento\MagentoCloud\StaticContent\CommandFactory;
use Magento\MagentoCloud\StaticContent\OptionInterface;
use Magento\MagentoCloud\Package\MagentoVersion;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject as Mock;

/**
 * @inheritdoc
 */
class CommandFactoryTest extends TestCase
{
    /**
     * @var MagentoVersion|Mock
     */
    private $magentoVersionMock;

    /**
     * @var CommandFactory
     */
    private $commandFactory;

    public function setUp()
    {
        $this->magentoVersionMock = $this->createMock(MagentoVersion::class);
        $this->commandFactory = new CommandFactory(
            $this->magentoVersionMock
        );
    }

    /**
     * @param array $optionConfig
     * @param bool $useScdStrategy
     * @param string $expected
     * @dataProvider createDataProvider
     */
    public function testCreate(array $optionConfig, bool $useScdStrategy, string $expected)
    {
        $this->magentoVersionMock
            ->expects($this->exactly(1))
            ->method('satisfies')
            ->willReturn(!$useScdStrategy);

        $this->assertEquals(
            $expected,
            $this->commandFactory->create($this->createOption($optionConfig, (int)$useScdStrategy))
        );
    }

    /**
     * @return array
     */
    public function createDataProvider(): array
    {
        return [
            [
                [
                    'thread_count' => 3,
                    'excluded_themes' => ['theme1', 'theme2'],
                    'strategy' => 'quick',
                    'locales' => ['en_US'],
                    'is_force' => true,
                    'verbosity_level' => '-v',
                ],
                true,
                'php ./bin/magento setup:static-content:deploy -f --exclude-theme=theme1 --exclude-theme=theme2 -s ' .
                'quick -v en_US --jobs=3',
            ],
            [
                [
                    'thread_count' => 1,
                    'excluded_themes' => ['theme1'],
                    'strategy' => 'quick',
                    'locales' => ['en_US', 'de_DE'],
                    'is_force' => false,
                    'verbosity_level' => '-v',
                ],
                true,
                'php ./bin/magento setup:static-content:deploy --exclude-theme=theme1 -s quick -v en_US de_DE --jobs=1',
            ],
            [
                [
                    'thread_count' => 3,
                    'excluded_themes' => ['theme1', 'theme2'],
                    'strategy' => 'quick',
                    'locales' => ['en_US'],
                    'is_force' => true,
                    'verbosity_level' => '-v',
                ],
                false,
                'php ./bin/magento setup:static-content:deploy -f --exclude-theme=theme1 --exclude-theme=theme2 ' .
                '-v en_US --jobs=3',
            ],
            [
                [
                    'thread_count' => 1,
                    'excluded_themes' => ['theme1'],
                    'strategy' => 'quick',
                    'locales' => ['en_US', 'de_DE'],
                    'is_force' => false,
                    'verbosity_level' => '-v',
                ],
                false,
                'php ./bin/magento setup:static-content:deploy --exclude-theme=theme1 -v en_US de_DE --jobs=1',
            ],
        ];
    }

    /**
     * @param array $optionConfig
     * @param int $getStrategyTimes
     *
     * @return Mock|OptionInterface
     */
    private function createOption(array $optionConfig, int $getStrategyTimes)
    {
        $optionMock = $this->getMockBuilder(OptionInterface::class)
            ->getMockForAbstractClass();

        if (isset($optionConfig['thread_count'])) {
            $optionMock->expects($this->once())
                ->method('getThreadCount')
                ->willReturn($optionConfig['thread_count']);
        }
        $optionMock->expects($this->once())
            ->method('getExcludedThemes')
            ->willReturn($optionConfig['excluded_themes']);
        $optionMock->expects($this->exactly($getStrategyTimes))
            ->method('getStrategy')
            ->willReturn($optionConfig['strategy']);
        $optionMock->expects($this->once())
            ->method('getLocales')
            ->willReturn($optionConfig['locales']);
        $optionMock->expects($this->once())
            ->method('isForce')
            ->willReturn($optionConfig['is_force']);
        $optionMock->expects($this->once())
            ->method('getVerbosityLevel')
            ->willReturn($optionConfig['verbosity_level']);

        return $optionMock;
    }
}
