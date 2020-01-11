<?php

namespace App\Form;

use App\Entity\Ad;
use App\Entity\Booking;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AdminBookingType extends ApplicationType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('startDate', DateType::class, $this->getConfiguration("Date d'arrivée", "Renseignez la date d'arrivée", [
                'widget' => 'single_text'
            ]))
            ->add('endDate', DateType::class, $this->getConfiguration("Date de départ", "Renseignez la date de départ", [
                'widget' => 'single_text'
            ]))
            ->add('comment')
            ->add('booker', EntityType::class, [
                'class' => User::class,
                'choice_label' => function ($user) {
                    return $user->getFullName();
                }
            ])
            ->add('ad', EntityType::class, [
                'class' => Ad::class,
                'choice_label' => 'title'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Booking::class,
        ]);
    }
}
