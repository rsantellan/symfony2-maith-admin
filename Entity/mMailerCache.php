<?php

namespace Maith\Common\AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * mMailerCache
 *
 * @ORM\Table(name="maith_mailer_cache")
 * @ORM\Entity
 * @author Rodrigo Santellan <rsantellan@gmail.com>
 */
class mMailerCache
{

    /**
     * @var String
     * @ORM\Id
     * @ORM\Column(name="datestring", type="string", length=15)
     */
    private $datestring;

    /**
     * @var String
     *
     * @ORM\Id
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var Integer
     *
     * @ORM\Column(name="quantity", type="integer")
     */
    private $quantity;


    /**
     * Set datestring
     *
     * @param String $datestring
     * @return mMailerCache
     */
    public function setDatestring($datestring)
    {
        $this->datestring = $datestring;

        return $this;
    }

    /**
     * Get datestring
     *
     * @return String 
     */
    public function getDatestring()
    {
        return $this->datestring;
    }

    /**
     * Set name
     *
     * @param String $name
     * @return mMailerCache
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return String 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set quantity
     *
     * @param \DateTime $quantity
     * @return mMailerCache
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;

        return $this;
    }

    /**
     * Get quantity
     *
     * @return \DateTime 
     */
    public function getQuantity()
    {
        return $this->quantity;
    }
}
