<?php

namespace App\Controller;

use App\Entity\Event;
use App\Repository\EventRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/events')]
#[IsGranted('ROLE_USER')]
class EventController extends AbstractController
{
    #[Route('/', name: 'app_events')]
    public function index(Request $request, EventRepository $eventRepository): Response
    {
        $severity = $request->query->get('severity', 'all');
        $source = $request->query->get('source', 'all');
        
        // Build query with filters
        $qb = $eventRepository->createQueryBuilder('e');
        
        if ($severity !== 'all') {
            $qb->andWhere('e.severity = :severity')
               ->setParameter('severity', $severity);
        }
        
        if ($source !== 'all') {
            $qb->andWhere('e.source = :source')
               ->setParameter('source', $source);
        }
        
        $events = $qb->orderBy('e.timestamp', 'DESC')
                    ->getQuery()
                    ->getResult();
        
        // Get available sources for filter dropdown
        $sources = $eventRepository->createQueryBuilder('e')
            ->select('DISTINCT e.source')
            ->getQuery()
            ->getResult();
        
        $sourceOptions = array_map('current', $sources);
        
        return $this->render('event/index.html.twig', [
            'events' => $events,
            'currentSeverity' => $severity,
            'currentSource' => $source,
            'sources' => $sourceOptions,
        ]);
    }

    #[Route('/{id}', name: 'app_event_show')]
    public function show(Event $event): Response
    {
        return $this->render('event/show.html.twig', [
            'event' => $event,
        ]);
    }
}
