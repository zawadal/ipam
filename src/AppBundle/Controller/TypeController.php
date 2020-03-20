<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Type;
use AppBundle\Forms\TypeAddForm;
use AppBundle\Forms\TypeEditForm;

class TypeController extends Controller
{
    /**
    * @Route("/type/list", name="typelist")
    * @Template("IPAM/typelist.html.twig")
    */
    public function listTypesAction()
    {

        $types = $this->getDoctrine()
                ->getRepository('AppBundle:Type')
                ->findAll();
        return ['types' => $types];
    }
    
    /**
    * @Route("/type/add", name="addtype")
    * @Template("IPAM/typeadd.html.twig")
    */
    public function typeAddAction(Request $request)
    {
     
         $form = $this->createForm(TypeAddForm::class, new Type());
         $form->handleRequest($request);
         if($form->isSubmitted() && $form->isValid())
         {
             $type = $form->getData();
             $em = $this->GetDoctrine()->GetManager();
             $em->persist($type);
             $em->flush();
             $this->addFlash(
                            'success',
                            'Type succesfully added'
              );

              return $this->redirectToRoute('typelist');
         }
        
         return [
            'form' => $form->CreateView(),  
        ];
    } 
    
    /**
    * @Route("/type/delete/{id}", name="typedelete")
    * @Template("IPAM/typelist.html.twig")
    */
    public function deleteTypeAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        try 
        {
            $em->remove($em->getRepository('AppBundle:Type')
                ->find($id));
            $em->flush();  
            $this->addFlash(
                            'success',
                            'Type removed'
            );
        } 
        catch (\Doctrine\DBAL\DBALException $ex) {
                      $this->addFlash(
                                'error',
                                'Type not removed! There are active connections to element in database'
            );
        }

        return $this->redirectToRoute('typelist');
    }
    
    /**
    * @Route("/type/edit/{id}", name="typeedit")
    * @Template("IPAM/typeadd.html.twig")
    */
    public function editTypeAction($id, Request $request)
    {
         $em = $this->GetDoctrine()->GetManager();
         $type = $em->getrepository('AppBundle:Type')
                 ->find($id);
         $form = $this->createForm(TypeEditForm::class, $type);
         
         $form->handleRequest($request);
         if($form->isSubmitted() && $form->isValid())
         {
             $type = $form->getData();
             $em->persist($type);
             $em->flush();
             $this->addFlash(
                            'success',
                            'Type succesfully updated'
              );

              return $this->redirectToRoute('typelist');
         }
        
         return [
            'form' => $form->CreateView(),  
        ];
    } 
}