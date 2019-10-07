<?php

namespace AppBundle\Forms;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;


/**
 * Description
 *
 * @author l00gan
 */
class SearchForm extends AbstractType {
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
                 ->add('searchclass', ChoiceType::class, ['attr' => ['class' => 'form-control', 'style' => 'margin-bottom=15px'],
                     'placeholder' => '',
                    'choices'  => [                       
                        'Device' => 'Device',
                        'Owner' => 'Owner',
                        'Customer' => 'Customer'
                 ]])
                 ->addEventListener(FormEvents::PRE_SUBMIT, [$this, 'onPreSubmit'])
                 ->addEventListener(FormEvents::PRE_SET_DATA, [$this, 'onPreSet'])
                 ->add('search', SubmitType::class, ['attr' => ['class' => 'btn btn-primary', 'style' => 'margin-bottom=15px']])
                 ->getForm();
    }
    public function onPreSet(FormEvent $event){
                     $formdata = $event->getData();   
                     $form = $event->getForm();
                     if($formdata['searchclass'] === null)
                     {
                        $form->add('searchtype', ChoiceType::class, ['attr' => ['disabled' => true, 'class' => 'form-control', 'style' => 'margin-bottom=15px']]);
                        $form->add('searchphrase', TextType::class, ['attr' => ['disabled' => true, 'class' => 'form-control',  'style' => 'margin-bottom=15px']]);
                     }    
                    
    }
    public function onPreSubmit(FormEvent $event) {
                     $formdata = $event->getData();   
                     $form = $event->getForm();
                     if ($formdata === null)
                     {
                           return;
                     }
                     if($formdata['searchclass'])
                     {
                         switch($formdata['searchclass']){
                                   case 'Device': 
                                        $form->add('searchtype', ChoiceType::class, ['attr' => ['class' => 'form-control', 'style' => 'margin-bottom=15px'],
                                            'choices'  => [                       
                                                'Hostname' => 'hostname',
                                                'Serial' => 'serial',
                                                'Description'=> 'description',
                                                'MAC'=> 'MAC',
                                                'Owner' => 'owner',
                                                'Type' => 'type'
                                         ]]);
                                        break;
                                   case 'Owner':
                                            $form->add('searchtype', ChoiceType::class, ['attr' => ['class' => 'form-control', 'style' => 'margin-bottom=15px'],
                                            'choices'  => [                       
                                                'First name' => 'firstname',
                                                'Last Name' => 'lastname',
                                                'Location' => 'location',
                                                'Phone' => 'phone',
                                                'Email' => 'email',
                                                'Description' => 'description',
                                                'Customer' => 'customer'
                                         ]]);
                                        break;
                                   case 'Customer':
                                            $form->add('searchtype', ChoiceType::class, ['attr' => ['class' => 'form-control', 'style' => 'margin-bottom=15px'],
                                            'choices'  => [                       
                                                'Name'=>'name',
                                                'Descritpion'=>'descritpion',
                                                'Street'=>'street',
                                                'Building'=>'building',
                                                'City'=>'city',
                                                'Code'=>'code',
                                                'Country'=>'country'
                                         ]]);
                                        break;
                                   default:
                                            $form->add('searchtype', ChoiceType::class, ['attr' => ['class' => 'form-control', 'style' => 'margin-bottom=15px'],
                                            'choices'  => [                       
                                                
                                         ]]);
                                        break;
                            }
                        }
                   }
        
        
    }

