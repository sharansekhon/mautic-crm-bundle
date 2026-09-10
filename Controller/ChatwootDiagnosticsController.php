<?php

declare(strict_types=1);

namespace MauticPlugin\MautomicCrmBundle\Controller;

use Mautic\CoreBundle\Controller\CommonController;
use MauticPlugin\MautomicCrmBundle\Integration\ChatwootDiagnostics;
use Symfony\Component\HttpFoundation\Response;

final class ChatwootDiagnosticsController extends CommonController
{
    public static function getSubscribedServices(): array
    {
        return array_merge(parent::getSubscribedServices(), [ChatwootDiagnostics::class => ChatwootDiagnostics::class]);
    }

    public function indexAction(): Response
    {
        if (!$this->security->isAdmin()) {
            return $this->accessDenied();
        }

        /** @var ChatwootDiagnostics $diagnostics */
        $diagnostics = $this->container->get(ChatwootDiagnostics::class);

        return $this->delegateView([
            'viewParameters' => $diagnostics->inspect(),
            'contentTemplate' => '@MautomicCrm/Settings/chatwoot.html.twig',
            'passthroughVars' => [
                'activeLink' => '#mautomic_crm_settings',
                'mauticContent' => 'mautomicCrmChatwootDiagnostics',
                'route' => $this->generateUrl('mautic_mautomic_crm_chatwoot_diagnostics'),
            ],
        ]);
    }
}
