<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Service\IscDhcpService;

class DhcpController extends Controller {
  
    public function listDhcpOptions() {
        
        $DHCPservice = new IscDhcpService();
        $status = $DHCPservice->getDhcpServiceStatus();
        if($status)
        {
            
        }
        else
        {
            
        }
        return 3;
        
    }
}
