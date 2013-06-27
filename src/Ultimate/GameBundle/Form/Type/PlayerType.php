<?php

namespace Ultimate\PlayerBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class PlayerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
		
		$builder
			->add('id', null, array(
				'required' => false,
				'error_bubbling' => true,
			))
			->add('name', null, array(
				'required' => false,
				'error_bubbling' => true,
				'attr' => array(
					'placeholder' => 'Name',
					'class' => 'span12',
				)
			))
			->add('phone', null, array(
				'required' => false,
				'error_bubbling' => true,
				'attr' => array(
					'placeholder' => 'Phone',
					'class' => 'span12',
				)
			))
			->add('email', null, array(
				'required' => false,
				'error_bubbling' => true,
				'attr' => array(
					'placeholder' => 'Email',
					'class' => 'span12',
				)
			))
			;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Ultimate\GameBundle\Entity\Player',
            'csrf_protection' => false,
        ));
    }

    public function getName()
    {
        return 'player';
    }
}
