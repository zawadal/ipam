<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Validator\Constraints as AcmeAssert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Address
 *
 * @ORM\Table(name="address")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\AddressRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Address
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="ip", type="string", length=255)
     */
    private $ip;
    
     /**
     * @var boolean
     *
     * @ORM\Column(name="active", type="boolean")
     */
     private $active = true;
     
      /**
     * @var boolean
     *
     * @ORM\Column(name="gw", type="boolean")
     * 
     */
     private $gw = false;

     
     /**
     * @var boolean
     *
     * @ORM\Column(name="excluded", type="boolean")
     */
     private $excluded = false;
     
    /**
     * @var Network;
     *
     * @ORM\ManyToOne(targetEntity="Network", cascade={"persist"}, inversedBy="Address")
     * @ORM\JoinColumn(name="network", referencedColumnName="id", onDelete="CASCADE")
     */
    private $network;

    /**
     * @var Device
     *
     * @ORM\ManyToOne(targetEntity="Device", cascade={"persist"})
     */
    private $device;

    /**
     * @var string
     *
     * @ORM\Column(name="Description", type="string", length=255, nullable=true)
     */
    private $description;

    /**
     * @var int
     *
     * @ORM\Column(name="Type", type="integer", nullable=true)
     */
    private $type;

    /**
     * @var \DateTime
     * @ORM\Column(name="modifydate", type="datetime")
     */
    private $modifyDate;
    
    
    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set ip
     *
     * @param string $ip
     *
     * @return Address
     */
    public function setIp($ip)
    {
        $this->ip = $ip;

        return $this;
    }

    /**
     * Get ip
     *
     * @return string
     */
    public function getIp()
    {
        return $this->ip;
    }
    
     /**
     * Set excluded
     *
     * @param boolean $excluded
     *
     * @return Address
     */
    public function setExcluded($excluded)
    {
        $this->excluded = $excluded;

        return $this;
    }

    /**
     * Get excluded
     *
     * @return boolean
     */
    public function getExcluded()
    {
        return $this->excluded;
    }
    
     /**
     * Set active
     *
     * @param boolean $active
     *
     * @return Address
     */
    public function setActive($active)
    {
        $this->active = $active;

        return $this;
    }

    /**
     * Get active
     *
     * @return boolean
     */
    public function getActive()
    {
        return $this->active;
    }
    
     /**
     * Set gw
     *
     * @param boolean $gw
     *
     * @return Address
     */
    public function setGw($gw)
    {
        $this->gw = $gw;

        return $this;
    }

    /**
     * Get gw
     *
     * @return boolean
     */
    public function getGw()
    {
        return $this->gw;
    }
    
    
    /**
     * Set Network
     *
     * @param Network $network
     *
     * @return Address
     */
    public function setNetwork($network)
    {
        $this->network = $network;

        return $this;
    }

    /**
     * Get Network
     *
     * @return Network
     */
    public function getNetwork()
    {
        return $this->network;
    }

    /**
     * Set device
     *
     * @param Device $device
     *
     * @return Address
     */
    public function setDevice($device)
    {
        $this->device = $device;

        return $this;
    }

    /**
     * Get device
     *
     * @return Device
     */
    public function getDevice()
    {
        return $this->device;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Address
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set type
     *
     * @param integer $type
     *
     * @return Address
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return int
     */
    public function getType()
    {
        return $this->type;
    }
    
    /**
     * Set modifyDate
     *
     * @param \DateTime $modifyDate
     *
     * @return Address
     */
    public function setModifyDate($modifyDate)
    {
        $this->modifyDate = $modifyDate;

        return $this;
    }

    /**
     * Get modifyDate
     *
     * @return \DateTime
     */
    public function getModifyDate()
    {
        return $this->modifyDate;
    }
    
    
    
      /**
     * OnPrePersistEvent
     * @ORM\PrePersist
     */
    public function autoUpdaterCreate()
    {
        $this->modifyDate = new \DateTime();
    }
    
   
}

