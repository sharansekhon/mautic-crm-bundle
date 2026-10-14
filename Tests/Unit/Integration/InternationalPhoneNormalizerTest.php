<?php

declare(strict_types=1);

namespace MauticPlugin\MautomicCrmBundle\Tests\Unit\Integration;

use libphonenumber\PhoneNumberUtil;
use MauticPlugin\MautomicCrmBundle\Integration\InternationalPhoneNormalizer;
use PHPUnit\Framework\TestCase;

final class InternationalPhoneNormalizerTest extends TestCase
{
    private InternationalPhoneNormalizer $normalizer;

    protected function setUp(): void
    {
        $this->normalizer = new InternationalPhoneNormalizer(PhoneNumberUtil::getInstance());
    }

    public function testNormalizesValidInternationalNumbersToE164(): void
    {
        self::assertSame('+442079460018', $this->normalizer->normalize('+44 20 7946 0018'));
        self::assertSame('0018', $this->normalizer->suffix('+44 (0)20 7946 0018'));
    }

    public function testRejectsNationalInvalidAndNonScalarValues(): void
    {
        self::assertNull($this->normalizer->normalize('020 7946 0018'));
        self::assertNull($this->normalizer->normalize('+123'));
        self::assertNull($this->normalizer->normalize(['+442079460018']));
    }
}
