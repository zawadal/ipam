<?php

namespace AppBundle\Forms;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\ORM\EntityRepository;


use AppBundle\Validator\Constraints\IsUniqueGw;

use AppBundle\Entity\Address;

/**
 * Description of addressAssignForm
 *
 * @author l00gan
 */
class AddressAssignForm extends AbstractType {
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $value = $options['network'];
        $builder->add('device', EntityType::class, [
                     'attr' => ['class' => 'form-control', 'style' => 'margin-bottom:15px'],
                     'class' => 'AppBundle:Device',
                     'choice_label' => function ($device)
                     {
                        return $device->getHostname();
                     },
                     'query_builder' => function (EntityRepository $er) use ($options) {
                            return $er->createQueryBuilder('u')
                                    ->innerjoin('u.owner', 'c')
                                    ->innerjoin('c.customer','d')
                                    ->where('d.id = :customer')  
                                    ->setParameter('customer', $options['customer']);
                     }
                  ]) 
                ->add('description', TextAreaType::class, ['required' => false, 'attr' => ['class' => 'form-control', 'style' => 'margin-bottom=15px']])
                ->add('type', ChoiceType::class, ['attr' => ['class' => 'form-control', 'style' => 'margin-bottom=15px'],
                    'choices'  => [                       
                        'IPAM - static' => 1,
                        'DHCP - reserved' => 2,
                        'DHCP - dynamic' => 3
                 ]])
                ->add('gw', CheckboxType::class, array(
                    'label'    => 'Is this IP default gateway address?',
                    'required' => false,
                ))         
                ->add('Assign', SubmitType::class, ['attr' => ['class' => 'btn btn-primary', 'style' => 'margin-bottom=15px']]); 
    }
    
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Address::class,
            'customer' => null,
            'network' => null,
        ));
    }
    
    
    
}
