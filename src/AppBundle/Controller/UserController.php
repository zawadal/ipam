<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\User;
use AppBundle\Forms\UserAddForm;
use AppBundle\Forms\UserEditForm;
use AppBundle\Forms\UserPasswordForm;

class UserController extends Controller
{
    /**
      * @Route("/user/add", name="adduser")
      * @Template("IPAM/adduser.html.twig")
     */
    public function userAddAction(Request $request)
    {
         $form = $this->createForm(UserAddForm::class, new User());
         $form->handleRequest($request);
         if($form->isSubmitted() && $form->isValid())
         {
             $data = $form->GetData();
             $userManager = $this->get('fos_user.user_manager');
             if(!$userManager->FindUserByUsername($data->getUsername()) && !$userManager->FindUserByEmail($data->getEmail()) )
             {
                 $userManager->updateUser($data);
                 $this->addFlash(
                            'success',
                            'User succesfully added'
                );
             } 
             else
             {
                $this->addFlash(
                               'error',
                               'User could not be added - duplicated entries detected'
                 );
             }

         }
         return ['form' => $form->createView()];
    }
    
     /**
      * @Route("/user/edit/{id}", name="edituser")
      * @Template("IPAM/edituser.html.twig")
      */
    public function userEditAction($id, Request $request)
    {
         $userManager = $this->get('fos_user.user_manager');
         $form = $this->createForm(UserEditForm::class, $userManager->FindUserBy(array('id' => $id)));
         $form->handleRequest($request);
         if($form->isSubmitted() && $form->isValid())
         {
                $data = $form->GetData();
                $userManager->updateUser($data);
                $this->addFlash(
                            'success',
                            'User succesfully updated'
                );
         }
         return ['form' => $form->createView()];
    }
    
   
}