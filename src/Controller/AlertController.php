<?php

namespace App\Controller;

use App\Entity\Alert;
use App\Repository\AlertRepository;
use Doctrine\ORM\EntityManagerInterface;
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

    #[Route('/{id}/resolve', name: 'app_alert_resolve', methods: ['POST'])]
    public function resolve(Request $request, Alert $alert, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('resolve'.$alert->getId(), $request->request->get('_token'))) {
            $alert->setStatus('resolved');
            $entityManager->persist($alert);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_alerts');
    }
}
