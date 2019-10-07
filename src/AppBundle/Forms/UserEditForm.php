<?php

namespace AppBundle\Forms;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;
use Symfony\Component\Validator\Constraints\NotBlank;
use AppBundle\Entity\User;
use FOS\UserBundle\Util\LegacyFormHelper;

/**
 * Description 
 *
 * @author l00gan
 */
class UserEditForm extends AbstractType {
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        
        $constraintsOptions = array(
            'message' => 'fos_user.current_password.invalid',
        );

        if (!empty($options['validation_groups'])) {
            $constraintsOptions['groups'] = array(reset($options['validation_groups']));
        }
        $builder->add('username', TextType::class, ['required' => false, 'attr' => ['class' => 'form-control', 'style' => 'margin-bottom=15px']])
                ->add('email', TextType::class, ['required' => false, 'attr' => ['class' => 'form-control', 'style' => 'margin-bottom=15px']])
                ->add('firstname', TextType::class, ['required' => false, 'attr' => ['class' => 'form-control', 'style' => 'margin-bottom=15px']])
                ->add('lastname', TextType::class, ['required' => false, 'attr' => ['class' => 'form-control', 'style' => 'margin-bottom=15px']])
                ->add('description', TextareaType::class, ['required' => false, 'attr' => ['class' => 'form-control', 'style' => 'margin-bottom:15px']])
                ->add('current_password', LegacyFormHelper::getType('Symfony\Component\Form\Extension\Core\Type\PasswordType'), array(
                    'attr' => ['class' => 'form-control', 'style' => 'margin-bottom:15px; background-color:#ff9999'],
                    'label' => 'Confirm current password',
                    'translation_domain' => 'FOSUserBundle',
                    'mapped' => false,
                    'constraints' => array(
                        new NotBlank(),
                        new UserPassword($constraintsOptions)),
        ));
    }
    
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => User::class,
        ));
    }
    
    public function getParent()
    {
        return 'FOS\UserBundle\Form\Type\ProfileFormType';
    }
    
}
