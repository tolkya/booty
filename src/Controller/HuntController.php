<?php

namespace App\Controller;

use App\Entity\Hunt;
use App\Form\HuntType;
use App\Repository\HuntRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
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
    
    #[Route('/new', name: 'hunt_new')]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $hunt = new Hunt();
        $hunt->setOrganizer($this->getUser());
        // createdAt, updatedAt, status sont auto-remplis dans __construct
        
        $form = $this->createForm(HuntType::class, $hunt);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $qrCodeCount = $form->get('qrCodeCount')->getData();
            
            // TODO: Générer les QR codes (prochaine étape)
            
            $em->persist($hunt);
            $em->flush();
            
            $this->addFlash('success', 'Chasse créée avec succès !');
            return $this->redirectToRoute('hunt_index');
        }
        
        return $this->render('hunt/form.html.twig', [
            'form' => $form,
            'hunt' => $hunt,
            'is_edit' => false,
        ]);
    }
    
    #[Route('/{id}/edit', name: 'hunt_edit')]
    public function edit(Hunt $hunt, Request $request, EntityManagerInterface $em): Response
    {
        // Vérifier que l'utilisateur est bien le propriétaire
        if ($hunt->getOrganizer() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }
        
        $form = $this->createForm(HuntType::class, $hunt);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            
            $this->addFlash('success', 'Chasse modifiée avec succès !');
            return $this->redirectToRoute('hunt_index');
        }
        
        return $this->render('hunt/form.html.twig', [
            'form' => $form,
            'hunt' => $hunt,
            'is_edit' => true,
        ]);
    }
}
