<?php

namespace AppBundle\Forms;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

use AppBundle\Entity\Network;


/**
 * Description of networkForm
 *
 * @author l00gan
 */
class NetworkChangeForm extends AbstractType {
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('description', TextareaType::class, ['attr' => ['class' => 'form-control', 'style' => 'margin-bottom=15px']])
                ->add('change', SubmitType::class, ['attr' => ['class' => 'btn btn-primary', 'style' => 'margin-bottom=15px']]);
              
    }
    
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Network::class,
        ));
    }
    
    
    
}
