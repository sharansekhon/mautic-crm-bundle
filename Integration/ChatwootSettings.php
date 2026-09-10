<?php

declare(strict_types=1);

namespace MauticPlugin\MautomicCrmBundle\Integration;

final class ChatwootSettings
{
    /** @return array<int, int> */
    public function agentMap(): array
    {
        $settings = @parse_ini_file('/etc/mautic/chatwoot.env', false, INI_SCANNER_RAW);
        $map      = [];

        if (!is_array($settings)) {
            return $map;
        }

        foreach (explode(',', (string) ($settings['CHATWOOT_AGENT_MAP'] ?? '')) as $pair) {
            $ids = array_map('trim', explode(':', $pair, 2));
            if (2 === count($ids) && ctype_digit($ids[0]) && ctype_digit($ids[1])) {
                $map[(int) $ids[0]] = (int) $ids[1];
            }
        }

        return $map;
    }
}
