<?php

declare(strict_types=1);

namespace MauticPlugin\MautomicCrmBundle\Integration;

final class ChatwootSettings
{
    public function assignmentEnabled(): bool
    {
        return filter_var($this->all()['CHATWOOT_ASSIGNMENT_ENABLED'] ?? false, FILTER_VALIDATE_BOOL);
    }

    public function baseUrl(): ?string
    {
        return $this->nonEmpty('CHATWOOT_BASE_URL');
    }

    public function apiToken(): ?string
    {
        return $this->nonEmpty('CHATWOOT_API_TOKEN');
    }

    public function accountId(): ?int
    {
        $value = $this->nonEmpty('CHATWOOT_ACCOUNT_ID');

        return null !== $value && ctype_digit($value) ? (int) $value : null;
    }

    /** @return array<int, int> */
    public function agentMap(): array
    {
        $settings = $this->all();
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

    /** @return array<string, mixed> */
    private function all(): array
    {
        $settings = @parse_ini_file('/etc/mautic/chatwoot.env', false, INI_SCANNER_RAW);

        return is_array($settings) ? $settings : [];
    }

    private function nonEmpty(string $key): ?string
    {
        $value = trim((string) ($this->all()[$key] ?? ''));

        return '' === $value ? null : $value;
    }
}
