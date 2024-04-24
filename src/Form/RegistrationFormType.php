<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'form-control'
                ],
                'required' => true
            ])
            ->add('firstname', TextType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'form-control'
                ],
                'required' => true
            ])
            ->add('lastname', TextType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'form-control'
                ],
                'required' => true
            ])
            ->add('username', TextType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'form-control'
                ],
                'required' => true
            ])
            ->add('birth_day', ChoiceType::class, [
                'label' => false,
                'mapped' => false,
                'choices' => array_combine(range(1, 31), range(1, 31)),
                'choice_label' => function ($value, $key, $index) {
                    return $value;
                },
                'attr' => ['class' => 'form-select'],
            ])
            ->add('birth_month', ChoiceType::class, [
                'label' => false,
                'mapped' => false,
                'choices' => [
                    'Janvier' => 1,
                    'Février' => 2,
                    'Mars' => 3,
                    'Avril' => 4,
                    'Mai' => 5,
                    'Juin' => 6,
                    'Juillet' => 7,
                    'Août' => 8,
                    'Septembre' => 9,
                    'Octobre' => 10,
                    'Novembre' => 11,
                    'Décembre' => 12,
                ],
                'attr' => ['class' => 'form-select'],
            ])
            ->add('birth_year', ChoiceType::class, [
                'label' => false,
                'mapped' => false,
                'choices' => array_combine(range(date('Y'), 1900), range(date('Y'), 1900)),
                'choice_label' => function ($value, $key, $index) {
                    return $value;
                },
                'attr' => ['class' => 'form-select'],
            ])
            ->add('plainPassword', PasswordType::class, [
                'required' => true,
                'label' => false,
                'mapped' => false,
                'attr' => [
                    'autocomplete' => 'new-password',
                    'class' => 'form-control'
                ],
                'row_attr' => [
                    'class' => 'form-floating my-3'
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez entrer un mot de passe',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Votre mot de passe doit avoir au moins {{ limit }} caractères',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                ]
            ])
            ->add('confirmPassword', PasswordType::class, [
                'required' => true,
                'label' => false,
                'mapped' => false,
                'attr' => [
                    'autocomplete' => 'new-password',
                    'class' => 'form-control'
                ],
                'row_attr' => [
                    'class' => 'form-floating my-3'
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez confirmer votre mot de passe',
                    ]),
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
