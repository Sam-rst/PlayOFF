<?php

namespace App\Form;

use App\Entity\Gender;
use App\Entity\Meeting;
use App\Entity\Team;
use App\Entity\Tournament;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AddNewPlayerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstname', TextType::class)
            ->add('lastname', TextType::class)
            ->add('email', EmailType::class)
            ->add('gender', EntityType::class, [
                'class' => Gender::class,
                'choice_label' => function (Gender $gender) {
                    return $gender->getGender(); 
                },
                'label' => 'Gender Rule',
                'placeholder' => 'Select a Gender Rule', 
                'expanded' => true,
                'multiple' => false, 
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
