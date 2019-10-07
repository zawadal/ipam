<?php

namespace AppBundle\Service;

use AppBundle\Service\NetworkCalculator;


class NetworkService
{

        public function prepareAddressSchema($net, $netmask)
        {
            $IPschema = [];
            $subnetCalculatorObject = new NetworkCalculator($net, $netmask);
            $firstIP = $subnetCalculatorObject->GetFirstAddress();
            $lastIP = $subnetCalculatorObject->getLastAddress();
            $currentIP = $nextIP = $firstIP;
            if($netmask >= 24 && $netmask < 31)
            {
                do {        
                    $currentIP = $nextIP;
                    $IPschema[] = $currentIP;
                    $quad = explode('.',$currentIP);
                    $quad[3]++;
                    $nextIP = implode('.', $quad);
                }
                while($currentIP <> $lastIP);
                return $IPschema;
            }
            if($netmask >= 16 && $netmask < 24)
            {
                do {        
                    $currentIP = $nextIP;
                    $IPschema[] = $currentIP;
                    $quad = explode('.',$currentIP);
                    if($quad[3] == 255)
                    {
                        $quad[2]++;
                        $quad[3]=0;
                    }
                    else
                    {
                        $quad[3]++;    
                    }

                    $nextIP = implode('.', $quad);
                }
                while($currentIP <> $lastIP);
                return $IPschema;     
                    
            } 
            if($netmask >= 8 && $netmask < 16)
            {
                do {        
                    $currentIP = $nextIP;
                    $IPschema[] = $currentIP;
                    $quad = explode('.',$currentIP);
                    if($quad[2] == 255 && $quad[3] == 255)
                    {
                        $quad[1]++;
                        $quad[2]=0;
                    }
                    elseif($quad[3] == 255)
                    {
                        $quad[2]++;
                        $quad[3]=0;
                    }
                    else
                    {
                        $quad[3]++;    
                    }

                    $nextIP = implode('.', $quad);
                }
                while($currentIP <> $lastIP);
                return $IPschema;     
            }
            if($netmask < 8 && $netmask > 0)
            {
                do {        
                    $currentIP = $nextIP;
                    $IPschema[] = $currentIP;
                    $quad = explode('.',$currentIP);
                    if($quad[1] == 255 && $quad[2] == 255 && $quad[3] == 255)
                    {
                        $quad[0]++;
                        $quad[1]=0;
                        $quad[2]=0;
                        $quad[3]=0;
                    }
                    elseif($quad[2] == 255 && $quad[3] == 255)
                    {
                        $quad[1]++;
                        $quad[2]=0;
                        $quad[3]=0;
                    }
                    elseif($quad[3] == 255)
                    {
                        $quad[2]++;
                        $quad[3]=0;
                    }
                    else
                    {
                        $quad[3]++;    
                    }

                    $nextIP = implode('.', $quad);
                }
                while($currentIP <> $lastIP);
                return $IPschema;         
            }

        }
        
}