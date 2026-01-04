<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class QuestionChoiceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('choiceText', TextType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Texte de la réponse'
                ],
                'constraints' => [
                    new Assert\NotBlank(message: 'La réponse ne peut pas être vide'),
                    new Assert\Length(max: 500, maxMessage: 'Maximum 500 caractères')
                ]
            ])
            ->add('isCorrect', HiddenType::class, [
                'data' => 'false',
                'attr' => ['class' => 'choice-is-correct']
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => null,
        ]);
    }
}
