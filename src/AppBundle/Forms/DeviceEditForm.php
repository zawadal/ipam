<?php

namespace AppBundle\Forms;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\ORM\EntityRepository;
use AppBundle\Entity\Device;

/**
 * Description of DeviceForm
 *
 * @author l00gan
 */
class DeviceEditForm extends AbstractType {
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
                 ->add('hostname', TextType::class, ['attr' => ['class' => 'form-control', 'style' => 'margin-bottom=15px']])
                 ->add('description', TextareaType::class, ['required' => false, 'attr' => ['class' => 'form-control', 'style' => 'margin-bottom=15px']])
                 ->add('mac', TextType::class, ['attr' => ['class' => 'form-control', 'style' => 'margin-bottom=15px']])
                 ->add('serial', TextType::class, ['attr' => ['class' => 'form-control', 'style' => 'margin-bottom=15px']])
                 ->add('warranty', ChoiceType::class, ['attr' => ['class' => 'form-control', 'style' => 'margin-bottom=15px'], 'choices'  => [
                        '12 months' => 12,
                        '24 months' => 24,
                        '36 months' => 36,
                        '48 months' => 48,
                        '60 months' => 60,
                    ]])
                 ->add('owner', EntityType::class, [
                     'attr' => ['class' => 'form-control', 'style' => 'margin-bottom:15px'],
                     'class' => 'AppBundle:Owner',
                     'query_builder' => function (EntityRepository $er) use($options) {
                            return $er->createQueryBuilder('u')
                                    ->innerjoin('u.customer', 'c')
                                    ->where('c.id = :customer')  
                                    ->setParameter('customer', $options['customer']);
                     },
                     'choice_label' => function ($owner)
                     {
                        return $owner->getFirstname()." ".$owner->getLastname()." - ".$owner->getCustomer()->getName();
                     }
                  ])   
                 ->add('type', EntityType::class, [
                     'attr' => ['class' => 'form-control', 'style' => 'margin-bottom:15px'],
                     'class' => 'AppBundle:Type',
                     'choice_label' => 'name',
                  ])      
                 ->add('edit', SubmitType::class, ['attr' => ['class' => 'btn btn-primary', 'style' => 'margin-bottom=15px']]);

        
    }
    
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Device::class,
            'customer' => null,

        ));
    }
    
    
    
}
