<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Forms\SearchForm;

use AppBundle\Entity\Device;

class SearchController extends Controller
{
    /**
    * @Route("/search/", name="search")
    * @Template("IPAM/search.html.twig")
    */
    public function searchAction(Request $request)
    {
        
         $form = $this->createForm(SearchForm::class);
         $form->handleRequest($request);
         if($form->isSubmitted() && $form->isValid())
         {
             $data = $form->getData();
             if($data['searchclass'] && $data['searchphrase'] && $data['searchtype'])
             {
                 return $this->redirectToRoute(strtolower($data['searchclass']).'list', ['type' => $data['searchtype'], 'search' => $data['searchphrase']]);

             }
             else
             {
                    $this->addFlash(
                            'error',
                            'I cannot search - missing arguments'
                    );
             }
             
             
         }
         return [ 'form' => $form->CreateView() ];
    }
    
   
}