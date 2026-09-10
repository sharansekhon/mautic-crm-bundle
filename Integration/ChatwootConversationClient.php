<?php

declare(strict_types=1);

namespace MauticPlugin\MautomicCrmBundle\Integration;

use Symfony\Contracts\HttpClient\Exception\ExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class ChatwootConversationClient
{
    public function __construct(private readonly HttpClientInterface $httpClient, private readonly ChatwootSettings $settings) {}

    public function forMauticUser(int $userId, bool $showAll = false): array
    {
        $agentId = $this->settings->agentMap()[$userId] ?? null;
        $baseUrl = $this->settings->baseUrl();
        $token = $this->settings->apiToken();
        $accountId = $this->settings->accountId();
        if (!$showAll && null === $agentId) return ['conversations' => [], 'status' => 'unmapped'];
        if (null === $baseUrl || null === $token || null === $accountId) return ['conversations' => [], 'status' => 'unavailable'];

        try {
            $response = $this->httpClient->request('GET', sprintf('%s/api/v1/accounts/%d/conversations', rtrim($baseUrl, '/'), $accountId), ['headers' => ['api_access_token' => $token], 'query' => ['assignee_type' => 'all', 'status' => 'all'], 'timeout' => 10]);
            if (200 !== $response->getStatusCode()) return ['conversations' => [], 'status' => 'unavailable'];
            $body = $response->toArray(false);
        } catch (ExceptionInterface) {
            return ['conversations' => [], 'status' => 'unavailable'];
        }

        $conversations = [];
        foreach (($body['data']['payload'] ?? []) as $item) {
            if (is_array($item) && ($showAll || $agentId === (int) ($item['meta']['assignee']['id'] ?? 0))) {
                $conversations[] = ['id' => (int) ($item['id'] ?? 0), 'contact_name' => (string) ($item['meta']['sender']['name'] ?? ''), 'status' => (string) ($item['status'] ?? ''), 'last_activity_at' => (int) ($item['last_activity_at'] ?? 0)];
            }
        }
        return ['conversations' => $conversations, 'status' => 'ok'];
    }
}
