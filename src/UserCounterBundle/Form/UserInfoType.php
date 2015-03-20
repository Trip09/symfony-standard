<?php

namespace UserCounterBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Test\FormBuilderInterface;
use \Symfony\Component\OptionsResolver\OptionsResolverInterface;

class UserInfoType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options) {

        $builder->add('email', 'email', array('label' => 'Email'))
                ->add('dob', 'date', array('label' => 'Birthdate', 'years' => range(date('Y') - 100, date('Y') - 18)))
                ->add('accountNumber', 'integer', array('label' => 'Account Number'))
                ->add('Submit', 'submit');
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(['data_class' => 'UserCounterBundle\Entity\User']);
    }

    public function getName() {
        return 'user_info_type';
    }

}
