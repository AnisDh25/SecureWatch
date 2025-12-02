<?php

namespace App\Controller;

use App\Entity\Incident;
use App\Repository\IncidentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/incidents')]
#[IsGranted('ROLE_USER')]
class IncidentController extends AbstractController
{
    #[Route('/', name: 'app_incidents')]
    public function index(Request $request, IncidentRepository $incidentRepository): Response
    {
        $severity = $request->query->get('severity', 'all');
        $status = $request->query->get('status', 'all');
        
        // Build query with filters
        $qb = $incidentRepository->createQueryBuilder('i');
        
        if ($severity !== 'all') {
            $qb->andWhere('i.severity = :severity')
               ->setParameter('severity', $severity);
        }
        
        if ($status !== 'all') {
            $qb->andWhere('i.status = :status')
               ->setParameter('status', $status);
        }
        
        $incidents = $qb->orderBy('i.openedAt', 'DESC')
                         ->getQuery()
                         ->getResult();
        
        return $this->render('incident/index.html.twig', [
            'incidents' => $incidents,
            'currentSeverity' => $severity,
            'currentStatus' => $status,
        ]);
    }

    #[Route('/{id}', name: 'app_incident_show')]
    public function show(Incident $incident): Response
    {
        return $this->render('incident/show.html.twig', [
            'incident' => $incident,
        ]);
    }

    #[Route('/{id}/close', name: 'app_incident_close', methods: ['POST'])]
    public function close(Request $request, Incident $incident, IncidentRepository $incidentRepository): Response
    {
        if ($this->isCsrfTokenValid('close'.$incident->getId(), $request->request->get('_token'))) {
            $incident->close();
            $incidentRepository->save($incident, true);
        }

        return $this->redirectToRoute('app_incidents');
    }
}
