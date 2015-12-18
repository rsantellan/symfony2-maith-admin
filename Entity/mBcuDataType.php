<?php

namespace Maith\Common\AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * mBcuDataTypes
 *
 * @ORM\Table(name="maith_bcu_data_type")
 * @ORM\Entity
 */
class mBcuDataType
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
     * @var string
     *
     * @ORM\Column(name="country", type="string", length=255)
     */
    private $country;

    /**
     * @var integer
     *
     * @ORM\Column(name="currency", type="string", length=255)
     */
    private $currency;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="code", type="string", length=255)
     */
    private $code;

    /**
     * @var boolean
     *
     * @ORM\Column(name="visible", type="boolean")
     */
    private $visible;

    /**
     *
     * @ORM\OneToMany(targetEntity="mBcuCotizacion", mappedBy="type")
     */
    private $dataValues;

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
     * Set visible
     *
     * @param boolean $visible
     * @return mBcuDataTypes
     */
    public function setVisible($visible)
    {
        $this->visible = $visible;

        return $this;
    }

    /**
     * Get visible
     *
     * @return boolean 
     */
    public function getVisible()
    {
        return $this->visible;
    }
    
    function getCountry() {
      return $this->country;
    }

    function getCurrency() {
      return $this->currency;
    }

    function getCode() {
      return $this->code;
    }

    function setCountry($country) {
      $this->country = $country;
    }

    function setCurrency($currency) {
      $this->currency = $currency;
    }

    function setCode($code) {
      $this->code = $code;
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->dataValues = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add option
     *
     * @param \AppBundle\Entity\Displaygroupelementoption $option
     *
     * @return Displaygroupelement
     */
    public function addDataValues(\AppBundle\Entity\Displaygroupelementoption $option)
    {
        $this->dataValues[] = $option;

        return $this;
    }

    /**
     * Remove option
     *
     * @param \AppBundle\Entity\Displaygroupelementoption $option
     */
    public function removeDataValues(\AppBundle\Entity\Displaygroupelementoption $option)
    {
        $this->dataValues->removeElement($option);
    }

    /**
     * Get dataValues
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getDataValues()
    {
        return $this->dataValues;
    }
}
