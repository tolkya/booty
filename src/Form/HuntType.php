<?php

namespace App\Form;

use App\Entity\Hunt;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class HuntType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Nom de la chasse',
                'attr' => ['class' => 'form-control', 'placeholder' => 'Ex: Chasse au trésor de Paris']
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description (optionnel)',
                'required' => false,
                'attr' => ['class' => 'form-control', 'rows' => 3, 'placeholder' => 'Décrivez votre chasse...']
            ])
            ->add('mode', ChoiceType::class, [
                'label' => 'Mode de jeu',
                'choices' => [
                    'QR codes uniquement' => 'qr_only',
                    'QR codes + Questions' => 'qr_with_questions',
                ],
                'expanded' => true, // Radio buttons
                'attr' => ['class' => 'form-check']
            ])
            ->add('timeLimitMode', ChoiceType::class, [
                'label' => 'Limite de temps pour répondre aux questions',
                'choices' => [
                    'Strict (impossible de répondre après)' => 'strict',
                    'Avec pénalité (peut répondre mais pénalisé)' => 'penalty',
                ],
                'expanded' => true,
                'attr' => ['class' => 'form-check']
            ])
            ->add('qrCodeCount', IntegerType::class, [
                'label' => 'Nombre de QR codes',
                'mapped' => false, // Pas dans l'entité Hunt
                'attr' => ['class' => 'form-control', 'min' => 2, 'max' => 50, 'value' => 5],
                'help' => 'Entre 2 et 50 QR codes'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Hunt::class,
        ]);
    }
}
