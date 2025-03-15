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

namespace MackRais\PropertyTransform\Tests\Dummy;

use MackRais\PropertyTransform\Transform;

class DummyDto
{
    #[Transform('trim')]
    #[Transform('strtolower')]
    public string $name;

    #[Transform]
    public DummyNestedDto $nested;
}
