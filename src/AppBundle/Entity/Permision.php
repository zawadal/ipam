<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Permision
 *
 * @ORM\Table(name="permision")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PermisionRepository")
 */
class Permision
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
     * @ORM\Column(name="perm", type="string", length=255, unique=true)
     */
    private $perm;

    /**
     * @var Collection
     * @ORM\ManyToMany(targetEntity="User", inversedBy="permissions")
     */
    private $users;
    
    /**
     * @var Collection
     * @ORM\ManyToMany(targetEntity="Customer", inversedBy="permissions")
     */
    private $customers;

    public function __construct()
    {
        $this->users = new ArrayCollection();
        $this->customers = new ArrayCollection();
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
     * Set perm
     *
     * @param string $perm
     *
     * @return Permision
     */
    public function setPerm($perm)
    {
        $this->perm = $perm;

        return $this;
    }

    /**
     * Get perm
     *
     * @return string
     */
    public function getPerm()
    {
        return $this->perm;
    }
    
    /**
    * Get users
    *
    * @return Collection
    */
    public function getUsers()
    {
        return $this->users;
    }
    
    /**
    *
    * @param User $user
    * @return Permission
    */
    public function addUsers(User $user)
    {
        $this->users[] = $user;
        return $this;
    }
    
    /**
    * Get customers
    *
    * @return Collection
    */
    public function getCustomers()
    {
        return $this->customers;
    } 

    /**
    *
    * @param Customer $customer
    * @return Permission
    */
    public function addCustomers(Customer $customer)
    {
        $this->customers[] = $customer;
        return $this;
    }
}

