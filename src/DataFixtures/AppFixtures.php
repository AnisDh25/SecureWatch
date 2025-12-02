<?php

namespace App\DataFixtures;

use App\Entity\Asset;
use App\Entity\Event;
use App\Entity\User;
use App\Entity\Alert;
use App\Entity\AlertRule;
use App\Entity\Incident;
use App\Entity\Notification;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        // Create admin user
        $admin = new User();
        $admin->setEmail('admin@securewatch.com');
        $admin->setUsername('admin');
        $admin->setRoles(['ROLE_ADMIN', 'ROLE_USER']);
        $admin->setRole('admin');
        $admin->setPassword($this->passwordHasher->hashPassword($admin, 'admin123'));
        $manager->persist($admin);

        // Create additional users for testing
        $managerUser = new User();
        $managerUser->setEmail('manager@securewatch.com');
        $managerUser->setUsername('manager');
        $managerUser->setRoles(['ROLE_USER']);
        $managerUser->setRole('manager');
        $managerUser->setPassword($this->passwordHasher->hashPassword($managerUser, 'manager123'));
        $manager->persist($managerUser);

        $operatorUser = new User();
        $operatorUser->setEmail('operator@securewatch.com');
        $operatorUser->setUsername('operator');
        $operatorUser->setRoles(['ROLE_USER']);
        $operatorUser->setRole('operator');
        $operatorUser->setPassword($this->passwordHasher->hashPassword($operatorUser, 'operator123'));
        $manager->persist($operatorUser);

        $viewerUser = new User();
        $viewerUser->setEmail('viewer@securewatch.com');
        $viewerUser->setUsername('viewer');
        $viewerUser->setRoles(['ROLE_USER']);
        $viewerUser->setRole('viewer');
        $viewerUser->setPassword($this->passwordHasher->hashPassword($viewerUser, 'viewer123'));
        $manager->persist($viewerUser);

        // Create assets
        $assets = [];
        $assetData = [
            ['hostname' => 'Web-01', 'ip' => '192.168.1.10', 'type' => 'Web Server', 'status' => 'active'],
            ['hostname' => 'DB-Primary', 'ip' => '192.168.1.20', 'type' => 'Database Server', 'status' => 'active'],
            ['hostname' => 'API-Gateway', 'ip' => '192.168.1.30', 'type' => 'API Gateway', 'status' => 'active'],
            ['hostname' => 'Mail-Server', 'ip' => '192.168.1.40', 'type' => 'Mail Server', 'status' => 'active'],
            ['hostname' => 'File-Server', 'ip' => '192.168.1.50', 'type' => 'File Server', 'status' => 'active'],
        ];

        foreach ($assetData as $data) {
            $asset = new Asset();
            $asset->setHostname($data['hostname']);
            $asset->setIp($data['ip']);
            $asset->setType($data['type']);
            $asset->setStatus($data['status']);
            $manager->persist($asset);
            $assets[] = $asset;
        }

        // Create alert rules
        $alertRules = [];
        $ruleData = [
            ['name' => 'High CPU Usage', 'condition' => 'cpu_high', 'threshold' => 80, 'action' => 'notify'],
            ['name' => 'Failed Login Attempts', 'condition' => 'failed_login', 'threshold' => 5, 'action' => 'alert'],
            ['name' => 'Disk Space Low', 'condition' => 'disk_low', 'threshold' => 90, 'action' => 'critical'],
        ];

        foreach ($ruleData as $data) {
            $rule = new AlertRule();
            $rule->setName($data['name']);
            $rule->setCondition($data['condition']);
            $rule->setThreshold($data['threshold']);
            $rule->setAction($data['action']);
            $manager->persist($rule);
            $alertRules[] = $rule;
        }

        // Create events
        $events = [];
        $eventTypes = ['info', 'warning', 'critical'];
        $sources = ['system', 'network', 'application', 'security'];

        for ($i = 0; $i < 50; $i++) {
            $event = new Event();
            $event->setTimestamp(new \DateTime('-' . rand(0, 24) . ' hours'));
            $event->setSource($sources[array_rand($sources)]);
            $event->setSeverity($eventTypes[array_rand($eventTypes)]);
            $event->setData('Sample event data #' . ($i + 1));
            $event->setAsset($assets[array_rand($assets)]);
            $manager->persist($event);
            $events[] = $event;
        }

        // Create alerts
        $alerts = [];
        for ($i = 0; $i < 20; $i++) {
            $alert = new Alert();
            $alert->setCreatedAt(new \DateTime('-' . rand(0, 12) . ' hours'));
            $alert->setSeverity(['low', 'medium', 'high', 'critical'][array_rand(['low', 'medium', 'high', 'critical'])]);
            $alert->setStatus(['active', 'resolved', 'escalated'][array_rand(['active', 'resolved', 'escalated'])]);
            $alert->setAlertRule($alertRules[array_rand($alertRules)]);
            $alert->setEvent($events[array_rand($events)]);
            $manager->persist($alert);
            $alerts[] = $alert;
        }

        // Create incidents
        $incidents = [];
        for ($i = 0; $i < 10; $i++) {
            $incident = new Incident();
            $incident->setOpenedAt(new \DateTime('-' . rand(1, 48) . ' hours'));
            $incident->setStatus(['open', 'investigating', 'resolved'][array_rand(['open', 'investigating', 'resolved'])]);
            $incident->setNotes('Initial incident notes #' . ($i + 1));
            $incident->setAssignedTo($admin);
            
            if ($incident->getStatus() === 'resolved') {
                $incident->setClosedAt(new \DateTime('-' . rand(0, 24) . ' hours'));
            }
            
            $manager->persist($incident);
            $incidents[] = $incident;
        }

        // Create notifications
        $notificationTypes = ['alert', 'info', 'warning', 'error', 'success'];
        $channels = ['email', 'sms', 'webhook'];
        $notificationMessages = [
            'High CPU usage detected on server',
            'New security alert requires attention',
            'Database backup completed successfully',
            'Failed login attempt detected',
            'System update available',
            'Memory threshold exceeded',
            'Network connectivity issue',
            'Security patch applied',
            'Disk space running low',
            'Service restarted automatically'
        ];
        
        for ($i = 0; $i < 30; $i++) {
            $notification = new Notification();
            $notification->setUser($admin); // Assign to admin user for testing
            $notification->setMessage($notificationMessages[array_rand($notificationMessages)]);
            $notification->setType($notificationTypes[array_rand($notificationTypes)]);
            $notification->setSentAt(new \DateTime('-' . rand(0, 24) . ' hours'));
            $notification->setIsRead(rand(0, 1) === 0); // Random read/unread status
            $notification->setChannel($channels[array_rand($channels)]);
            $notification->setStatus(['sent', 'pending', 'failed'][array_rand(['sent', 'pending', 'failed'])]);
            $notification->setAlert($alerts[array_rand($alerts)]);
            
            // Set related entity for some notifications
            if (rand(0, 1) === 0) {
                $relatedEntities = ['Asset', 'Event', 'Alert', 'Incident'];
                $notification->setRelatedEntity($relatedEntities[array_rand($relatedEntities)]);
                $notification->setRelatedId(rand(1, 50));
            }
            
            $manager->persist($notification);
        }

        $manager->flush();
    }
}
