<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Entity\Device;
use AppBundle\Entity\Address;
use AppBundle\Forms\DeviceAddForm;
use AppBundle\Forms\DeviceEditForm;
use AppBundle\Service\AddressService;

class DeviceController extends Controller
{
  
    
    /**
    * @Route("/device/list/", name="devicelist")
    * @Template("IPAM/devicelist.html.twig")
    */
    public function deviceListAction(Request $request, $search = null, $type = null)
    {
        if(!$this->get('session')->get('context_id'))
        {
            $this->addFlash(
                    'error',
                    'No context found!'
            );   
            return $this->redirectToRoute('customerenumerate');
        }
        if(!$search)
        {
            $search = $request->query->get('search');
        } 
        if($search)
        {
             try 
             {
                $devices = $this->getDoctrine()
                     ->getRepository('AppBundle:Device')
                     ->findLikeSearch($search, $type, $this->get('session')->get('context_id'));    
             } 
             catch(\Doctrine\DBAL\DBALException $e)
             {
                $this->addFlash(
                                 'error',
                                 'Device search failed'
                 );
                return $this->redirectToRoute('devicelist');  
             }  
        }
        else
        {
            $devices = $this->getDoctrine()
                ->getRepository('AppBundle:Device')
                ->findAllInContext($this->get('session')->get('context_id'));
        }
        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate( 
                     $devices, 
                     $request->query->getInt('page', 1),
                     20
         );
        $content = $this->renderView('IPAM/devicelist.html.twig', array('devices' => $pagination, 'search' => $search));
        return new Response($content);

    }

     /**
      * @Route("/device/add", name="adddevice")
      * @Template("IPAM/adddevice.html.twig")
      */
    public function deviceAddAction(Request $request)
    {
     
         $form = $this->createForm(DeviceAddForm::class, new Device(), array('customer' => $this->get('session')->get('context_id')));
         $form->handleRequest($request);
         if($form->isSubmitted() && $form->isValid())
         {
             $device = $form->getData();
             $em = $this->GetDoctrine()->GetManager();
             //prepare mac
             if($device->getMac() <> null)
             {
                 $macprepared =  new AddressService();
                 $device->setMac($macprepared->prepareMac($device->getMac()));
             }
             $em->persist($device);
             $em->flush();
             $this->addFlash(
                            'success',
                            'Device succesfully added'
              );

              return $this->redirectToRoute('devicelist');
         }
        
         return [
            'form' => $form->CreateView(),  
        ];
    } 

    /**
    * @Route("/device/delete/{id}", name="devicedelete")
    * @Template("IPAM/devicelist.html.twig")
    */
    public function deviceDeleteAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $address = $em->getrepository('AppBundle:Address')->findAddressForDevice($id);
        if($address)
        {
                $this->addFlash(
                                'error',
                                'Device not removed! There are active IP address in plan for this device: '.$address->getIp().' in '.$address->getNetwork()->getNet(). ' network!'
                );
                return $this->redirectToRoute('devicelist');

        }
        try      
        {        
            $address = $em->getrepository('AppBundle:Address')->removeHistoryForDevice($id);    
            $em->remove($em->getRepository('AppBundle:Device')
                ->find($id));
            $em->flush();  
            $this->addFlash(
                            'success',
                            'Device removed'
            );
        } 
        catch (\Doctrine\DBAL\DBALException $ex) {
                      $this->addFlash(
                                'error',
                                'Device not removed! There are active connections to element in database'
            );
        }
        return $this->redirectToRoute('devicelist');
    }
    
    /**
    * @Route("/device/edit/{id}", name="deviceedit")
    * @Template("IPAM/adddevice.html.twig")
    */
    public function deviceEditAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $device = $em->getRepository('AppBundle:Device')
                ->find($id);
        $form = $this->createForm(DeviceEditForm::class, $device, array('customer' => $this->get('session')->get('context_id')));
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
             $device = $form->getData();
             $em = $this->GetDoctrine()->GetManager();
             //prepare mac
             if($device->getMac() <> null)
             {
                 $macprepared =  new AddressService();
                 $device->setMac($macprepared->prepareMac($device->getMac()));
                 
             }
             $em->persist($device);
             $em->flush();
             $this->addFlash(
                            'success',
                            'Device succesfully updated'
              );

              return $this->redirectToRoute('devicelist');
         }
         return [
            'form' => $form->CreateView(),  
        ];
    }
}