<?php

namespace App\Form;


use App\Entity\Gender;
use App\Entity\Sport;
use App\Entity\Tournament;
use App\Entity\User;
use App\Repository\SportRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TournamentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder

            ->add('name', null, [
                'label' => 'Tournament Name',
            ])
            ->add('rules', TextareaType::class, [
                'label' => 'Tournament Rules',
                'required' => false,
                'attr' => ['class' => 'custom-textarea', 'rows' => '5'],
            ])
            ->add('is_public', CheckboxType::class, [
                'label'    => 'Public Tournament',
                'required' => false,
            ])
            ->add('start_time', null, [
                'label' => 'Start Time',
                'widget' => 'single_text',
            ])
            ->add('end_time', null, [
                'label' => 'End Time',
                'widget' => 'single_text',
            ])
            ->add('gender_rule', EntityType::class, [
                'class' => Gender::class,
                'choice_label' => function (Gender $gender) {
                    return $gender->getGender(); 
                },
                'label' => 'Gender Rule',
                'placeholder' => 'Select a Gender Rule', 
                'expanded' => true,
                'multiple' => false, 
            ])
            
            ->add('number_players_per_team', null, [
                'label' => 'Number of Players per Team',
            ])
            ->add('type_tournament', ChoiceType::class, [
                'label' => 'Tournament Type',
                'choices' => [
                    'Single Elimination' => 'single_elimination',
                    'Double Elimination' => 'double_elimination',
                    'Round Robin' => 'round_robin',
                    'League' => 'league',
                ],
                'expanded' => true,
                'multiple' => false,
            ])
            ->add('sport', EntityType::class, [
                'label' => 'Sport Type',
                'class' => Sport::class,
                'choice_label' => 'name',
                'query_builder' => function (SportRepository $repo) {
                    return $repo->createQueryBuilder('s')
                        ->orderBy('s.name', 'ASC');
                },
            ])
            ->add('organisator', EntityType::class, [
                'class' => User::class,
                'label' => 'Tournament Organiser',
                'choice_label' => 'username',
            ])
            ->add('participating_players', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'username', // Assuming User entity has a username property
                'label' => 'Participating Players',
                'multiple' => true,
            ]);

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Tournament::class,
        ]);
    }
}
