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

class DataTransformer
{
    public function __construct(private readonly TransformerFactory $transformerFactory)
    {
    }

    public function transform(object $object): void
    {
        $reflectionClass = new \ReflectionClass($object);

        foreach ($reflectionClass->getProperties() as $property) {
            $attributes = $property->getAttributes(Transform::class);

            if (\count($attributes) > 0) {
                $value = $property->getValue($object);

                if (\is_array($value)) {
                    $value = $this->applyTransformations($value, $attributes);
                    $value = $this->transformArray(\is_array($value) ? $value : [], $attributes);
                } elseif (\is_object($value)) {
                    $this->transform($value);
                } else {
                    $value = $this->applyTransformations($value, $attributes);
                }

                $property->setValue($object, $value);
            }
        }
    }

    private function transformArray(array $array, array $attributes): array
    {
        foreach ($array as &$item) {
            if (\is_object($item)) {
                $this->transform($item);
            } else {
                $item = $this->applyTransformations($item, $attributes);
            }
        }

        return $array;
    }

    private function applyTransformations(mixed $value, array $attributes): mixed
    {
        foreach ($attributes as $attribute) {
            /** @var Transform $transformInstance */
            $transformInstance = $attribute->newInstance();
            $transformer = $this->transformerFactory->create($transformInstance->getTransformer());
            $params = $transformInstance->getParams();

            $value = $transformer($value, ...$params);
        }

        return $value;
    }
}
