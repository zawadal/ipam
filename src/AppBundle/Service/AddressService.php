<?php

namespace AppBundle\Service;

use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\Entity\EntityManagerInterface;

class AddressService
{
        public function prepareMac($value)
        {
              if($value <> null)
              {
                    $prepared = strtolower(str_replace("-","",str_replace(":", "", trim($value)))); 
                    return $prepared;
               }
        }
       
}