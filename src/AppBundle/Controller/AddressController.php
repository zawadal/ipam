<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Service\NetworkService;
use AppBundle\Entity\Address;
use AppBundle\Entity\Network;
use AppBundle\Service\AddressService;
use AppBundle\Forms\AddressAssignForm;

class AddressController extends Controller
{
    
    /**
     * @Route("/address/list/{id}", name="addresslist")
     * @Template("IPAM/addresslist.html.twig")
    */
    public function listAddressAction($id, Request $request)
    {
        $em = $this->GetDoctrine()->GetEntityManager();
        $network = $em->getRepository('AppBundle:Network')->find($id);
        if($request->get('search'))
        {
                $search = $request->get('search');
                $addresslist = $this->getDoctrine()
                ->getRepository('AppBundle:Address')
                ->findLike($id, $search); 
                 return [
                    'addresslist' => $addresslist,
                    'network' => $id,
                    'search' => true
        ];
        }
        elseif($request->get('reclaim') && $request->get('check'))
        {
            foreach($request->get('check') as $singleIP)
            {
                $em = $this->GetDoctrine()->GetEntityManager();
                $addressid = $em->getRepository('AppBundle:Address')->findActiveAddressByIp($singleIP, $id);
                if($addressid)
                {
                    $this->reclaimAddressAction($addressid);   
                }
                else
                {
                     $this->addFlash(
                            'notice',
                            'Nothing to reclaim under '.$singleIP
                      );   
                }
            }
            return $this->redirectToRoute('addresslist', ['id' => $id]);
        }
        elseif($request->get('reclaim') && $request->get('check'))
        {
            foreach($request->get('check') as $singleIP)
            {
                $em = $this->GetDoctrine()->GetEntityManager();
                $addressid = $em->getRepository('AppBundle:Address')->findActiveAddressByIp($singleIP, $id);
                if($addressid)
                {
                    $this->reclaimAddressAction($addressid);   
                }
                else
                {
                     $this->addFlash(
                            'notice',
                            'Nothing to reclaim under '.$singleIP
                      );   
                }
            }
            return $this->redirectToRoute('addresslist', ['id' => $id]);
        }
        elseif($request->get('gw'))
        {
            if($request->get('check'))
            {
                if(count($request->get('check')) == 1)
                {
                    $result = $em->getRepository('AppBundle:Address')->findGwForNetwork($id);
                    if($result)
                    {

                        if($result->getIp() == $request->get('check')[0])
                        {
                             $result->setGw(false);
                             $em->flush();
                             $this->addFlash(
                                'success',
                                'Gateway IP address unset'
                                );   
                        }
                        else
                        {
                          $this->addFlash(
                                'error',
                                'Only one IP can be default gateway'
                          );    
                        }

                    }
                    else
                    {
                        $address = $em->getRepository('AppBundle:Address')->findActiveAddressByIp($request->get('check'), $id);
                        $address->setGw(true);
                        $em->flush();
                        $this->addFlash(
                                'success',
                                'Gateway IP address set'
                                );   
                    }
                }
                else
                {
                    $this->addFlash(
                                'error',
                                'Only one IP can be default gateway'
                          );  
                }
                return $this->redirectToRoute('addresslist', ['id' => $id]);
            }
            else
            {
                $this->addFlash(
                                'error',
                                'You must select at least one address'
                          );  
                return $this->redirectToRoute('addresslist', ['id' => $id]);
            }
        }
        elseif($request->get('unexclude') && $request->get('check'))
        {
            foreach($request->get('check') as $singleIP)
            {
                        $results = $em->getRepository('AppBundle:Address')->findExclusion($singleIP, $id);
                        if($results)
                        {
                            foreach($results as $result)
                            {
                               $em->remove($result);
                            }
                            $this->addFlash(
                            'notice',
                            'Existing exclusions were removed');
                        }
                        else
                        {
                            $this->addFlash(
                            'notice',
                            'No exclusion found for '.$singleIP);
                        }
            }
            $em->flush();
            
            return $this->redirectToRoute('addresslist', ['id' => $id]);
        }
        else
        {
          
            $schema = new NetworkService();
            $my = $schema->prepareAddressSchema($network->getNet(), $network->getNetmask());
            $addresslist = $this->getDoctrine()
                    ->getRepository('AppBundle:Address')
                    ->findAllActiveAddresses($network);          
            $paginator  = $this->get('knp_paginator');
            $pagination = $paginator->paginate( 
                $my,
                $request->query->getInt('page', 1),
                20
            );
            return [
            'schema' => $pagination,
            'addresslist' => $addresslist,
            'network' => $id
        ];
        }
       
    }
    
    /**
     * @Route("/address/history/{ip}/{network}", name="addresshistory")
    */
    public function addressHistoryAction($ip, $network)
    {
        $history = $this->getDoctrine()
                ->getRepository('AppBundle:Address')
                ->findInactiveAddresses($ip, $network);
       $historyCount = $this->getDoctrine()
                ->getRepository('AppBundle:Address')
                ->getInactiveAddressesCount($ip, $network);
        return $this->render('IPAM/addresshistory.html.twig', [
            'history' => $history,
            'network' => $network,
            'historyCount' => $historyCount
        ]);
    }
    
    
    
     /**
      * @Route("/address/assign/{network}/{ip}", name="addressassign")
      */
    public function adressAssignAction($network, $ip, Request $request)
    {
     
         $form = $this->createForm(AddressAssignForm::class, new Address(), array('customer' => $this->get('session')->get('context_id'), 'network' => $network));
         $form->handleRequest($request);

         if($form->isSubmitted() && $form->isValid())
         {
             $em = $this->GetDoctrine()->GetManager();
             $entity = $em->GetRepository('AppBundle:Network')->find($network);
             $address = $form->getData();
             $address->setIp($ip);
             if($address->getGw())
             {
                    $gw = $em->GetRepository('AppBundle:Address')->findGwForNetwork($network);
                    //instead of validator =//
                    if($gw)
                    {
                       $this->addFlash(
                                   'error',
                                   'Gateway already present in network!'
                       );
                       return $this->redirectToRoute('addresslist', ['id' => $network]); 
                    }
             }
            
             $entity->addAddress($address);
             $em->flush();
             $this->addFlash(
                            'success',
                            'IP address '.$ip.' assigned!'
              );

              $utilization = $em->GetRepository('AppBundle:Network')->incrementUtilization($address->getType(), $network);
              $this->addFlash(
                            'success',
                            'Utilization: '.($utilization).'%'
              );
               
              return $this->redirectToRoute('addresslist', ['id' => $network]);
         }
        
         return $this->render('IPAM/addressupdate.html.twig', [
            'form' => $form->CreateView(),
            'ip' => $ip     
        ]);
    } 
    
     /**
     * @Route("/address/reclaim/{id}", name="addressreclaim")
    */
    public function reclaimAddressAction($id)
    {
        $time = new \DateTime('now');
        $em = $this->GetDoctrine()->GetManager(); 
        $address = $em->getRepository('AppBundle:Address')
                ->find($id);
        $utilization = $em->GetRepository('AppBundle:Network')->decrementUtilization($address->getType(), $address->getNetwork());
        if($address->getType() <> 0)
        {
            $address->SetActive(false);
            $address->SetGw(false);
            $em->flush();
            $this->addFlash(
                                'success',
                                'IP address ' .$address->GetIP(). ' has been reclaimed!'
                  );

            $this->addFlash(
                            'success',
                            'Utilization: '.($utilization).'%'
              );
        }
        return $this->redirectToRoute('addresslist', ['id' => $address->getNetwork()->getId()]);
    }
    
    /**
     * @Route("/address/rollback/{id}", name="addressrollback")
    */
    public function addressRollbackAction($id)
    {
        $em = $this->getDoctrine()->GetManager();
        $previousIP = $em->getRepository('AppBundle:Address')
                ->find($id);
        $currentIP = $em->GetRepository('AppBundle:Address')->findActiveAddressByIp($previousIP->getIp(), $previousIP->getNetwork());
        if($currentIP)
        {
            $currentIP->setActive(false);
            $utilization = $em->GetRepository('AppBundle:Network')->decrementUtilization($currentIP->getType(), $currentIP->getNetwork());
            
        }
        else
        {
            $utilization = $em->GetRepository('AppBundle:Network')->incrementUtilization($previousIP->getType(), $previousIP->getNetwork());

        }
        $this->addFlash(
                            'success',
                            'Utilization: '.($utilization).'%'
        );
        $previousIP->setActive(true);
        $em->flush();
        
        $this->addFlash(
                            'success',
                            'IP address rolled back to previous value!'
            );
        
        return $this->redirectToRoute('addresslist', ['id' => $previousIP->GetNetwork()->getId()]);
    }
    
    /**
    * @Route("/nextaddress/assign/{network}", name="nextaddressassign")
    */
    public function nextAdressAssignAction($network, Request $request)
    {
        $sv = new NetworkService();
        $em = $this->GetDoctrine()->GetEntityManager();
        $nt = $em->GetRepository('AppBundle:Network')->find($network);
        $schema = $sv->prepareAddressSchema($nt->getNet(), $nt->getNetmask());
        $nextIp = $em->getRepository('AppBundle:Address')->findFirstFreeIp($nt, $schema);
        $this->addFlash(
                            'success',
                            'Next free IP address found: '.$nextIp
        );
        $form = $this->createForm(AddressAssignForm::class, new Address(), array('customer' => $this->get('session')->get('context_id'), 'network' => $network));
        $form->handleRequest($request);
        
         if($form->isSubmitted() && $form->isValid())
         {
             $entity = $em->GetRepository('AppBundle:Network')->find($network);
             $address = $form->getData();
             $address->setIp($nextIp);
             $entity->addAddress($address);
             $em->flush();
             $this->addFlash(
                            'success',
                            'IP address '.$nextIp.' assigned!'
              );

              $utilization = $em->GetRepository('AppBundle:Network')->incrementUtilization($address->getType(), $network);
              $this->addFlash(
                            'success',
                            'Utilization: '.($utilization).'%'
              );
               
              return $this->redirectToRoute('addresslist', ['id' => $network]);
         }
        
        return $this->render('IPAM/addressupdate.html.twig', [
            'form' => $form->CreateView(),
            'ip' => $nextIp
        ]);
    } 
    
 
    
    
    
}