<?php

namespace App\Form;

use App\Entity\Sport;
use App\Entity\Team;
use App\Entity\Tournament;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TournamentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('rules')
            ->add('is_public')
            ->add('start_time', null, [
                'widget' => 'single_text',
            ])
            ->add('end_time', null, [
                'widget' => 'single_text',
            ])
            ->add('gender_rule')
            ->add('number_players_per_team')
            ->add('type_tournament')
            ->add('created_at', null, [
                'widget' => 'single_text',
            ])
            ->add('sport_id', EntityType::class, [
                'class' => Sport::class,
                'choice_label' => 'id',
            ])
            ->add('users', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'id',
                'multiple' => true,
            ])
            ->add('organisator_id', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'id',
            ])
            ->add('enrolled_teams', EntityType::class, [
                'class' => Team::class,
                'choice_label' => 'id',
                'multiple' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Tournament::class,
        ]);
    }
}
