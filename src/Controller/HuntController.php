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
            
            // Récupérer le temps limite par défaut pour les questions (en secondes)
            $defaultQuestionTimeLimit = null;
            $hasTimeLimit = $form->get('hasTimeLimit')->getData();
            if ($hasTimeLimit) {
                $minutes = $form->get('timeLimitMinutes')->getData() ?? 0;
                $seconds = $form->get('timeLimitSeconds')->getData() ?? 0;
                $defaultQuestionTimeLimit = ($minutes * 60) + $seconds;
            }
            
            // Stocker temporairement dans la session pour la page questions
            $request->getSession()->set('default_question_time_limit', $defaultQuestionTimeLimit);
            $request->getSession()->set('time_limit_mode', $hunt->getTimeLimitMode());
            
            // Persister la hunt d'abord
            $em->persist($hunt);
            $em->flush();
            
            // Générer les QR codes
            for ($i = 1; $i <= $qrCodeCount; $i++) {
                $qrCode = new \App\Entity\QrCode();
                $qrCode->setHunt($hunt);
                $qrCode->setCode('qr_' . uniqid()); // Générer un code unique
                $qrCode->setOrderPosition($i);
                $qrCode->setIsStartCode($i === 1); // Le premier est le code de départ
                $qrCode->setIsPlaced(false);
                $qrCode->setCreatedAt(new \DateTimeImmutable()); // Force l'initialisation au cas où le constructeur ne marche pas
                
                $em->persist($qrCode);
            }
            $em->flush();
            
            $this->addFlash('success', 'Chasse créée avec succès !');
            
            // Redirection selon le mode
            if ($hunt->getMode() === 'qr_with_questions') {
                // Rediriger vers création des questions
                return $this->redirectToRoute('hunt_questions', ['id' => $hunt->getId()]);
            } else {
                // Rediriger vers page détail de la hunt
                return $this->redirectToRoute('hunt_show', ['id' => $hunt->getId()]);
            }
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
    
    #[Route('/{id}/questions', name: 'hunt_questions')]
    public function questions(Hunt $hunt, Request $request, EntityManagerInterface $em): Response
    {
        // Vérifier que l'utilisateur est bien le propriétaire
        if ($hunt->getOrganizer() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }
        
        // Compter les QR codes pour afficher le minimum requis
        $qrCodeCount = $hunt->getQrCodes()->count();
        $questionCount = $hunt->getQuestions()->count();
        
        // Récupérer le temps par défaut depuis la session
        $defaultTimeLimit = $request->getSession()->get('default_question_time_limit');
        $timeLimitMode = $request->getSession()->get('time_limit_mode');
        
        return $this->render('hunt/questions.html.twig', [
            'hunt' => $hunt,
            'qrCodeCount' => $qrCodeCount,
            'questionCount' => $questionCount,
            'defaultTimeLimit' => $defaultTimeLimit,
            'timeLimitMode' => $timeLimitMode,
        ]);
    }
}
