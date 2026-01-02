<?php

namespace App\Controller;

use App\Repository\HuntRepository;
use App\Repository\SessionRepository;
use App\Repository\SessionHunterRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
final class DashboardController extends AbstractController
{
    #[Route('/dashboard', name: 'app_dashboard')]
    public function index(
        HuntRepository $huntRepository,
        SessionRepository $sessionRepository,
        SessionHunterRepository $sessionHunterRepository
    ): Response
    {
        $user = $this->getUser();
        
        // Statistiques simples
        $totalHunts = $huntRepository->count(['organizer' => $user]);
        
        // Nombre de sessions de cet organisateur
        $totalSessions = $sessionRepository->createQueryBuilder('s')
            ->select('COUNT(s.id)')
            ->join('s.hunt', 'h')
            ->where('h.organizer = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getSingleScalarResult();
        
        // Nombre de sessions actives
        $activeSessionsCount = $sessionRepository->count(['status' => 'active']);
        
        // Total hunters (simplifié pour éviter erreurs)
        $totalHunters = 0; // On calculera plus tard si besoin
        
        // Dernières chasses (3 dernières)
        $recentHunts = $huntRepository->findBy(
            ['organizer' => $user],
            ['createdAt' => 'DESC'],
            3
        );
        
        // Sessions actives de cet organisateur
        $activeSessions = $sessionRepository->createQueryBuilder('s')
            ->join('s.hunt', 'h')
            ->where('h.organizer = :user')
            ->andWhere('s.status = :status')
            ->setParameter('user', $user)
            ->setParameter('status', 'active')
            ->orderBy('s.startedAt', 'DESC')
            ->getQuery()
            ->getResult();
        
        return $this->render('dashboard/index.html.twig', [
            'total_hunts' => $totalHunts,
            'total_sessions' => $totalSessions,
            'active_sessions_count' => $activeSessionsCount,
            'total_hunters' => $totalHunters,
            'recent_hunts' => $recentHunts,
            'active_sessions' => $activeSessions,
        ]);
    }
}
