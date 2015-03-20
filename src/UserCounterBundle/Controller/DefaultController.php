<?php

namespace UserCounterBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use UserCounterBundle\Entity\User;
use UserCounterBundle\Entity\Page;

class DefaultController extends Controller {

    public function indexAction(Request $request) {
        $page = $this->pageCheck();
        $user = new User();
        $user->setVisits(0);
        $user->setReference(0); // alterar para nullable no .yml

        $form = $this->createFormBuilder($user)
                ->setAction($this->generateUrl('user_counter_homepage'))
                ->add('email', 'email', array('label' => 'Email'))
                ->add('dob', 'date', array('label' => 'Birthdate', 'years' => range(date('Y') - 100, date('Y') - 18)))
                ->add('accountNumber', 'integer', array('label' => 'Account Number'))
                ->add('Submit', 'submit')
                ->getForm();

        $alert = '';

        if ($request->isMethod('POST')) {

            $form->handleRequest($request);

            if ($form->isValid()) {

                try {

                    $manager = $this->getDoctrine()->getManager();
                    $manager->persist($user);
                    $manager->flush();

                    $user = $manager->getRepository('UserCounterBundle:User')->findOneByEmail($user->getEmail());
                    $user->generateReference();
                    $page->setUniqueVisits($page->getUniqueVisits() + 1);
                    $manager->persist($page);
                    $manager->persist($user);
                    $manager->flush();

                    return $this->redirect($this->generateUrl('user_info', array('userEmail' => $user->getEmail(), 'request' => $request)));
                } catch (\Exception $e) {
                    $this->addFlash('warning', 'erro: ' . $e->getMessage());
                }
            }
        }

        return $this->render('UserCounterBundle:Default:index.html.twig', array('form' => $form->createView(),
                    'totalVisits' => $page->getTotalVisits(),
                    'uniqueVisits' => $page->getUniqueVisits(),
                    'userVisits' => $user->getVisits(),
                    'alert' => $alert
        ));
    }

    public function infoAction(Request $request, $userEmail) {

        $manager = $this->getDoctrine()->getManager();

        $user = $manager->getRepository('UserCounterBundle:User')->findOneByEmail($userEmail);

        if (!$user instanceof User) {
            throw $this->createNotFoundException('User does not exist!');
        }

        $PageId = $manager->createQuery('SELECT P.id FROM UserCounterBundle:Page P')->getSingleScalarResult();  // gets the Page from DB
        $page = $manager->getRepository('UserCounterBundle:Page')->find($PageId);

        $user->addVisit();
        $page->addTotalVisit();

        $manager->persist($user);
        $manager->persist($page);
        $manager->flush();

        $userAge = $user->getAge();
        $userDieRisk = round((log10($userAge) - 1) * 100);
        $userMortagageRisk = round((1 + 5 - 3 + 100 - 300 * log10((log10($userAge) - 1))) / 10);


        if ($request->isMethod('POST')) {
            return $this->redirect($this->generateUrl('user_change_info', array('userEmail' => $user->getEmail(), 'request' => $request)));
        }


        return $this->render('UserCounterBundle:Default:info.html.twig', array('userEmail' => $user->getEmail(),
                    'userAge' => $userAge,
                    'userDieRisk' => $userDieRisk,
                    'userMortgageRisk' => $userMortagageRisk,
                    'totalVisits' => $page->getTotalVisits(),
                    'uniqueVisits' => $page->getUniqueVisits(),
                    'userVisits' => $user->getVisits()
        ));
    }

    /*
     * Checks if the DB has a Page and if not creates one
     * 
     * @returns a Page object
     */

    public function pageCheck() {
        $manager = $this->getDoctrine()->getManager();
        try {
            $id = $manager->createQuery('SELECT P.id FROM UserCounterBundle:Page P')->getSingleScalarResult();
        } catch (\Doctrine\ORM\NoResultException $ex) {
            $id = -1;
        }

        if ($id >= 0) {

            $page = $manager->getRepository('UserCounterBundle:Page')->find($id);
            $page->addTotalVisit();
            $page->setUniqueVisits($manager->createQuery('SELECT COUNT(U.id) FROM UserCounterBundle:User U')
                            ->getSingleScalarResult());
            $manager->persist($page);
            $manager->flush();
        } else {

            $page = new Page();
            $page->setUniqueVisits($manager->createQuery('SELECT COUNT(U.id) FROM UserCounterBundle:User U')
                            ->getSingleScalarResult());

            if ($page->getUniqueVisits() == 0) {

                $page->setTotalVisits(1);
            } else {

                $page->setTotalVisits($manager->createQuery('SELECT SUM(U.visits) FROM UserCounterBundle:User U')
                                ->getSingleScalarResult());
            }
            $manager->persist($page);
            $manager->flush();
        }

        return $page;
    }

}
