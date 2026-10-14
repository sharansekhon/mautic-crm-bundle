<?php

declare(strict_types=1);

namespace MauticPlugin\MautomicCrmBundle\Tests\Unit\Integration;

use libphonenumber\PhoneNumberUtil;
use MauticPlugin\MautomicCrmBundle\Integration\ContactSyncDryRunAnalyzer;
use MauticPlugin\MautomicCrmBundle\Integration\InternationalPhoneNormalizer;
use MauticPlugin\MautomicCrmBundle\Integration\NormalizedPhoneIndex;
use PHPUnit\Framework\TestCase;

final class ContactSyncDryRunAnalyzerTest extends TestCase
{
    public function testCountsNormalizedDuplicatesWithoutExposingPhoneValues(): void
    {
        $normalizer = new InternationalPhoneNormalizer(PhoneNumberUtil::getInstance());
        $analyzer   = new ContactSyncDryRunAnalyzer($normalizer, new NormalizedPhoneIndex($normalizer));

        self::assertSame([
            'contacts_with_phone' => 4,
            'eligible_contacts'   => 3,
            'invalid_contacts'    => 1,
            'duplicate_groups'    => 1,
            'duplicate_contacts'  => 2,
            'multiple_phones'     => 0,
            'safe_candidates'     => 1,
        ], $analyzer->analyze([
            ['id' => 1, 'phone' => '+44 20 7946 0018', 'mobile' => null],
            ['id' => 2, 'phone' => null, 'mobile' => '+442079460018'],
            ['id' => 3, 'phone' => '+14155552671', 'mobile' => null],
            ['id' => 4, 'phone' => '4155552671', 'mobile' => null],
        ]));
    }

    public function testBlocksAContactWithTwoDistinctValidPhones(): void
    {
        $normalizer = new InternationalPhoneNormalizer(PhoneNumberUtil::getInstance());
        $analyzer   = new ContactSyncDryRunAnalyzer($normalizer, new NormalizedPhoneIndex($normalizer));

        $summary = $analyzer->analyze([[
            'id'     => 1,
            'phone'  => '+442079460018',
            'mobile' => '+14155552671',
        ]]);

        self::assertSame(1, $summary['multiple_phones']);
        self::assertSame(0, $summary['safe_candidates']);
    }
}
