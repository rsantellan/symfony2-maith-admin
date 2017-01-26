<?php

namespace Maith\Common\AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * mBcuDataTypes
 *
 * @ORM\Table(name="maith_bcudatatype")
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
     * @ORM\Column(name="name", type="string", length=255, unique=true)
     */
    private $name;

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
    
    function getName() {
      return $this->name;
    }

    function setName($name) {
      $this->name = $name;
      return $this;
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
     * @param mBcuCotizacion $option
     *
     * @return Displaygroupelement
     */
    public function addDataValues(mBcuCotizacion $option)
    {
        $this->dataValues[] = $option;

        return $this;
    }

    /**
     * Remove option
     *
     * @param mBcuCotizacion $option
     */
    public function removeDataValues(mBcuCotizacion $option)
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
