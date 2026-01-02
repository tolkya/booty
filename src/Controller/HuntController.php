<?php

namespace App\Controller;

use App\Repository\HuntRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
#[Route('/hunts')]
final class HuntController extends AbstractController
{
    #[Route('', name: 'hunt_index')]
    public function index(HuntRepository $huntRepository): Response
    {
        $user = $this->getUser();
        
        // Récupérer toutes les chasses de l'utilisateur
        $hunts = $huntRepository->findBy(
            ['organizer' => $user],
            ['createdAt' => 'DESC']
        );
        
        return $this->render('hunt/index.html.twig', [
            'hunts' => $hunts,
        ]);
    }
}
