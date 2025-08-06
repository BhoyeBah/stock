<?php

namespace App\Form;

use App\Entity\Client;
use App\Entity\Order;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OrderType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('client', EntityType::class, [
                'class' => Client::class,
                'choices' => $options['clients'],
                'placeholder' => 'Sélectionnez un client',
                'attr' => ['class' => 'form-control'],
                'choice_label' => function ($client) {
                    return $client->getPrenom() . ' ' . $client->getNom();
                },
                'required' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Order::class,
            'clients' => [],
        ]);
    }
}
