<?php

namespace Ultimate\DefaultBundle\Form\Type;

use	Symfony\Component\Form\AbstractType,
	Symfony\Component\Form\FormBuilderInterface,
	Symfony\Component\OptionsResolver\OptionsResolverInterface,
	Symfony\Component\Validator\Constraints\Email,
	Symfony\Component\Validator\Constraints\Length,
	Symfony\Component\Validator\Constraints\NotBlank,
	Symfony\Component\Validator\Constraints\Collection
	;

class ContactType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		
		$builder
			->add('name', 'text', array(
				'required' => true,
				'error_bubbling' => true,
				'attr' => array(
					'placeholder' => 'Name',
					'pattern' => '.{2,}',
					'class' => 'span6',
				)
			))
			->add('email', 'email', array(
				'required' => true,
				'error_bubbling' => true,
				'attr' => array(
					'placeholder' => 'Email address',
					'class' => 'span6',
				)
			))
			->add('message', 'textarea', array(
				'required' => true,
				'error_bubbling' => true,
				'attr' => array(
					'rows' => 5,
					'placeholder' => 'Your message',
					'class' => 'span12',
				),
			))
			;
	}

	public function setDefaultOptions(OptionsResolverInterface $resolver)
	{
		$collectionConstraint = new Collection(array(
			'name' => array(
				new NotBlank(array('message' => 'Name should not be blank.')),
				new Length(array('min' => 2, 'minMessage' => 'Name is too short.'))
			),
			'email' => array(
				new NotBlank(array('message' => 'Email should not be blank.')),
				new Email(array('message' => 'Invalid email address.'))
			),
			'message' => array(
				new NotBlank(array('message' => 'Message should not be blank.')),
				new Length(array('min' => 5, 'minMessage' => 'Message is too short.'))
			)
		));
		$resolver->setDefaults(array(
			'constraints' => $collectionConstraint
			// 'csrf_protection' => false,
		));
	}

	public function getName()
	{
		return 'contact';
	}
}
