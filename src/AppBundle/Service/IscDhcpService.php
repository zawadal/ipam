<?php

namespace AppBundle\Service;

//Class written to allow altering dhcpd.conf 
class IscDhcpService
{
        public function returnDhcpConfigBlock($configfile, $startpos)
        {
                            $bracketcounter = 0; //if starting after 1st bracket else put 0
                            for($i=$startpos; $i < strlen($configfile); $i++)
                            {
                                    if($configfile[$i] == "{")
                                    {
                                        $bracketcounter++;
                                    }
                                    elseif($configfile[$i] == "}")
                                    {
                                       $bracketcounter--; 
                                       if($bracketcounter == 0)
                                       {
                                           $endpos = $i;
                                           break;
                                       }
                                    }
                            }
                            if($endpos <> $startpos)
                            {
                                $start = min($startpos, $endpos);
                                $length = abs($startpos - $endpos);
                                return substr($configfile, $start, $length+1);
                            }
                            else
                            {
                                return 0;//error config not good
                            }

        }
        public function findDhcpSubnetConfig($subnet, $netmask, $configfile)
        {
            /* ex searching :
                1. Find first occurance: subnet <ip> netamsk <mask>, set $br=0
             *  2. save first position
             *  3. search for { and } in loop 
             *  4. if { find increment value $bracketcounter++, if found } decrement $bracketcounter--, if } found check if $br == 0 to exit loop - save last posiotion
             *  5. you have whole configpart 
             */
            $endpos = $startpos = strpos($configfile, "subnet ".$subnet." netmask ".$netmask);
            if($startpos)
            {
                return $this->returnDhcpConfigBlock($configfile, $startpos);
            }
            else
            {
                return 0; //no such network
            }

        }
        
        
        //$networkconfigarea = "##-#-##NETWORK CONFIGURATION##-#-##"; 
        //this must be placed in DHCPD.CONF to app know where to push subnet declarations  
        public function newDhcpSubnetConfig($subnet, $netmask, $configfile)
        {
             if(!$this->findDhcpSubnetConfig($subnet, $netmask, $configfile))
             {
                 $networkconfigarea = "##-#-##NETWORK CONFIGURATION##-#-##"; //this must be placed in DHCPD.CONF to app know where to put subnet declarations  
                 $netpos = strpos($configfile, $networkconfigarea);
                 if($netpos)
                 {
                    $configentry = "subnet ".$subnet." netmask ".$netmask." { \n}"; 
                    $newconfig = $this->stringInsert($configfile,"\n".$configentry,$netpos+strlen($networkconfigarea)); 
                    return $newconfig;
                 }
                 else
                 {
                     return 0; 
                 }
             }
             else
             {
                 return 0;
             }
        }
        
        
        //returning new configfile withoud subnet and all it's configuration
        public function deleteDhcpSubnetConfig($subnet, $netmask, $configfile)
        {
             $configsubnet = $this->findDhcpSubnetConfig($subnet, $netmask, $configfile);
             if($configsubnet)
             {
                 return str_replace($configsubnet,'', $configfile);
             }
             else
             {
                 return 0;
             }
        }
         
        
        public function addIpsToDhcpRangeConfig($subnet, $netmask, $configfile, $ips)
        {
            if(count($ips) > 0)
            {
		//check existing ranges
		$netpart = $newconfigsubnet = $this->findDhcpSubnetConfig($subnet, $netmask, $configfile);
		$ranges = $this->getDhcpRangesConfig($netpart);
		if($ranges)
		{
		    $allIps = [];
		    foreach($ranges as $range)
		    {
			//remove range from config
			$newconfigsubnet = str_replace($range, '', $newconfigsubnet);
			$rangePure = trim(str_replace('range ','',str_replace(';','',$range)));
			$allIps = array_merge($allIps, $this->rangeToIps($rangePure));
		    }
		    //config file without network ranges for this network

		    $newconfigfile = $this->removeMultipleNewlines(str_replace($netpart, $newconfigsubnet, $configfile));
		    $au = array_unique(array_merge($ips, $allIps));
		    sort($au);
		    $newconfigfile = $this->addIpsToDhcpRangeConfig($subnet, $netmask, $newconfigfile, $au);
		    return $newconfigfile;
		}
		else
		{
            	    $ranges = $this->ipsToRanges($ips);
                    $rangepos = strrpos($netpart, '{')+1;
            	    $rangestr = "\n";
            	    foreach($ranges as $range)
            	    {
                	$rangestr.= "range ".$range.";\n";
            	    }
            	    $newnetconfig = $this->stringInsert($netpart, $rangestr, $rangepos);
            	    return $this->removeMultipleNewlines(str_replace($netpart, $newnetconfig, $configfile));
        	}
	    }
            else
            {
                return 0;
            }
           
        }
        
        
        //returning new configfile withoud host
        public function deleteDhcpHostConfig($subnet, $netmask, $configfile, $hostname)
        {             
	     $confighost = $this->findDhcpHostConfig($subnet, $netmask, $configfile, $hostname);
	    if($confighost)
             {
                 return $this->removeMultipleNewlines(str_replace($confighost, '', $configfile));
                 
             }
             else
             {
                 return 0;
             }
        }
        
        public function findDhcpHostConfig($subnet, $netmask, $configfile, $hostname)
        {
            $networkconfig = $this->findDhcpSubnetConfig($subnet, $netmask, $configfile);
            if($networkconfig)
            {
                $startpos = strpos($networkconfig, "host ".$hostname);
                if($startpos)
                {
                               $hostblock = $this->returnDhcpConfigBlock($networkconfig, $startpos);
                               if($hostblock)
                               {
                                   return $hostblock;
                               }
                               else
                               {
                                    return 0; //error config not good
                               }
                }
                else
                {
                    return 0; //no such host	
                }  
            }
            else
            {
                //no host in such network;
                return 0;
            }

        }

	function getDhcpRangesConfig($networkconfig)
        {
            if($networkconfig)
            {
		$arrayrange = [];
                $startpos = strpos($networkconfig, "range ");
		preg_match_all('/range [\d\. ]*;/', $networkconfig, $lines, PREG_UNMATCHED_AS_NULL);
                foreach ($lines[0] as $line)
                {
		    $arrayrange[] = $line;
                }
		return $arrayrange;
            }
            else
            {
                //no config;
                return 0;
            }

        }


	public function deleteIpsFromDhcpRangeConfig($subnet, $netmask, $configfile, $ips)
        {             
	    if(count($ips) > 0)
	    {
	    $configsubnet = $newconfigsubnet = $this->findDhcpSubnetConfig($subnet, $netmask, $configfile);
	    if($configsubnet)
             {
		$ranges = $this->getDhcpRangesConfig($configsubnet);

		if($ranges)
		{
		    $allIps = [];
		    foreach($ranges as $range)
		    {
			//remove range from config
			$newconfigsubnet = str_replace($range, '', $newconfigsubnet);
			$rangePure = trim(str_replace('range ','',str_replace(';','',$range)));
			$allIps = array_merge($allIps, $this->rangeToIps($rangePure));
		    }
		    //config file without network ranges for this network
		    $newconfigfile = $this->removeMultipleNewlines(str_replace($configsubnet, $newconfigsubnet, $configfile));
		    if($diff = array_diff($allIps, $ips))
		    {
			$newconfigfile = $this->addIpsToDhcpRangeConfig($subnet, $netmask, $newconfigfile, $diff);
		    }
		    return $newconfigfile;
		}
             }
             else
             {
                 return 0;
             }
    	    }
	    else
	    {
		return 0;
	    }
	}
        
        function addDhcpGatewayConfig($subnet, $netmask, $configfile, $ip)
        {
            if($ip)
            {
                $netpart = $this->findDhcpSubnetConfig($subnet, $netmask, $configfile);
                if($netpart)
                {
                    $rangepos = strrpos($netpart, '{')+1;
                    $rangestr = "\n";
                    $rangestr .= "option routers ".$ip.";";
                    $newnetconfig = $this->stringInsert($netpart, $rangestr, $rangepos);
                    return $this->removeMultipleNewlines(str_replace($netpart, $newnetconfig, $configfile)); 
                }
                else
                {
                    return 0;
                }
            }
            else
            {
                return 0;
            }
        }
        
         function deleteDhcpGatewayConfig($subnet, $netmask, $configfile)
        {
                $netpart = $this->findDhcpSubnetConfig($subnet, $netmask, $configfile);
                if($netpart)
                {
                    $newnetconfig = preg_replace( '/option routers (.*[0-9\.]);(\s*)/', '' ,$netpart);
		    return $this->removeMultipleNewlines(str_replace($netpart, $newnetconfig, $configfile));
                }
                else
                {
                    return 0;
                }
        }
               
        function addDhcpHostConfig($subnet, $netmask, $configfile, $hostname, $ip = null, $mac = null)
        {

	    $netconfig = $this->findDhcpSubnetConfig($subnet, $netmask, $configfile);
            if($netconfig)
            {
                $hostconfig = $this->findDhcpHostConfig($subnet, $netmask, $configfile, $hostname);
		if(!$hostconfig)
		{
            	    $insertpos = strrpos($netconfig, '}');
            	    $hostconfig = "\nhost ".$hostname." { \n";
            	    if($mac)
            	    {
                	$hostconfig.= "hardware ethernet ".$this->prepareMac($mac)."; \n";  
            	    }  
            	    if($ip)
            	    {
                	$hostconfig.= "fixed-address ".$ip."; \n"; 
            	    }
            	    $hostconfig.= "}\n";
            	    $newsubnet = $this->stringInsert($netconfig, $hostconfig, $insertpos);
            	    return $this->removeMultipleNewlines(str_replace($netconfig, $newsubnet, $configfile));
		}
		else
		{
		    return 0;
		}
            }
            else
            {
                return 0;
            }
        }
        
        
        
        
        
        function rangeToIps(string $range)
        {
            $splited = explode(' ', trim($range));
            if(count($splited) === 2 )
            {
                $ips[] = $nextip = $splited[0];
                do
                {
                    $tmp = explode('.',$nextip);
                    if($tmp[3] === 255)
                    {
                        if($tmp[2] === 255)
                        {
                            if($tmp[1] === 255)
                            {
                                $tmp[0]++;
                                $tmp[1] = 0;
                                $tmp[2] = 0;
                                $tmp[3] = 0;
                                $ips[] = $nextip = implode('.', $tmp); 
                            }
                            else
                            {
                                $tmp[1]++;
                                $tmp[2] = 0;
                                $tmp[3] = 0;
                                $ips[] = $nextip = implode('.', $tmp);  
                            }
                        } 
                        else
                        {
                            $tmp[2]++;
                            $tmp[3] = 0;
                            $ips[] = $nextip = implode('.', $tmp);
                        }
                    }
                    else
                    {
                        $tmp[3]++;
                        $ips[] = $nextip = implode('.', $tmp);
                    }
                } while($nextip <> $splited[1]);
                return $ips;
            }
            elseif(count($splited) === 1)
            {
                return $splited;
            }
        }
        
        //default DHCP options
        function getOptionAuthoritative($configfile)
        {
            if($value =  preg_match('/^authoritative;/',$configfile))
            {
                return $value;
            }  
            else
            {
                return 0;   
            }  
        }
        
        function getOptionDefaultLeasetime($configfile)
        {
            if($value =  preg_match('/(default-lease-time )([0-9]+)(;)/',$configfile))
            {
                return $value;
            }  
            else
            {
                return 0;   
            }  
        }
        
        function getOptionMinimumtLeasetime($configfile)
        {
            if($value =  preg_match('/(min-lease-time )([0-9]+)(;)/',$configfile))
            {
                return $value;
            }  
            else
            {
                return 0;   
            }  
        }
        
        function getOptionaximumLeasetime($configfile)
        {
            if($value =  preg_match('/(max-lease-time )([0-9]+)(;)/',$configfile))
            {
                return $value;
            }  
            else
            {
                return 0;   
            }  
        }
        
        function getOptionDomain($configfile)
        {
            if($value = preg_match('/(option domain-name ")(.+)(";)/',$configfile))
            {
                return $value;
            }  
            else
            {
                return 0;   
            }  
        }
        
        function getOptionDNS($configfile)
        {
            if($value =  preg_match('/(option domain-name-servers )(.+)(;)/',$configfile))
            {
                return $value;
            }  
            else
            {
                return 0;   
            }  
        }
        
        //gets properties pushed into array from aditional dhcpd-general.conf file
        public function getDhcpGeneralProperties($configfile)
        {
            $params = [];
            if($authoritative = $this->getOptionAuthoritative($configfile))
            {
                $params[] = ['authoritative' => true];
            }
            if($tmp = $this->getOptionDefaultLeaseTime($configfile))
            {
                $params[] = ['default-lease-time' => $tmp[2]];
            }
            if($tmp = $this->getOptionMinimumLeaseTime($configfile))
            {
                $params[] = ['min-lease-time' => $tmp[2]];
            }
            if($tmp = $this->getOptionMaximumLeaseTime($configfile))
            {
                $params[] = ['max-lease-time' => $tmp[2]];
            }
            if($tmp = $this->getOptionDomain($configfile))
            {
                $params[] = ['domain-name' => $tmp[2]];
            }
            if($tmp = $this->getOptionDNS($configfile))
            {
                $params[] = ['domain-name-servers' => explode(',', str_replace(' ', '', $tmp[2]))];
            }
            return $params;
            
        }

        //converts array of ip address to array of ranges in dhcp notation
        function ipsToRanges($ips)
        {
            $rangescollection = [];
            $tmprange = [];
            foreach ($ips as $ip) 
	    {
                if(count($tmprange) == 0)
                {
                    $tmprange[] = $ip;
                }
                else
                {
                    $previous = explode('.', end($tmprange));
                    $current = explode('.',$ip);
                    if($previous[3] == 255 )
                    {
    
                         if($previous[2] == 255 )
                         { 
                            if($previous[1] == 255)
                            { 
                                if(($previous[0]+1 == $current[0]) && ($current[3] == 0 && $current[2] == 0 && $current[1] == 0))
                                {
                                        $tmprange[] = $ip;
                                }
                                else
                                {
                                        if(count($tmprange) > 1) 
                                        {
                                            $rangescollection[] = current(reset($tmprange)).' '.current(end($tmprange)); 
                                        }
                                        else
                                        {
                                             $rangescollection[] = current($tmprange);
                                        }
                                        $tmprange = [];
					$tmprange[] = $ip;
                                } 
                            }
                            else 
                            {
                                if(($previous[1]+1 == $current[1]) && ($current[3] == 0 && $current[2] == 0))
                                {
                                        $tmprange[] = $ip;
                                }
                                else
                                {
                                        if(count($tmprange) > 1) 
                                        {
                                            $rangescollection[] = reset($tmprange).' '.end($tmprange); 
                                        }
                                        else
                                        {
                                             $rangescollection[] = current($tmprange);
                                        }
                                        $tmprange = []; 
					$tmprange[] = $ip; 
                                } 
                            }
                         }
                         else
                         {
                             if(($previous[2]+1 == $current[2]) && ($current[3] == 0))
                             {
                                     $tmprange[] = $ip;
                             }
                             else
                             {
                                     if(count($tmprange) > 1) 
                                     {
                                         $rangescollection[] = reset($tmprange).' '.end($tmprange); 
                                     }
                                     else
                                     {
                                          $rangescollection[] = current($tmprange);
                                     }
                                     $tmprange = [];  
				     $tmprange[] = $ip;
                             } 
                         }
                    }
                    else
                    {
                        if($previous[3]+1 == $current[3])
                        {
                            $tmprange[] = $ip;
                        }
                        else
                        {
                            if(count($tmprange) > 1) 
                            {
                                $rangescollection[] = reset($tmprange).' '.end($tmprange); 

                            }
                            else
                            {
                                 $rangescollection[] = current($tmprange);
                            }
                    	    $tmprange = [];
			    $tmprange[] = $ip;
                        }  
                    }
                  
                    
                }
            }
	    if(count($tmprange) <> 0 )
	    {
		if(count($tmprange) > 1) 
                            {
                                $rangescollection[] = reset($tmprange).' '.end($tmprange); 

                            }
                            else
                            {
                                 $rangescollection[] = current($tmprange);
                            }
	    }
	    return $rangescollection;
        }
        
        function stringInsert($str, $insertstr, $pos)
        {       
                $str = substr($str, 0, $pos) . $insertstr . substr($str, $pos);
                return $str;
        }

	function removeMultipleNewlines($text)
        {       
                return preg_replace('/(\n)(\n+)/', '\1', $text);
        }

        
	public function getDhcpServiceStatus()
        {
            $status = shell_exec('/usr/bin/sudo /etc/init.d/isc-dhcp-server status');
        }
 
        
        public function startDhcpService()
        {
            $status = shell_exec('/usr/bin/sudo /etc/init.d/isc-dhcp-server start');
        }
        
        
        public function stopDhcpService()
        {
            $status = shell_exec('/usr/bin/sudo /etc/init.d/isc-dhcp-server stop');
        }
        
        
        
        public function prepareMac($value)
        {
              if($value <> null)
              {
                    $prepared = strtolower(str_replace("-","",str_replace(":", "", trim($value)))); 
                    if(len($prepared) == 12)
                    {
                       $chunks =  str_split($prepared, 2); 
                       return implode(":", $chunks);
                    }
                    else
                    {
                        return 0;   
                    }
                        
                   
               }
        }
}

