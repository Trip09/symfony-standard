<?php

namespace UserCounterBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Page
 */
class Page
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var integer
     */
    private $totalVisits;


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set totalVisits
     *
     * @param integer $totalVisits
     * @return Page
     */
    public function setTotalVisits($totalVisits)
    {
        $this->totalVisits = $totalVisits;

        return $this;
    }

    /**
     * Get totalVisits
     *
     * @return integer 
     */
    public function getTotalVisits()
    {
        return $this->totalVisits;
    }
    /**
     * @var integer
     */
    private $uniqueVisits;


    /**
     * Set uniqueVisits
     *
     * @param integer $uniqueVisits
     * @return Page
     */
    public function setUniqueVisits($uniqueVisits)
    {
        $this->uniqueVisits = $uniqueVisits;

        return $this;
    }

    /**
     * Get uniqueVisits
     *
     * @return integer 
     */
    public function getUniqueVisits()
    {
        return $this->uniqueVisits;
    }
}
