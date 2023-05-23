<?php

namespace App\Form;

use App\Entity\RestaurantEmployee;
use App\Entity\RestaurantRole;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EmployeeType extends AbstractType
{
    /** @SuppressWarnings(PHPMD.UnusedFormalParameter) */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('restaurant', HiddenType::class, ['property_path' => 'employee.id'])
            ->add('employee', RegisterUserType::class)
            ->add('roles', EntityType::class, [
                'class' => RestaurantRole::class,
                'multiple' => true,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => RestaurantEmployee::class,
        ]);
    }
}
