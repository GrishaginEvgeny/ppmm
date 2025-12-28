<?php

namespace App\Form;

use App\Entity\Direction;
use App\Entity\Event;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EventType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('level', TextType::class, [
                'label' => 'Уровень мерориятия'
            ])
            ->add('points', NumberType::class, [
                'label' => 'Баллы',
                'scale' => 1,
            ])
            ->add('name', TextType::class, [
                'label' => 'Название мероприятия'
            ])
            ->add('direction', EntityType::class, [
                'class' => Direction::class,
                'choice_label' => 'name',
                'label' => 'Направление',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Event::class,
        ]);
    }
}
