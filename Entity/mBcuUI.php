<?php

namespace Maith\Common\AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * mBcuUI
 *
 * @ORM\Table(name="maith_bcu_ui")
 * @ORM\Entity
 */
class mBcuUI
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
     * @var float
     *
     * @ORM\Column(name="value", type="string", length=255)
     */
    private $value;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="valueDate", type="date")
     */
    private $valueDate;


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
     * Set value
     *
     * @param float $value
     * @return mBcuUI
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Get value
     *
     * @return float 
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set valueDate
     *
     * @param \DateTime $valueDate
     * @return mBcuUI
     */
    public function setValueDate($valueDate)
    {
        $this->valueDate = $valueDate;

        return $this;
    }

    /**
     * Get valueDate
     *
     * @return \DateTime 
     */
    public function getValueDate()
    {
        return $this->valueDate;
    }
}
