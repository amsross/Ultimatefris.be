<?php

namespace Ultimate\GameBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class GameType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
		
		$builder
			->add('id', null, array(
				'required' => false,
			))
			->add('title', null, array(
				'required' => true,
				'attr' => array(
					'placeholder' => 'Title',
					'class' => 'span12',
				)
			))
			->add('date', 'datetime', array(
				'required' => true,
				'widget' => 'single_text',
				'input' => 'string',
				'format' => 'yyyy-MM-dd hh:mm a',
				'attr' => array(
					'placeholder' => 'Date / Time',
					'class' => 'span12 form_datetime',
				)
			))
			->add('location', null, array(
				'required' => true,
				'attr' => array(
					'placeholder' => 'Location',
					'class' => 'span12',
				)
			))
			->add('info', null, array(
				'required' => false,
				'attr' => array(
					'placeholder' => 'Info',
					'class' => 'span12',
					'rows' => 8,
				)
			))
			;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Ultimate\GameBundle\Entity\Game',
            // 'csrf_protection' => false,
        ));
    }

    public function getName()
    {
        return 'game';
    }
}
