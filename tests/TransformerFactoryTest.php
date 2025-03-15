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

use MackRais\PropertyTransform\Exception\InvalidArgumentException;
use MackRais\PropertyTransform\Tests\Dummy\DummyTransformer;
use MackRais\PropertyTransform\TransformerFactory;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

class TransformerFactoryTest extends TestCase
{
    private TransformerFactory $factory;
    private ContainerInterface&MockObject $container;

    protected function setUp(): void
    {
        $this->container = $this->createMock(ContainerInterface::class);
        $this->factory = new TransformerFactory($this->container);
    }

    #[Test]
    public function itShouldCreateCallableForArrayTransformer(): void
    {
        $transformer = [new DummyTransformer(), 'transform'];

        $callable = $this->factory->create($transformer);

        $this->assertIsCallable($callable);
        $this->assertEquals('TRANSFORMED: hello', $callable('hello'));
    }

    #[Test]
    public function itShouldCreateCallableForClassAndMethodTransformer(): void
    {
        $this->container
            ->method('get')
            ->willReturn(new DummyTransformer());

        $transformer = [DummyTransformer::class, 'transform'];

        $callable = $this->factory->create($transformer);

        $this->assertIsCallable($callable);
        $this->assertEquals('TRANSFORMED: hello', $callable('hello'));
    }

    #[Test]
    public function itShouldThrowExceptionForNonCallableStringTransformer(): void
    {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('Provided transformer is not callable.');

        $transformer = 'non_existent_function';

        $callable = $this->factory->create($transformer);
        $callable();
    }

    #[Test]
    public function itShouldThrowExceptionForInvalidArrayTransformerSize(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Array transformer must have exactly two elements: [object/class, method].');

        $this->factory->create([DummyTransformer::class]);
    }

    #[Test]
    public function itShouldThrowExceptionForInvalidArrayMethod(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The second element of the array transformer must be a method name (string).');

        $this->factory->create([DummyTransformer::class, 123]);
    }

    #[Test]
    public function itShouldThrowExceptionForInvalidArrayClass(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The first element of the array transformer must be an object or a valid class name.');

        $this->factory->create(['NonExistentClass', 'transform']);
    }

    #[Test]
    public function itShouldCacheTransformers(): void
    {
        $transformer = [new DummyTransformer(), 'transform'];

        $firstCallable = $this->factory->create($transformer);
        $secondCallable = $this->factory->create($transformer);

        $this->assertSame($firstCallable, $secondCallable, 'Transformer should be cached.');
    }

    #[Test]
    public function itShouldInstantiateClassWithoutContainer(): void
    {
        $this->factory = new TransformerFactory(new class implements ContainerInterface {
            public function get(string $id)
            {
                return null;
            }

            public function has(string $id): bool
            {
                return false;
            }
        });

        $transformer = [DummyTransformer::class, 'transform'];

        $callable = $this->factory->create($transformer);

        $this->assertIsCallable($callable);
        $this->assertEquals('TRANSFORMED: test', $callable('test'));
    }

    #[Test]
    public function itShouldSupportAnonymousFunctionAsTransformer(): void
    {
        $transformer = fn ($value) => strtoupper($value);

        $callable = $this->factory->create($transformer);

        $this->assertIsCallable($callable);
        $this->assertEquals('HELLO', $callable('hello'));
    }

    #[Test]
    public function itShouldThrowExceptionForArrayWithInvalidObject(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The first element of the array transformer must be an object or a valid class name.');

        $this->factory->create([42, 'transform']);
    }

    #[Test]
    public function itShouldFailWhenRequiredArgumentsAreMissing(): void
    {
        $anonymousClass = new class {
            public function transform(string $value, int $number): string
            {
                return "TRANSFORMED: $value - $number";
            }
        };

        $transformer = [$anonymousClass, 'transform'];

        $callable = $this->factory->create($transformer);

        $this->expectException(\ArgumentCountError::class);
        $callable();
    }

    #[Test]
    public function itShouldIgnoreParameterWithoutTypeInMethod(): void
    {
        $anonymousClass = new class {
            // @phpstan-ignore-next-line
            public function transform($value = null): int
            {
                return \func_num_args();
            }
        };

        $transformer = [$anonymousClass, 'transform'];

        $callable = $this->factory->create($transformer);

        $this->assertIsCallable($callable);

        $this->assertEquals(0, $callable('hello'));
    }
}
