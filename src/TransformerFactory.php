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

use MackRais\PropertyTransform\Exception\InvalidArgumentException;
use MackRais\PropertyTransform\Exception\NotCallableException;
use Psr\Container\ContainerInterface;

class TransformerFactory
{
    private array $transformerCache = [];

    public function __construct(private readonly ContainerInterface $container)
    {
    }

    public function create(object|string|array $transformer): callable
    {
        $transformerId = $this->getCallableKey($transformer);

        if (!isset($this->transformerCache[$transformerId])) {
            $this->transformerCache[$transformerId] = function (mixed ...$params) use ($transformer) {
                if ($transformer instanceof \Closure) {
                    return $transformer(...$params);
                }

                if (\is_array($transformer)) {
                    [$class, $method] = $transformer;
                    $object = \is_object($class) ? $class : $this->container->get($class);
                    if (null === $object) {
                        $object = new $class();
                    }
                    /** @var object $object */
                    $reflection = new \ReflectionMethod($object, $method);
                    $dependencies = [];
                    $paramsQueue = $params;

                    foreach ($reflection->getParameters() as $parameter) {
                        $type = $parameter->getType();
                        if (!$type instanceof \ReflectionNamedType) {
                            continue;
                        }

                        if (!$type->isBuiltin()) {
                            $dependencies[] = $this->container->get($type->getName());
                        } elseif (!empty($paramsQueue)) {
                            $dependencies[] = array_shift($paramsQueue);
                        }
                    }

                    return $reflection->invoke($object, ...$dependencies);
                }

                if (!\is_callable($transformer)) {
                    throw new NotCallableException('Provided transformer is not callable.');
                }

                return $transformer(...$params);
            };
        }

        return $this->transformerCache[$transformerId];
    }

    private function getCallableKey(object|string|array $transformer): string
    {
        if (\is_string($transformer)) {
            return $transformer;
        }

        if (\is_array($transformer)) {
            if (2 !== \count($transformer)) {
                throw new InvalidArgumentException('Array transformer must have exactly two elements: [object/class, method].');
            }

            [$objectOrClass, $method] = $transformer;

            if (!\is_string($method)) {
                throw new InvalidArgumentException('The second element of the array transformer must be a method name (string).');
            }

            if (\is_object($objectOrClass)) {
                return spl_object_hash($objectOrClass).'::'.$method;
            }

            if (\is_string($objectOrClass) && class_exists($objectOrClass)) {
                return $objectOrClass.'::'.$method;
            }

            throw new InvalidArgumentException('The first element of the array transformer must be an object or a valid class name.');
        }

        return spl_object_hash($transformer);
    }
}
