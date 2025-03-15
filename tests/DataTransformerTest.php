<?php

/**
 * @author     Oleh Boiko <developer@mackrais.com>
 *
 * @site       https://mackrais.com
 *
 * @copyright  2014-2025 MackRais
 *
 * @date: 15.03.25
 */

declare(strict_types=1);

namespace MackRais\PropertyTransform\Tests;

use MackRais\PropertyTransform\DataTransformer;
use MackRais\PropertyTransform\Tests\Dummy\DummyCustomTransformerDto;
use MackRais\PropertyTransform\Tests\Dummy\DummyDto;
use MackRais\PropertyTransform\Tests\Dummy\DummyDtoWithArray;
use MackRais\PropertyTransform\Tests\Dummy\DummyNestedDto;
use MackRais\PropertyTransform\TransformerFactory;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

final class DataTransformerTest extends TestCase
{
    private DataTransformer $dataTransformer;
    private TransformerFactory $transformerFactory;
    private ContainerInterface&MockObject $container;

    protected function setUp(): void
    {
        $this->container = $this->createMock(ContainerInterface::class);
        $this->transformerFactory = new TransformerFactory($this->container);
        $this->dataTransformer = new DataTransformer($this->transformerFactory);
    }

    protected function tearDown(): void
    {
        unset($this->dataTransformer, $this->transformerFactory, $this->container);
    }

    #[Test]
    public function itTransformsStringPropertiesWithMultipleTransformers(): void
    {
        $dto = new DummyDto();
        $dtoNested = new DummyNestedDto();
        $dtoNested->address = ' Test Address ';
        $dto->nested = $dtoNested;
        $dto->name = '  TestName ';

        $this->dataTransformer->transform($dto);

        $this->assertSame('testname', $dto->name);
        $this->assertSame('test address', $dto->nested->address);
    }

    #[Test]
    public function itTransformsArrayOfObjects(): void
    {
        $dto = new DummyDtoWithArray();
        $dto->items = [
            (object) ['name' => 'Item1'],
            null,
        ];

        $this->dataTransformer->transform($dto);

        $this->assertCount(1, $dto->items);
        $this->assertSame('Item1', $dto->items[0]->name);
    }

    #[Test]
    public function itAppliesCustomTransformers(): void
    {
        $dto = new DummyCustomTransformerDto();
        $dto->name = '<script>alert("Xss attack!!!")</script> safe text';

        $logger = $this->createMock(LoggerInterface::class);

        $this->container->method('get')
            ->willReturnMap([
                [LoggerInterface::class, $logger],
            ]);

        $this->dataTransformer->transform($dto);

        $this->assertSame('alert("Xss attack!!!") safe text', $dto->name);
    }

    #[Test]
    public function itHandlesEmptyArray(): void
    {
        $dto = new DummyDtoWithArray();
        $dto->items = [];

        $this->dataTransformer->transform($dto);

        $this->assertSame([], $dto->items);
    }

    #[Test]
    public function itTransformsArrayOfArrays(): void
    {
        $dto = new DummyDtoWithArray();
        $dto->items = [
            ['name' => 'Item1'],
            null,
        ];

        $this->dataTransformer->transform($dto);

        $this->assertCount(1, $dto->items);
        $this->assertSame('Item1', $dto->items[0]['name']);
    }
}
