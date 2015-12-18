<?php

namespace Maith\Common\AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * mBcuCotizacion
 *
 * @ORM\Table(name="maith_bcu_data")
 * @ORM\Entity
 */
class mBcuCotizacion
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
     * @ORM\Column(name="buy", type="string", length=255)
     */
    private $buy;

    /**
     * @var float
     *
     * @ORM\Column(name="sell", type="string", length=255)
     */
    private $sell;

    /**
     *
     * @ORM\ManyToOne(targetEntity="mBcuDataType", inversedBy="dataValues")
     * @ORM\JoinColumn(name="data_type_id", referencedColumnName="id", nullable=false)
     */    
    private $type;
    
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="valueDate", type="datetime")
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
     * Set buy
     *
     * @param float $buy
     * @return mBcuCotizacion
     */
    public function setBuy($buy)
    {
        $this->buy = $buy;

        return $this;
    }

    /**
     * Get buy
     *
     * @return float 
     */
    public function getBuy()
    {
        return $this->buy;
    }

    /**
     * Set sell
     *
     * @param float $sell
     * @return mBcuCotizacion
     */
    public function setSell($sell)
    {
        $this->sell = $sell;

        return $this;
    }

    /**
     * Get sell
     *
     * @return float 
     */
    public function getSell()
    {
        return $this->sell;
    }
    
    /**
     * Set element
     *
     * @param mBcuDataTypes $element
     *
     * @return mBcuDataTypes
     */
    public function setType(mBcuDataType $element)
    {
        $this->type = $element;

        return $this;
    }

    /**
     * Get element
     *
     * @return mBcuDataTypes
     */
    public function getType()
    {
        return $this->type;
    }
    
    /**
     * Set valueDate
     *
     * @param \DateTime $valueDate
     * @return mBcuCotizacion
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
