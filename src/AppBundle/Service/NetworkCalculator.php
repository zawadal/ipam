<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace AppBundle\Service;

use IPv4\SubnetCalculator;


/**
 * Description of networkCalculator
 *
 * @author l00gan
 */
class NetworkCalculator extends SubnetCalculator
{
     public function getFirstAddress()
        {
            $quadsip = explode('.', $this->getIpAddress());
            ++$quadsip[3];
            $firstaddress = implode('.', $quadsip);
            return $firstaddress;  
        }   
          
        
        
        public function getLastAddress()
        {
            $quadsbroadcast = explode('.', $this->getBroadcastAddress());
            $quadsbroadcast[3]--;
            $lastaddress = implode('.', $quadsbroadcast);
            return $lastaddress;
        } 
        
        public function IsNetwork()
        {
            
        }
        
}
