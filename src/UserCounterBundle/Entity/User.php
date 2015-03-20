<?php

namespace UserCounterBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;

/**
 * User
 */
class User {

    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $email;

    /**
     * @var \DateTime
     */
    private $dob;

    /**
     * @var integer
     * @Assert\Length(
     *          min = 8,
     *          max = 34,
     *          minMessage = "Account Number should have at last {{ limit }} characters!",
     *          maxMessage = "Account Number should have {{ limit }} characters maximum!"
     * )
     */
    private $accountNumber;

    /**
     * @var integer
     */
    private $visits;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return User
     */
    public function setEmail($email) {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string 
     */
    public function getEmail() {
        return $this->email;
    }

    /**
     * Set dob
     *
     * @param \DateTime $dob
     * @return User
     */
    public function setDob($dob) {
        $this->dob = $dob;

        return $this;
    }

    /**
     * Get dob
     *
     * @return \DateTime 
     */
    public function getDob() {
        return $this->dob;
    }

    /**
     * Set accountNumber
     *
     * @param integer $accountNumber
     * @return User
     */
    public function setAccountNumber($accountNumber) {
        $this->accountNumber = $accountNumber;

        return $this;
    }

    /**
     * Get accountNumber
     *
     * @return integer 
     */
    public function getAccountNumber() {
        return $this->accountNumber;
    }

    /**
     * Set visits
     *
     * @param integer $visits
     * @return User
     */
    public function setVisits($visits) {
        $this->visits = $visits;

        return $this;
    }

    /**
     * Get visits
     *
     * @return integer 
     */
    public function getVisits() {
        return $this->visits;
    }

    public function getAge() {
        $actualDate = new \DateTime('today');
        $age = $actualDate->diff($this->dob);
        return $age->y;
    }

    /**
     * @var string
     */
    private $reference;

    /**
     * Set reference
     *
     * @param string $reference
     * @return User
     */
    public function setReference($reference) {
        $this->reference = $reference;

        return $this;
    }

    /**
     * Get reference
     *
     * @return string 
     */
    public function getReference() {
        return $this->reference;
    }

    public function generateReference() {
        $userAccount = "{$this->getAccountNumber()}";
        $userId = "{$this->getId()}";
        $this->setReference(str_pad(substr($userAccount, 0, 4), 4, '0', STR_PAD_LEFT) . '-' . str_pad($userId, 10, '0', STR_PAD_LEFT));
    }

    public function addVisit() {
        $this->setVisits($this->getVisits() + 1);
    }

}
