<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Customer;
use AppBundle\Entity\User;
use AppBundle\Forms\CustomerAddForm;
use AppBundle\Forms\CustomerEditForm;
use AppBundle\Forms\CustomerEnumerateForm;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class CustomerController extends Controller
{
   
    
     /**
      * @Route("/customer/add", name="addcustomer")
      * @Template("IPAM/addcustomer.html.twig")
      */
    public function customerAddAction(Request $request)
    {
         $em = $this->GetDoctrine()->GetManager();
         $form = $this->createForm(CustomerAddForm::class, new Customer());
         $form->handleRequest($request);
         if($form->isSubmitted() && $form->isValid())
         {
             $customer = $form->getData();

             $em->persist($customer);
             $em->flush();
             $this->addFlash(
                            'success',
                            'Customer succesfully added'
              );

              return $this->redirectToRoute('customerlist');
         }
        
         return [
            'form' => $form->CreateView(),  
        ];
    } 
    
    
    /**
    * @Route("/customer/search/", name="customerlist")
    * @Template("IPAM/customerlist.html.twig")
    */
    public function customerListAction(Request $request, $search = null, $type = null)
    {
    
        if(!$search)
        {
            $search = $request->query->get('search');
        } 
        if($search)
        {
             try 
             {
                $customers = $this->getDoctrine()
                     ->getRepository('AppBundle:Customer')
                     ->findLikeSearch($search, $type);    
             } 
             catch(\Doctrine\DBAL\DBALException $e)
             {
                $this->addFlash(
                                 'error',
                                 'Customer search failed'
                 );
                return $this->redirectToRoute('customerlist');  
             }  
        }
        else
        {
            $customers = $this->getDoctrine()
                ->getRepository('AppBundle:Customer')
                ->findAll();
        }
        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate( 
                     $customers, 
                     $request->query->getInt('page', 1),
                     20
         );
        $content = $this->renderView('IPAM/customerlist.html.twig', array('customers' => $pagination, 'search' => $search));
        return new Response($content);

    }
    
    /**
    * @Route("/customer/delete/{id}", name="customerdelete")
    */
    public function deleteCustomerAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        try
        {
            $em->remove($em->getRepository('AppBundle:Customer')
                ->find($id));
            $em->flush();  
            $this->addFlash(
                            'success',
                            'Customer removed'
            );
            //if customer is in current contexct - remove context
            if($this->get('session')->get('context_id') == $id)
            {
                 $this->addFlash(
                            'warning',
                            'You have been removed from current context'
                 );
                $this->get('session')->remove('context_id');
                $this->get('session')->remove('context_name');
            }
            return $this->redirectToRoute('customerenumerate');
            
        } 
   
        
        catch (\Doctrine\DBAL\DBALException $ex) {
                      $this->addFlash(
                                'error',
                                'Customer not removed! There are active connections to element in database'
            );
        }
        return $this->redirectToRoute('customerlist');
    }
    
    /**
    * @Route("/customer/edit/{id}", name="customeredit")
    * @Template("IPAM/addcustomer.html.twig")
    */
    public function customerEditAction($id, Request $request)
    {
         $em = $this->GetDoctrine()->GetManager();
         $customer = $em->getRepository('AppBundle:Customer')
                ->find($id);       
         $form = $this->createForm(CustomerEditForm::class, $customer);
        
         $form->handleRequest($request);
         if($form->isSubmitted() && $form->isValid())
         {
             $type = $form->getData();
             $em->persist($type);
             $em->flush();
             $this->addFlash( 
                            'success',
                            'Customer succesfully updated'
              );

              return $this->redirectToRoute('customerlist');
         }
        
         return [
            'form' => $form->CreateView(),  
        ];
    } 
    
    
     /**
    * @Route("/", name="customerenumerate")
    * @Template("IPAM/customerenumerate.html.twig")
    */
    public function customerEnumerateAction(Request $request)
    {
        $em = $this->GetDoctrine()->GetManager();
        $allcustomers = $em->GetRepository('AppBundle:Customer')->findAll();
        foreach($allcustomers as $customer)
        {
            $tablicza[$customer->getName()] = $customer->getId();
        }
        $form = $this->createFormBuilder($tablicza)
            ->add('customer', ChoiceType::class, array(
                'choices' =>  $tablicza,
                'attr' => ['class' => 'form-control', 'style' => 'margin-bottom:15px'],
            ))
            ->add('context', SubmitType::class, array('label' => 'Add to context', 'attr' => ['class' => 'form-control', 'style' => 'margin-bottom:15px']))
            ->getForm();
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
         {
                        $selectedcustomer =  $em->GetRepository('AppBundle:Customer')->find($form['customer']->getData());
                        $session = $this->get('session');
                        if($session)
                        {
                            $session->set('context_name', $selectedcustomer->getName());
                            $session->set('context_id', $selectedcustomer->getId());
                        }
                        else
                        {
                                $this->addFlash(
                                    'error',
                                 'Session error! Try  to relog!'
                                ); 
                                return $this->redirectToRoute('logout');
                        }
                return $this->redirectToRoute('networklist', ['customerid' => $this->get('session')->get('context_id')]);
         }
         return [
            'form' => $form->CreateView(),  
        ];
    } 
    
}