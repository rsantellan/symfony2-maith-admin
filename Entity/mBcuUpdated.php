<?php

namespace Maith\Common\AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * mBcuUpdated
 *
 * @ORM\Table(name="maith_bcu_updated")
 * @ORM\Entity
 */
class mBcuUpdated
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="lastupdated", type="date")
     */
    private $lastupdated;


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
     * Set lastupdated
     *
     * @param \DateTime $lastupdated
     * @return mBcuUpdated
     */
    public function setLastupdated($lastupdated)
    {
        $this->lastupdated = $lastupdated;

        return $this;
    }

    /**
     * Get lastupdated
     *
     * @return \DateTime 
     */
    public function getLastupdated()
    {
        return $this->lastupdated;
    }
}
