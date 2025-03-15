<?php

/**
 * @author     Oleh Boiko <developer@mackrais.com>
 *
 * @version    SVN: $
 *
 * @see       https://mackrais.com
 *
 * @copyright  2014-2025 MackRais
 *
 * @date: 15.03.25
 */

declare(strict_types=1);

namespace MackRais\PropertyTransform\Tests\Dummy;

use Psr\Log\LoggerInterface;

class DummyCustomTransform
{
    public function protectXssAttack(LoggerInterface $logger, ?string $value = null): string
    {
        $logger->info(\sprintf('Protect xss %s', $value));

        return strip_tags((string) $value);
    }
}
