<?php

namespace App\Controller;

use App\Entity\Notification;
use App\Repository\NotificationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/notification')]
#[IsGranted('ROLE_USER')]
class NotificationController extends AbstractController
{
    #[Route('/list', name: 'app_notification_list', methods: ['GET'])]
    public function list(NotificationRepository $notificationRepository): JsonResponse
    {
        $user = $this->getUser();
        $notifications = $notificationRepository->findBy(
            ['user' => $user],
            ['sentAt' => 'DESC'],
            10
        );

        $data = [];
        foreach ($notifications as $notification) {
            $data[] = [
                'id' => $notification->getId(),
                'message' => $notification->getMessage(),
                'type' => $notification->getType(),
                'isRead' => $notification->isIsRead(),
                'sentAt' => $notification->getSentAt()->format('Y-m-d H:i:s'),
                'relatedEntity' => $notification->getRelatedEntity(),
                'relatedId' => $notification->getRelatedId()
            ];
        }

        return new JsonResponse([
            'notifications' => $data,
            'unreadCount' => $notificationRepository->count(['user' => $user, 'isRead' => false])
        ]);
    }

    #[Route('/{id}/mark-read', name: 'app_notification_mark_read', methods: ['POST'])]
    public function markAsRead(Notification $notification, EntityManagerInterface $entityManager): JsonResponse
    {
        if ($notification->getUser() !== $this->getUser()) {
            return new JsonResponse(['error' => 'Unauthorized'], 403);
        }

        $notification->setIsRead(true);
        $entityManager->flush();

        return new JsonResponse(['success' => true]);
    }

    #[Route('/mark-all-read', name: 'app_notification_mark_all_read', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function markAllAsRead(NotificationRepository $notificationRepository, EntityManagerInterface $entityManager): JsonResponse
    {
        $user = $this->getUser();
        $unreadNotifications = $notificationRepository->findBy(['user' => $user, 'isRead' => false]);

        foreach ($unreadNotifications as $notification) {
            $notification->setIsRead(true);
        }

        $entityManager->flush();

        return new JsonResponse(['success' => true]);
    }
}
