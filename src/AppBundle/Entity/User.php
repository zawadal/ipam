<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\User as BaseUser;

/**
 * User
 *
 * @ORM\Table(name="user")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\UserRepository")
 */
class User extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    
     /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=255, nullable=true)
     */
    private $description;

     /**
     * @var Collection
     * @ORM\ManyToMany(targetEntity="Permision", inversedBy="users")
     */
    private $permissions;
    
    
    /**
     * @var string
     *
     * @ORM\Column(name="firstname", type="string", length=255, nullable=true)
     */
    private $firstname;
    
     /**
     * @var string
     *
     * @ORM\Column(name="lastname", type="string", length=255, nullable=true)
     */
    private $lastname;

    
    
    
    
    public function __construct()
    {
        parent::__construct();
        $this->permissions = new ArrayCollection();
    }
    

     /**
     * Set firstname
     *
     * @param string $firstname
     *
     * @return Owner
     */
    public function setFirstname($firstname)
    {
        $this->firstname = $firstname;

        return $this;
    }

    /**
     * Get firstname
     *
     * @return string
     */
    public function getFirstname()
    {
        return $this->firstname;
    }

    
    /**
     * Set lastname
     *
     * @param string $lastname
     *
     * @return Owner
     */
    public function setLastname($lastname)
    {
        $this->lastname = $lastname;

        return $this;
    }
    
    /**
     * Get lastname
     *
     * @return string
     */
    public function getLastname()
    {
        return $this->lastname;
    }
        /**
     * Set description
     *
     * @param string $description
     *
     * @return Owner
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
    * Get permissions
    *
    * @return Collection
    */
    public function getPermissions()
    {
        return $this->permissions;
    }
    
     /**
     *
     * @param Permission $permission
     * @return User
     */
    public function addPermissions(Permission $permission)
    {
        $this->permissions[] = $permission;
        return $this;
    }
    
    
    /**
     *
     * @param Permission $permission
     * @return User
     */
    public function removePermission(Address $address)
    {
        $this->permission->removeElement($address);
        return $this;
    }
    
    
    
}