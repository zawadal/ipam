<?php

namespace AppBundle\Forms;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

use AppBundle\Entity\User;


/**
 * Description 
 *
 * @author l00gan
 */
class UserAddForm extends AbstractType {
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
                 ->add('username', TextType::class, ['attr' => ['class' => 'form-control', 'style' => 'margin-bottom=15px']])
                 ->add('password', RepeatedType::class, array(
                        'type' => PasswordType::class,
                        'invalid_message' => 'The password fields must match.',
                        'options' => array('attr' => array('class' => 'form-control')),
                        'required' => true,
                        'first_options'  => array('label' => 'Password'),
                        'second_options' => array('label' => 'Repeat Password'),
                    ))
                 ->add('email', EmailType::class, ['attr' => ['class' => 'form-control', 'style' => 'margin-bottom=15px']])
                 ->add('firstname', TextType::class, ['required' => false, 'attr' => ['class' => 'form-control', 'style' => 'margin-bottom=15px']])
                 ->add('lastname', TextType::class, ['required' => false, 'attr' => ['class' => 'form-control', 'style' => 'margin-bottom=15px']])
                 ->add('description', TextareaType::class, ['required' => false, 'attr' => ['class' => 'form-control', 'style' => 'margin-bottom:15px']])
                 ->add('enabled', CheckboxType::class, array(
                    'label'    => 'Account enabled?',
                    'required' => false,
                    ))
                 ->add('add', SubmitType::class, ['attr' => ['class' => 'btn btn-primary', 'style' => 'margin-bottom=15px']]);

        
    }
    
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => User::class,
        ));
    }
    
    
    
}
