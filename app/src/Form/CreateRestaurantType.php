<?php

namespace App\Form;

use App\Entity\Premises;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CreateRestaurantType extends AbstractType
{
    /** @SuppressWarnings(PHPMD.UnusedFormalParameter) */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('restaurant', RestaurantType::class)
            ->add('name', TextType::class, ['label' => 'Local name'])
            ->add('address', AddressType::class, ['label' => 'Local\'s address:']);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => Premises::class]);
    }
}
