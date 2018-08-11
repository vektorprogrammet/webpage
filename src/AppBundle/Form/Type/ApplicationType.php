<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ApplicationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // The fields that populate the form
        $builder
            ->add('user', new CreateUserOnApplicationType($options['departmentId']), array(
                'label' => '',
            ))
        ->add('yearOfStudy', ChoiceType::class, [
            'label' => 'Årstrinn',
            'choices' => array(
                '1. klasse' => '1. klasse',
                '2. klasse' => '2. klasse',
                '3. klasse' => '3. klasse',
                '4. klasse' => '4. klasse',
                '5. klasse' => '5. klasse',
            ),
        ]);
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Application',
            'user' => null,
            'allow_extra_fields' => true,
            'departmentId' => null,
            'environment' => 'prod',
        ));
    }

    public function getName()
    {
        return 'application'; // This must be unique
    }
}
