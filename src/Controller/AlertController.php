<?php

namespace App\Controller;

use App\Entity\Alert;
use App\Repository\AlertRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/alerts')]
#[IsGranted('ROLE_USER')]
class AlertController extends AbstractController
{
    #[Route('/', name: 'app_alerts')]
    public function index(Request $request, AlertRepository $alertRepository): Response
    {
        $severity = $request->query->get('severity', 'all');
        $status = $request->query->get('status', 'all');
        
        // Build query with filters
        $qb = $alertRepository->createQueryBuilder('a');
        
        if ($severity !== 'all') {
            $qb->andWhere('a.severity = :severity')
               ->setParameter('severity', $severity);
        }
        
        if ($status !== 'all') {
            $qb->andWhere('a.status = :status')
               ->setParameter('status', $status);
        }
        
        $alerts = $qb->orderBy('a.createdAt', 'DESC')
                    ->getQuery()
                    ->getResult();
        
        return $this->render('alert/index.html.twig', [
            'alerts' => $alerts,
            'currentSeverity' => $severity,
            'currentStatus' => $status,
        ]);
    }

    #[Route('/{id}', name: 'app_alert_show')]
    public function show(Alert $alert): Response
    {
        return $this->render('alert/show.html.twig', [
            'alert' => $alert,
        ]);
    }
}
