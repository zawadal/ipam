<?php

namespace AppBundle\Forms;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

use AppBundle\Entity\Customer;


/**
 * Description of NetworkForm
 *
 * @author l00gan
 */
class CustomerEditForm extends AbstractType {
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
                 ->add('name', TextType::class, ['attr' => ['class' => 'form-control', 'style' => 'margin-bottom=15px']])
                 ->add('description', TextareaType::class, ['required' => false, 'attr' => ['class' => 'form-control', 'style' => 'margin-bottom=15px']])
                 ->add('street', TextType::class, ['attr' => ['class' => 'form-control', 'style' => 'margin-bottom=15px']])
                 ->add('building', TextType::class, ['attr' => ['class' => 'form-control', 'style' => 'margin-bottom=15px']])
                 ->add('city', TextType::class, ['attr' => ['class' => 'form-control', 'style' => 'margin-bottom=15px']])
                 ->add('code', TextType::class, ['attr' => ['class' => 'form-control', 'style' => 'margin-bottom=15px']])
                 ->add('country', TextType::class, ['attr' => ['class' => 'form-control', 'style' => 'margin-bottom=15px']])
                 ->add('edit', SubmitType::class, ['attr' => ['class' => 'btn btn-primary', 'style' => 'margin-bottom=15px']]);

        
    }
    
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Customer::class,
        ));
    }
    
    
    
}
