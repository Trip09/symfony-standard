<?php

namespace UserCounterBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use UserCounterBundle\Entity\User;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    public function indexAction(Request $request)
    {
        
        $user = new User();
        $user->setEmail('');
        $user->setDob(new \DateTime('tomorrow'));
        $user->setAccountNumber(0);
        
        $form = $this->createFormBuilder($user)
                ->add('email','text',array('label' => false))
                ->add('dob','date',array('label'=> false))
                ->add('accountNumber','integer',array('label'=>false))
                ->getForm();
        
        if($request->isMethod('POST')){
            $form->handleRequest($request);
            $man = $this->getDoctrine()->getEntityManager();
            $man->persist($user);
            $man->flush();
        }
        
        
        return $this->render('UserCounterBundle:Default:index.html.twig',array('form' => $form->createView()));
    }
}
