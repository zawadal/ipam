<?php

namespace AppBundle\Forms;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\HttpFoundation\Session\Session;

use AppBundle\Entity\Network;


/**
 * Description of NetworkForm
 *
 * @author l00gan
 */
class NetworkAddForm extends AbstractType {
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $session = new Session();
        $builder
                 ->add('net', TextType::class, ['attr' => ['class' => 'form-control', 'style' => 'margin-bottom:15px'], 'label' => 'Network'])
                 ->add('netmask', TextType::class, ['attr' => ['class' => 'form-control', 'style' => 'margin-bottom:15px']])
                 ->add('vlanId', TextType::class, ['required' => false, 'attr' => ['class' => 'form-control', 'style' => 'margin-bottom:15px']])
                 ->add('description', TextareaType::class, ['required' => false, 'attr' => ['class' => 'form-control', 'style' => 'margin-bottom:15px']])
                 ->add('add', SubmitType::class, ['attr' => ['class' => 'btn btn-primary', 'style' => 'margin-bottom:15px', 'label' => 'Add network']]);
                    
        
    }
    
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Network::class,
        ));
    }
    
    
    
}
