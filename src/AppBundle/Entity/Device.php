<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Validator\Constraints as AcmeAssert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Device
 *
 * @ORM\Table(name="device")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\DeviceRepository")
 * @ORM\HasLifecycleCallbacks()
 * @UniqueEntity(fields = {"mac"},
 *               message = "MAC address already in use!")
 */
class Device
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="hostname", type="string", length=255)
     */
    private $hostname;

     /**
     * @var string
     *
     * @ORM\Column(name="serial", type="string", length=255, nullable=true)
     */
    private $serial;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="warranty", type="integer")
     */
    private $warranty;
    
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="warrantydate", type="datetime")
     */
    private $warrantydate;
    
    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=500, nullable=true)
     */
    
    private $description;

    /**
     * @var Type
     *
     * @ORM\ManyToOne(targetEntity="Type", cascade={"persist"} )`
     */
    private $type;

    /**
     * @var Owner
     *
     * @ORM\ManyToOne(targetEntity="Owner", cascade={"persist"})`
     */
    private $owner;

    /**
     * @var string
     *
     * @ORM\Column(name="mac", type="string", length=20, unique=true, nullable=true)
     * @AcmeAssert\IsMac() 
     * 
     */
    private $mac;


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
     * Set hostname
     *
     * @param string $hostname
     *
     * @return Device
     */
    public function setHostname($hostname)
    {
        $this->hostname = $hostname;

        return $this;
    }

    /**
     * Get hostname
     *
     * @return string
     */
    public function getHostname()
    {
        return $this->hostname;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Device
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
     * @param Type $type
     *
     * @return Device
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return Type
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set owner
     *
     * @param string $owner
     *
     * @return Device
     */
    public function setOwner($owner)
    {
        $this->owner = $owner;

        return $this;
    }

    /**
     * Get owner
     *
     * @return string
     */
    public function getOwner()
    {
        return $this->owner;
    }

    /**
     * Set mac
     *
     * @param string $mac
     *
     * @return Device
     */
    public function setMac($mac)
    {
        $this->mac = $mac;

        return $this;
    }

    /**
     * Get mac
     *
     * @return string
     */
    public function getMac()
    {
        return $this->mac;
    }
    
     /* Set serial
     *
     * @param string $serial
     *
     * @return Device
     */
    public function setSerial($serial)
    {
        $this->serial = $serial;

        return $this;
    }

    /**
     * Get serial
     *
     * @return string
     */
    public function getSerial()
    {
        return $this->serial;
    }
    
    /* Set warranty
     *
     * @param integer $warranty
     *
     * @return Device
     */
    public function setWarranty($warranty)
    {
        $this->warranty = $warranty;

        return $this;
    }

    /**
     * Get warranty
     *
     * @return integer
     */
    public function getWarranty()
    {
        return $this->warranty;
    }
    
   /* Set warrantydate
     *
     * @param \DateTime $warrantydate
     *
     * @return Device
     */
    public function setWarrantydate($warrantydate)
    {
        $this->warrantydate = $warrantydate;

        return $this;
    }

    /**
     * Get warrantydate
     *
     * @return \DateTime
     */
    public function getWarrantydate()
    {
        return $this->warrantydate;
    } 
    
     /**
     * OnPrePersistEvent
     * @ORM\PrePersist
     */
    public function autoCreate()
    {
        $this->warrantydate = new \DateTime();
    }
    
}

