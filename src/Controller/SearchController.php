<?php

namespace App\Controller;

use App\Repository\EventRepository;
use App\Repository\AlertRepository;
use App\Repository\IncidentRepository;
use App\Repository\AssetRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/search')]
#[IsGranted('ROLE_USER')]
class SearchController extends AbstractController
{
    public function __construct(
        private EventRepository $eventRepository,
        private AlertRepository $alertRepository,
        private IncidentRepository $incidentRepository,
        private AssetRepository $assetRepository
    ) {
    }

    #[Route('/', name: 'app_search', methods: ['GET'])]
    public function search(Request $request): JsonResponse
    {
        $query = $request->query->get('q', '');
        $results = [];

        if (strlen($query) >= 2) {
            // Search Events
            $events = $this->eventRepository->findBySearchQuery($query, 5);
            foreach ($events as $event) {
                $results[] = [
                    'id' => $event->getId(),
                    'type' => 'event',
                    'title' => $event->getSource() . ' Event',
                    'description' => substr($event->getData() ?? '', 0, 100),
                    'severity' => $event->getSeverity(),
                    'time' => $this->formatTime($event->getTimestamp()),
                    'url' => $this->generateUrl('app_event_show', ['id' => $event->getId()])
                ];
            }

            // Search Alerts
            $alerts = $this->alertRepository->findBySearchQuery($query, 5);
            foreach ($alerts as $alert) {
                $title = 'Alert #' . $alert->getId();
                if ($alert->getAlertRule()) {
                    $title = $alert->getAlertRule()->getName();
                }
                
                $description = 'Severity: ' . $alert->getSeverity() . ', Status: ' . $alert->getStatus();
                if ($alert->getEvent()) {
                    $description .= ', Source: ' . $alert->getEvent()->getSource();
                }
                
                $results[] = [
                    'id' => $alert->getId(),
                    'type' => 'alert',
                    'title' => $title,
                    'description' => substr($description, 0, 100),
                    'severity' => $alert->getSeverity(),
                    'time' => $this->formatTime($alert->getCreatedAt()),
                    'url' => $this->generateUrl('app_alert_show', ['id' => $alert->getId()])
                ];
            }

            // Search Incidents
            $incidents = $this->incidentRepository->findBySearchQuery($query, 5);
            foreach ($incidents as $incident) {
                $results[] = [
                    'id' => $incident->getId(),
                    'type' => 'incident',
                    'title' => 'Incident #' . $incident->getId(),
                    'description' => substr($incident->getNotes() ?? '', 0, 100),
                    'severity' => $incident->getSeverity(),
                    'time' => $this->formatTime($incident->getOpenedAt()),
                    'url' => $this->generateUrl('app_incident_show', ['id' => $incident->getId()])
                ];
            }

            // Search Assets
            $assets = $this->assetRepository->findBySearchQuery($query, 5);
            foreach ($assets as $asset) {
                $results[] = [
                    'id' => $asset->getId(),
                    'type' => 'asset',
                    'title' => $asset->getHostname(),
                    'description' => $asset->getType() . ' - ' . $asset->getIp(),
                    'severity' => null,
                    'time' => 'Asset',
                    'url' => $this->generateUrl('app_asset_show', ['id' => $asset->getId()])
                ];
            }

            // Sort results by relevance (simple sort - prioritize exact matches)
            usort($results, function ($a, $b) use ($query) {
                $aTitle = strtolower($a['title']);
                $bTitle = strtolower($b['title']);
                $queryLower = strtolower($query);

                $aExact = strpos($aTitle, $queryLower) !== false;
                $bExact = strpos($bTitle, $queryLower) !== false;

                if ($aExact && !$bExact) return -1;
                if (!$aExact && $bExact) return 1;

                return 0;
            });
        }

        return new JsonResponse([
            'results' => array_slice($results, 0, 10), // Limit to 10 results
            'query' => $query
        ]);
    }

    private function formatTime(\DateTimeInterface $dateTime): string
    {
        $now = new \DateTime();
        $diff = $now->getTimestamp() - $dateTime->getTimestamp();

        if ($diff < 60) return 'Just now';
        if ($diff < 3600) return floor($diff / 60) . 'm ago';
        if ($diff < 86400) return floor($diff / 3600) . 'h ago';
        if ($diff < 604800) return floor($diff / 86400) . 'd ago';

        return $dateTime->format('M j, Y');
    }
}
