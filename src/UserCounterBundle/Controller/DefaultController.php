<?php

namespace UserCounterBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use UserCounterBundle\Entity\User;
use UserCounterBundle\Entity\Page;

class DefaultController extends Controller {

    public function indexAction(Request $request) {

        $manager = $this->getDoctrine()->getManager();
        $page = $manager->getRepository('UserCounterBundle:Page')->find(1);
        if ($page) {
            $page->setTotalVisits($page->getTotalVisits() + 1);
            $manager->persist($page);
            $manager->flush();
        } else {
            $page = new Page();
            $page->setTotalVisits(0);
            $manager->persist($page);
            $manager->flush();
        }
        $uniqueVisits = $manager->createQuery('SELECT COUNT(U.id) FROM UserCounterBundle:User U')
                ->getSingleScalarResult();

        $user = new User();
        $user->setEmail('');
        $user->setDob(new \DateTime('today'));
        $user->setAccountNumber(0);
        $user->setVisits(0);

        $form = $this->createFormBuilder($user)
                ->add('email', 'text', array('label' => false))
                ->add('dob', 'date', array('label' => false, 'years' => range(date('Y') - 100, date('Y'))))
                ->add('accountNumber', 'integer', array('label' => false))
                ->getForm();
        $alert = "";
        if ($request->isMethod('POST')) {
            $form->handleRequest($request);

            try {
                $man = $this->getDoctrine()->getEntityManager();
                $man->persist($user);
                $man->flush();
                $man->clear();
                return $this->redirect($this->generateUrl('user_info', array('userEmail' => $user->getEmail())));
            } catch (\Doctrine\DBAL\DBALException $dbalEx) {
                var_dump($dbalEx->getCode());
                var_dump($dbalEx->getMessage());
                exit;
//                $form->get('email')->addError(new \Symfony\Component\Form\FormError('erro!')); 
            } catch (\PDOException $pdoEx) {
                
            }
        }

        return $this->render('UserCounterBundle:Default:index.html.twig', array('form' => $form->createView(),
                    'totalVisits' => $page->getTotalVisits(),
                    'uniqueVisits' => $uniqueVisits,
                    'userVisits' => $user->getVisits(),
                    'alert' => $alert
        ));
    }

    public function infoAction($userEmail) {

        $manager = $this->getDoctrine()->getManager();

        $page = $manager->getRepository('UserCounterBundle:Page')->find(1);

        $totalVisits = $page->getTotalVisits();

        $uniqueVisits = $manager->createQuery('SELECT COUNT(U.id) FROM UserCounterBundle:User U')
                ->getSingleScalarResult();

        $user = new User();

        $user = $manager->getRepository('UserCounterBundle:User')->findOneByEmail($userEmail);

        $user->setVisits($user->getVisits() + 1);

        $manager->flush();

        $userAge = $user->getAge();

        $userDieRisk = round((log10($userAge) - 1) * 100);

        $userMortagageRisk = round((1 + 5 - 3 + 100 - 300 * log10((log10($userAge) - 1))) / 10);

        return $this->render('UserCounterBundle:Default:info.html.twig', array('userEmail' => $userEmail,
                    'userAge' => $userAge,
                    'userDieRisk' => $userDieRisk,
                    'userMortgageRisk' => $userMortagageRisk,
                    'totalVisits' => $totalVisits,
                    'uniqueVisits' => $uniqueVisits,
                    'userVisits' => $user->getVisits()
        ));
    }

}
