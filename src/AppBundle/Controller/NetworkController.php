<?php

namespace AppBundle\Controller;

use AppBundle\Forms\NetworkAddForm;
use AppBundle\Forms\NetworkChangeForm;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

//serializer for Entity->JSON ;(
use Symfony\Component\Serializer\Serializer;    
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

use AppBundle\Service\IscDhcpService;
use AppBundle\Service\NetworkCalculator;

use AppBundle\Entity\Network;
use AppBundle\Entity\Customer;

use Doctrine\DBAL\DBALException;

//charts
use CMEN\GoogleChartsBundle\GoogleCharts\Charts\PieChart;

class NetworkController extends Controller
{
    /**
     * @Route("/network/listjson", name="networklistjson")
     * 
     */
    //to be secured in future 
    public function listNetworkJsonAction()
    {
        $networks = $this->getDoctrine()
                ->getRepository('AppBundle:Network')
                ->findAllJson();
        $serializer = new Serializer([new ObjectNormalizer()], [new JsonEncoder()]);
        $jsonSerialized = $serializer->serialize($networks, 'json');
        $response = new Response($jsonSerialized);
        $response->setCharset('UTF-8');
        return $response;
    }
    
    /**
     * @Route("/network/list/{customerid}", name="networklist")
     * @Template("IPAM/networklist.html.twig")
     */
    
    public function listNetworkAction(Request $request, $customerid = null)
    {
        
        if(!$this->get('session')->get('context_id'))
        {
            $this->addFlash(
                    'error',
                    'No context found!'
            );   
            return $this->redirectToRoute('customerenumerate');
        }
        
        
        $networks = $this->getDoctrine()
                ->getRepository('AppBundle:Network')
                ->findForCustomer($customerid);

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate( 
                $networks,
                $request->query->getInt('page', 1),
                20
               );
     
        return ['networks' => $pagination];
    }

    /**
    *  @Route("/network/search", name="searchnetwork") 
    *  @Template("IPAM/networklist.html.twig")
    * 
    */
    public function searchNetworkAction(Request $request)
    {
        $search = $request->get('search');
        $networks = $this->getDoctrine()
                ->getRepository('AppBundle:Network')
                ->findLikeSearch($search, $this->get('session')->get('context_id')); 
        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate( 
                $networks, 
                $request->query->getInt('page', 1),
                20
        );
        return ['networks' => $pagination, 'search' => true];
    }
    
    /**
     * @Route("/network/add", name="networkadd")
     * @Template("IPAM/networkadd.html.twig")
    */
    public function addNetworkAction(Request $request)
    {

         $form = $this->createForm(NetworkAddForm::class, new Network());
         $form->handleRequest($request);

         //Submmition handling
         if($form->isSubmitted() && $form->isValid())
         {
             $network = $form->GetData();
             $calculator = new NetworkCalculator($network->GetNet(), $network->GetNetmask());
             if($calculator->GetNetworkPortion() == $network->GetNet())
             {
                if(filter_var($network->GetNet(), FILTER_VALIDATE_IP))
                {
                   $em = $this->getDoctrine()->getManager();
                   $dhcp = new IscDhcpService();
		   #$newdhcpconfig = $dhcp->newDhcpSubnetConfig($network->getNet(), $calculator->getSubnetMask(), file_get_contents('/etc/dhcp/dhcpd.conf'));
		   #$newdhcpconfig = $dhcp->deleteDhcpSubnetConfig($network->getNet(), $calculator->getSubnetMask(), file_get_contents('/etc/dhcp/dhcpd.conf'));
		   #$newdhcpconfig = $dhcp->addDhcpGatewayConfig($network->getNet(), $calculator->getSubnetMask(), file_get_contents('/etc/dhcp/dhcpd.conf'), '10.0.1.1');
		   #$newdhcpconfig = $dhcp->deleteDhcpGatewayConfig($network->getNet(), $calculator->getSubnetMask(), file_get_contents('/etc/dhcp/dhcpd.conf'));
#		   $newdhcpconfig = $dhcp->addIpsToDhcpRangeConfig($network->getNet(), $calculator->getSubnetMask(),  file_get_contents('/etc/dhcp/dhcpd.conf'), ['10.0.1.8']);
		   #$newdhcpconfig = $dhcp->addDhcpHostConfig($network->getNet(), $calculator->getSubnetMask(),  file_get_contents('/etc/dhcp/dhcpd.conf'), 'morelka01');
		   #$newdhcpconfig = $dhcp->deleteDhcpHostConfig($network->getNet(), $calculator->getSubnetMask(),  file_get_contents('/etc/dhcp/dhcpd.conf'), 'morelka01');
		   #$newdhcpconfig = $dhcp->deleteIpsFromDhcpRangeConfig('10.0.1.0', '255.255.255.0', file_get_contents('/etc/dhcp/dhcpd.conf'), ['10.0.1.8']);
		   if(isset($newdhcpconfig) and $newdhcpconfig)
		   {
			file_put_contents('/etc/dhcp/dhcpd.conf', trim($newdhcpconfig));
		   }
		   #var_dump($newdhcpconfig);
		   #die();
		   $network->setCustomer($em->getRepository('AppBundle:Customer')->find($this->get('session')->get('context_id')));
                   $network->setFirstAddress($calculator->getFirstAddress());
                   $network->setLastAddress($calculator->getLastAddress());
                   $network->setMaxHosts($calculator->getNumberAddressableHosts());
                   $em->persist($network);
                   $em->flush();
                   $this->addFlash(
                               'success',
                               'Network Added'
                   );
                    return $this->redirectToRoute('networklist', ['customerid' => $this->get('session')->get('context_id')]);

                }
            }
            else
            {
                $this->addFlash(
                               'error',
                               'Provided address is not network address!'
                   );   
                 return $this->redirectToRoute('networklist', ['customerid' => $this->get('session')->get('context_id')]);
            }
                               
               
         }
         return ['form' => $form->CreateView()];
    }  
    
     /**
     * @Route("/network/remove/{id}", name="networkremove")
     */
   
    public function removeNetworkAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($em->getRepository('AppBundle:Network')
                ->find($id));
        $em->flush();  
        $this->addFlash(
                            'success',
                            'Network removed'
            );
         return $this->redirectToRoute('networklist', ['customerid' => $this->get('session')->get('context_id')]);
        
    }
    
     /**
     * @Route("/network/details/{id}", name="networkdetails")
     * @Template("IPAM/networkdetails.html.twig")
     */
    
    public function networkDetailsAction($id)
    {
        
        $network = $this->get('doctrine')
                ->getRepository('AppBundle:Network')
                ->find($id);
        $details = new networkCalculator($network->getNet(), $network->getNetmask());

        $pieChart = new PieChart();
        $pieChart->getData()->setArrayToDataTable(
        [['Utilization', 'Units'],
         ['IPAM',     $network->getIPAMutilization()],
         ['DHCP reserved',      $network->getDHCPReservedUtilization()],
         ['DHCP dynamic',  $network->getDHCPDynamicUtilization()],
         ['Free',  ($details->getNumberAddressableHosts()-($network->getDHCPDynamicUtilization()+ $network->getDHCPReservedUtilization()+$network->getIPAMutilization()))],   
        ]
        );
        $pieChart->getOptions()->setTitle('Network utilization');
        $pieChart->getOptions()->setColors(['#666699','#ff99bb', '#80ced6', '#668cff']);
        $pieChart->getOptions()->setHeight(500);
        $pieChart->getOptions()->setSliceVisibilityThreshold(0);
        $pieChart->getOptions()->setWidth(900);
        $pieChart->getOptions()->getTitleTextStyle()->setBold(true);

        $pieChart->getOptions()->getTitleTextStyle()->setFontName('Verdana');
        $pieChart->getOptions()->getTitleTextStyle()->setFontSize(18);

        return  [
            'details' => $details->GetSubnetArrayReport(),
            'network' => $network,
            'piechart' => $pieChart
        ];
    }
    
    /**
     * @Route("/network/change/{id}", name="networkchange")
     * 
     */
    public function changeNetworkDescriptionAction($id, Request $request)
    {
         $em = $this->get('doctrine')->getmanager();
         $network =  $em->Getrepository('AppBundle:Network')->find($id);
         if($request->get('change'))
         {
           
            try
            {
                if($request->get('description'))
                {
                $network->setDescription($request->get('description'));
                }
                if($request->get('vlanid'))
                {
                     $network->setVlanid($request->get('vlanid'));
                }
                    $em->flush();
                    $this->addFlash(
                            'success',
                            'Network metadata changed'
                    );  
                } 
                                 
            catch (DBALException $ex) 
            {
                    $exception = 1;
                    $this->addFlash(
                            'error',
                            'Network VLAN unchanged - VLANID or description values not valid or too long'
                );
            }
 
         }
          return $this->redirectToRoute('networklist', ['customerid' => $this->get('session')->get('context_id')]);
    }  
   
}
