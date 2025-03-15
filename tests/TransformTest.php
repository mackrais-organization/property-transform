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

use MackRais\PropertyTransform\Transform;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class TransformTest extends TestCase
{
    #[Test]
    public function itShouldCreateTransformAttributeWithDefaultValues(): void
    {
        $transform = new Transform();

        $this->assertSame('', $transform->getTransformer());
        $this->assertSame([], $transform->getParams());
    }

    #[Test]
    public function itShouldCreateTransformAttributeWithStringTransformer(): void
    {
        $transformer = 'string_transformer';
        $transform = new Transform($transformer);

        $this->assertSame($transformer, $transform->getTransformer());
        $this->assertSame([], $transform->getParams());
    }

    #[Test]
    public function itShouldCreateTransformAttributeWithArrayTransformer(): void
    {
        $transformer = ['transformer1', 'transformer2'];
        $transform = new Transform($transformer);

        $this->assertSame($transformer, $transform->getTransformer());
        $this->assertSame([], $transform->getParams());
    }

    #[Test]
    public function itShouldCreateTransformAttributeWithParams(): void
    {
        $transformer = 'string_transformer';
        $params = ['param1' => 'value1', 'param2' => 'value2'];
        $transform = new Transform($transformer, $params);

        $this->assertSame($transformer, $transform->getTransformer());
        $this->assertSame([$params], $transform->getParams());
    }

    #[Test]
    public function itShouldCreateTransformAttributeWithMultipleParams(): void
    {
        $transformer = 'string_transformer';
        $params1 = ['param1' => 'value1'];
        $params2 = ['param2' => 'value2'];
        $transform = new Transform($transformer, $params1, $params2);

        $this->assertSame($transformer, $transform->getTransformer());
        $this->assertSame([$params1, $params2], $transform->getParams());
    }
}
