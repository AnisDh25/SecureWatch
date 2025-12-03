<?php

namespace App\Controller;

use App\Entity\AlertRule;
use App\Form\AlertRuleType;
use App\Repository\AlertRuleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/settings')]
class SettingsController extends AbstractController
{
    public function __construct(
        private AlertRuleRepository $alertRuleRepository,
        private EntityManagerInterface $entityManager
    ) {
    }

    #[Route('/', name: 'app_settings')]
    #[IsGranted('ROLE_USER')]
    public function index(): Response
    {
        $alertRules = $this->alertRuleRepository->findAll();
        
        return $this->render('settings/index.html.twig', [
            'alertRules' => $alertRules,
        ]);
    }

    #[Route('/alert-rule/new', name: 'app_alert_rule_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function newAlertRule(Request $request): Response
    {
        $alertRule = new AlertRule();
        $form = $this->createForm(AlertRuleType::class, $alertRule);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($alertRule);
            $this->entityManager->flush();

            $this->addFlash('success', 'Alert rule created successfully.');

            return $this->redirectToRoute('app_settings');
        }

        return $this->render('settings/alert_rule_new.html.twig', [
            'alertRule' => $alertRule,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/alert-rule/{id}/edit', name: 'app_alert_rule_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function editAlertRule(Request $request, AlertRule $alertRule): Response
    {
        $form = $this->createForm(AlertRuleType::class, $alertRule);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();

            $this->addFlash('success', 'Alert rule updated successfully.');

            return $this->redirectToRoute('app_settings');
        }

        return $this->render('settings/alert_rule_edit.html.twig', [
            'alertRule' => $alertRule,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/alert-rule/{id}/delete', name: 'app_alert_rule_delete', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function deleteAlertRule(Request $request, AlertRule $alertRule): Response
    {
        if ($this->isCsrfTokenValid('delete' . $alertRule->getId(), $request->request->get('_token'))) {
            $this->entityManager->remove($alertRule);
            $this->entityManager->flush();

            $this->addFlash('success', 'Alert rule deleted successfully.');
        }

        return $this->redirectToRoute('app_settings');
    }
}
