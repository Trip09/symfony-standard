<?php

namespace UserCounterBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class InfoUpdateController extends Controller {

    public function infoUpdateAction(Request $request, $userEmail) {

        $manager = $this->getDoctrine()->getManager();
        $PageId = $manager->createQuery('SELECT P.id FROM UserCounterBundle:Page P')->getSingleScalarResult();
        $user = $manager->getRepository('UserCounterBundle:User')->findOneByEmail($userEmail);
        $page = $manager->getRepository('UserCounterBundle:Page')->find($PageId);

        $form = $this->createFormBuilder($user)
                ->setAction($this->generateUrl('user_change_info', array('userEmail' => $userEmail)))
                ->add('email', 'email', array('label' => 'Email', 'read_only' => 'true'))
                ->add('dob', 'date', array('label' => 'Birthdate', 'years' => range(date('Y') - 100, date('Y') - 18)))
                ->add('accountNumber', 'integer', array('label' => 'Account Number'))
                ->add('Change info', 'submit')
                ->getForm();

        if ($request->isMethod('POST')) {

            $form->handleRequest($request);

            if ($form->isValid()) {
                
            }
            $manager->flush();

            return $this->redirect($this->generateUrl('user_info', array('request' => $request, 'userEmail' => $user->getEmail())));
        }
        return $this->render('UserCounterBundle:Default:infoChange.html.twig', array('form' => $form->createView(),
                    'totalVisits' => $page->getTotalVisits(),
                    'uniqueVisits' => $page->getUniqueVisits(),
                    'userVisits' => $user->getVisits()
        ));
    }

}
