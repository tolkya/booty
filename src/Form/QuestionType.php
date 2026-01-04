<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class QuestionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // Type de question
            ->add('type', ChoiceType::class, [
                'label' => 'Type de question',
                'choices' => [
                    'QCM' => 'qcm',
                    'Vrai/Faux' => 'true_false',
                    'Texte libre' => 'text',
                ],
                'expanded' => true,
                'data' => 'qcm',
                'attr' => ['class' => 'question-type-radios']
            ])
            
            // Texte de la question
            ->add('questionText', TextareaType::class, [
                'label' => 'Texte de la question',
                'attr' => [
                    'class' => 'form-control',
                    'rows' => 3
                ],
                'constraints' => [
                    new Assert\NotBlank(message: 'Le texte de la question est requis'),
                    new Assert\Length(max: 1000, maxMessage: 'Maximum 1000 caractères')
                ]
            ])
            
            // Points
            ->add('points', IntegerType::class, [
                'label' => 'Points',
                'data' => 10,
                'attr' => [
                    'class' => 'form-control',
                    'min' => 1
                ],
                'constraints' => [
                    new Assert\Positive(message: 'Les points doivent être positifs')
                ]
            ])
            
            // Pénalité
            ->add('penalty', IntegerType::class, [
                'label' => 'Pénalité (si temps dépassé)',
                'data' => 5,
                'attr' => [
                    'class' => 'form-control',
                    'min' => 0
                ],
                'constraints' => [
                    new Assert\PositiveOrZero(message: 'La pénalité doit être positive ou zéro')
                ]
            ])
            
            // Image optionnelle
            ->add('image', FileType::class, [
                'label' => 'Image (optionnel)',
                'required' => false,
                'mapped' => false,
                'attr' => [
                    'class' => 'form-control',
                    'accept' => 'image/*'
                ],
                'constraints' => [
                    new Assert\Image(
                        maxSize: '5M',
                        mimeTypes: ['image/jpeg', 'image/png', 'image/webp', 'image/gif'],
                        mimeTypesMessage: 'Veuillez uploader une image valide (JPEG, PNG, WebP, GIF)'
                    )
                ]
            ])
            
            // Collection de choix (pour QCM et Vrai/Faux)
            ->add('choices', CollectionType::class, [
                'entry_type' => QuestionChoiceType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'label' => false,
                'prototype' => true,
                'prototype_name' => '__choice_name__',
                'entry_options' => [
                    'label' => false,
                ],
                'attr' => ['class' => 'choices-collection']
            ])
            
            // Réponse attendue (texte libre)
            ->add('expectedAnswer', TextType::class, [
                'label' => 'Réponse attendue (optionnel)',
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Laissez vide pour validation manuelle'
                ],
                'constraints' => [
                    new Assert\Length(max: 500, maxMessage: 'Maximum 500 caractères')
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => null,
        ]);
    }
}
