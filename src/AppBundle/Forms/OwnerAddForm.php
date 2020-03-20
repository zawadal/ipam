<?php

namespace AppBundle\Forms;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

use AppBundle\Entity\Owner;


/**
 * Description of OwnerForm
 *
 * @author l00gan
 */
class OwnerAddForm extends AbstractType {
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
                 ->add('firstname', TextType::class, ['attr' => ['class' => 'form-control', 'style' => 'margin-bottom=15px']])
                 ->add('lastname', TextType::class, ['attr' => ['class' => 'form-control', 'style' => 'margin-bottom=15px']])
                 ->add('email', TextType::class, ['attr' => ['class' => 'form-control', 'style' => 'margin-bottom=15px']])
                 ->add('phone', TextType::class, ['attr' => ['class' => 'form-control', 'style' => 'margin-bottom=15px']])
                 ->add('location', TextType::class, ['attr' => ['class' => 'form-control', 'style' => 'margin-bottom=15px']])
                 ->add('ou', TextType::class, ['attr' => ['class' => 'form-control', 'style' => 'margin-bottom=15px']])
                 ->add('description', TextareaType::class, ['required' => false, 'attr' => ['class' => 'form-control', 'style' => 'margin-bottom=15px']])
                 ->add('add', SubmitType::class, ['attr' => ['class' => 'btn btn-primary', 'style' => 'margin-bottom=15px']]);

        
    }
    
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Owner::class,
        ));
    }
    
    
    
}
