<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use AppBundle\Entity\Address;
use AppBundle\Entity\Customer;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Network
 *
 * @ORM\Table(name="network")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\NetworkRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Network
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
     * @ORM\Column(name="net", type="string", length=255)
     * @Assert\Ip()
     */
    private $net;
    
     /**
     * @var string
     *
     * @ORM\Column(name="firstAddress", type="string", length=255)
     */
    private $firstAddress;
    
     /**
     * @var string
     *
     * @ORM\Column(name="lastAddress", type="string", length=255)
     */
    private $lastAddress;

     /**
     * @var integer
     *
     * @ORM\Column(name="maxhosts", type="integer")
     */
    private $maxHosts;

     /**
     * @var integer
     *
     * @ORM\Column(name="vlanid", type="integer", nullable=true)
     * @Assert\Type("numeric")
     */
    private $vlanId;
    
    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=255, nullable=true)
     */
    private $description;

    /**
     * @var int
     *
     * @ORM\Column(name="netmask", type="integer")
     * @Assert\Type("numeric")
     * @Assert\LessThan(value = 32)
     * @Assert\GreaterThan(value = 1)
     */
    private $netmask;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="adddate", type="datetime")
     */
    private $addDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="modifydate", type="datetime")
     */
    private $modifyDate;

    /**
     * @var int
     *
     * @ORM\Column(name="type", type="integer", nullable=true)
     */
    private $type;
    
    /**
     * @var float
     *
     * @ORM\Column(name="IPAMutilization", type="float")
     */
    private $IPAMutilization = 0;


     /**
     * @var float
     *
     * @ORM\Column(name="DHCPReservedUtilization", type="float")
     */
    private $DHCPReservedUtilization = 0;

      /**
     * @var float
     *
     * @ORM\Column(name="DHCPDynamicUtilization", type="float")
     */
     private $DHCPDynamicUtilization = 0;

     /**
     * @var address[]
     * @ORM\OneToMany(targetEntity="Address", mappedBy="network", cascade={"persist"})
     */
     private $address;
     
     /**
     * @var customer;
     *
     * @ORM\ManyToOne(targetEntity="Customer")
     *
     */
     private $customer;
     
     
     public function __construct() {
         $this->address = new ArrayCollection();
     }
     
     
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
     * Set net
     *
     * @param string $net
     *
     * @return Network
     */
    public function setNet($net)
    {
        $this->net = $net;

        return $this;
    }

    /**
     * Get net
     *
     * @return string
     */
    public function getNet()
    {
        return $this->net;
    }

     /**
     * Set firstAddress
     *
     * @param string $firstAddress
     *
     * @return Network
     */
    public function setFirstAddress($firstAddress)
    {
        $this->firstAddress = $firstAddress;

        return $this;
    }

    /**
     * Get firstAddress
     *
     * @return string
     */
    public function getFirstAddress()
    {
        return $this->firstAddress;
    }

    /**
     * Set lastAddress
     *
     * @param string $lastAddress
     *
     * @return Network
     */
    public function setLastAddress($lastAddress)
    {
        $this->lastAddress = $lastAddress;

        return $this;
    }

    /**
     * Get lastAddress
     *
     * @return string
     */
    public function getLastAddress()
    {
        return $this->lastAddress;
    }
    
    
    
    /**
     * Set description
     *
     * @param string $description
     *
     * @return Network
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
     * Set netmask
     *
     * @param integer $netmask
     *
     * @return Network
     */
    public function setNetmask($netmask)
    {
        $this->netmask = $netmask;

        return $this;
    }

    /**
     * Get netmask
     *
     * @return int
     */
    public function getNetmask()
    {
        return $this->netmask;
    }

    /**
     * Set addDate
     *
     * @param \DateTime $addDate
     *
     * @return Network
     */
    public function setAddDate($addDate)
    {
        $this->addDate = $addDate;

        return $this;
    }

    /**
     * Get addDate
     *
     * @return \DateTime
     */
    public function getAddDate()
    {
        return $this->addDate;
    }

    /**
     * Set modifyDate
     *
     * @param \DateTime $modifyDate
     *
     * @return Network
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
     * Set type
     *
     * @param integer $type
     *
     * @return Network
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
     * Set maxHosts
     *
     * @param integer $maxHosts
     *
     * @return Network
     */
    public function setMaxHosts($maxHosts)
    {
        $this->maxHosts = $maxHosts;

        return $this;
    }

    /**
     * Get maxHosts
     *
     * @return int
     */
    public function getMaxHosts()
    {
        return $this->maxHosts;
    }
    
    
    /**
     * Set vlanId
     *
     * @param integer $vlanId
     *
     * @return Network
     */
    public function setVlanId($vlanId)
    {
        $this->vlanId = $vlanId;

        return $this;
    }

    /**
     * Get vlanid
     *
     * @return int
     */
    public function getVlanId()
    {
        return $this->vlanId;
    }
    
    
    
     /**
     * Set IPAMUtilization
     *
     * @param float $IPAMutilization
     *
     * @return Network
     */
    public function setIPAMutilization($IPAMutilization)
    {
        $this->IPAMutilization = $IPAMutilization;

        return $this;
    }

    /**
     * Get IPAMUtilization
     *
     * @return float
     */
    public function getIPAMutilization()
    {
        return $this->IPAMutilization;
    }
    
     /**
     * Set DHCPReservedUtilization
     *
     * @param float $DHCPReservedUtilization
     *
     * @return Network
     */
    public function setDHCPReservedUtilization($DHCPReservedUtilization)
    {
        $this->DHCPReservedUtilization = $DHCPReservedUtilization;

        return $this;
    }

    /**
     * Get DHCPReservedUtilization
     *
     * @return float
     */
    public function getDHCPReservedUtilization()
    {
        return $this->DHCPReservedUtilization;
    }
  
    
         /**
     * Set DHCPDynamicUtilization
     *
     * @param float $DHCPDynamicUtilization
     *
     * @return Network
     */
    public function setDHCPDynamicUtilization($DHCPDynamicUtilization)
    {
        $this->DHCPDynamicUtilization = $DHCPDynamicUtilization;

        return $this;
    }

    /**
     * Get DHCPDynamicUtilization
     *
     * @return float
     */
    public function getDHCPDynamicUtilization()
    {
        return $this->DHCPDynamicUtilization;
    }
    
    
    /**
     * Set customer
     *
     * @param Customer $customer
     *
     * @return Network
     */
    public function setCustomer(Customer $customer)
    {
        $this->customer = $customer;

        return $this;
    }

    /**
     * Get customer
     *
     * @return Customer
     */
    public function getCustomer()
    {
        return $this->customer;
    }
    
     /**
     *
     * @param Address $address
     * @return Network
     */
    public function addAddress(Address $address)
    {
        $address->setNetwork($this);
        $this->address[] = $address;
        return $this;
    }

    /**
     *
     * @param Address $address
     * @return Network
     */
    public function removeAddress(Address $address)
    {
        $this->address->removeElement($address);
        return $this;
    }

    /**
     *
     * @return type
     */
    public function getAddress()
    {
        return $this->address;
    }

    
    /**
     * OnPrePersistEvent
     * @ORM\PrePersist
     */
    public function autoCreate()
    {
        $this->type = 1;
        $this->addDate    = new \DateTime();
        $this->modifyDate = new \DateTime();
    }
    
     /**
     * OnPreUpdateEvent
     * @ORM\PreUpdate
     */
    public function updateModifyDate()
    {
        $this->modifyDate = new \DateTime();
    }
}

