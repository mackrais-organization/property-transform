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

namespace MackRais\PropertyTransform;

#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::IS_REPEATABLE)]
class Transform
{
    private string|array $transformer;
    private array $params;

    public function __construct(string|array $transformer = '', array ...$params)
    {
        $this->transformer = $transformer;
        $this->params = $params;
    }

    public function getTransformer(): string|array
    {
        return $this->transformer;
    }

    public function getParams(): array
    {
        return $this->params;
    }
}
