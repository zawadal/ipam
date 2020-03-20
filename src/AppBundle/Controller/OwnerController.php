<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Entity\Owner;
use AppBundle\Forms\OwnerAddForm;
use AppBundle\Forms\OwnerEditForm;

class OwnerController extends Controller
{
  
    
    /**
    * @Route("/owner/list", name="ownerlist")
    * @Template("IPAM/ownerlist.html.twig")
    */
    public function ownerListAction(Request $request, $type= null, $search = null)
    {
        //context safe
        if(!$this->get('session')->get('context_id'))
        {
            $this->addFlash(
                    'error',
                    'No context found!'
            );   
            return $this->redirectToRoute('customerenumerate');
        }
        //if no direct variable, try to get it from request
        if(!$search)
        {
            $search = $request->query->get('search');
        } 
        //if in search mode 
        if($search)
        {
             try 
             {
                $owners = $this->getDoctrine()
                     ->getRepository('AppBundle:Owner')
                     ->findLikeSearch($search, $type, $this->get('session')->get('context_id'));    
             } 
             catch(\Doctrine\DBAL\DBALException $e)
             {
                $this->addFlash(
                                 'error',
                                 'Owner search failed'
                 );
                return $this->redirectToRoute('ownerlist');  
             }  
        }
        else
        {
            $owners = $this->getDoctrine()
                ->getRepository('AppBundle:Owner')
                ->findAllInContext($this->get('session')->get('context_id'));
        }
        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate( 
                     $owners, 
                     $request->query->getInt('page', 1),
                     20
         );
        $content = $this->renderView('IPAM/ownerlist.html.twig', array('owners' => $pagination, 'search' => $search));
        return new Response($content);

    }
    
    
    
    /**
    * @Route("/owner/add", name="addowner")
    * @Template("IPAM/owneradd.html.twig")
    */
    public function ownerAddAction(Request $request)
    {
         $form = $this->createForm(OwnerAddForm::class, new Owner());
         $form->handleRequest($request);
         if($form->isSubmitted() && $form->isValid())
         {
             $type = $form->getData();
             $em = $this->GetDoctrine()->GetManager();
             $type->setCustomer($em->getRepository('AppBundle:Customer')->find($this->get('session')->get('context_id')));
             $em->persist($type);
             $em->flush();
             $this->addFlash(
                            'success',
                            'Owner succesfully added'
              );

              return $this->redirectToRoute('ownerlist');
         }
        
         return [
            'form' => $form->CreateView(),  
        ];
    } 
    
    /**
    * @Route("/owner/delete/{id}", name="ownerdelete")
    */
    public function deleteTypeAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        try 
        {
            $em->remove($em->getRepository('AppBundle:Owner')
                ->find($id));
            $em->flush();  
            $this->addFlash(
                            'success',
                            'Owner removed'
            );
        } 
        catch (\Doctrine\DBAL\DBALException $ex) {
                      $this->addFlash(
                                'error',
                                'Owner not removed! There are active connections to element in database. Remove owner devices first.'
            );
        }
        return $this->redirectToRoute('ownerlist');
    }
    
    /**
    * @Route("/owner/edit/{id}", name="owneredit")
    * @Template("IPAM/owneradd.html.twig")
    */
    public function ownerEditAction($id, Request $request)
    {
         $em = $this->GetDoctrine()->GetManager();
         $owner = $em->getRepository('AppBundle:Owner')->find($id);       
         $form = $this->createForm(OwnerEditForm::class, $owner);
         $form->handleRequest($request);
         if($form->isSubmitted() && $form->isValid())
         {
             $type = $form->getData();
             $em->persist($type);
             $em->flush();
             $this->addFlash(
                            'success',
                            'Owner succesfully updated'
              );

              return $this->redirectToRoute('ownerlist');
         }
        
         return [
            'form' => $form->CreateView(),  
        ];
    } 
}