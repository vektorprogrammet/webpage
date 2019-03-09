<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="usergroup")
 */
class UserGroup
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(type="string", nullable = false)
     */
    private $name;


    /**
     * @var bool
     * @ORM\Column(type="boolean", nullable = false)
     */
    private $isInUse;


    /**
     * @var ArrayCollection
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\User")
     */
    private $users;



    /**
     * @var UserGroupCollection
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\UserGroupCollection", inversedBy="userGroups")
     * @ORM\JoinColumn
     */
    private $userGroupCollection;


    public function __construct()
    {
        $this->users = array();
        $this->name = "";
        $this->isInUse = false;
    }

    public function __toString()
    {
        return $this->name;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return User[]
     */
    public function getUsers()
    {
        return $this->users;
    }

    /**
     * @param User[] $users
     */
    public function setUsers(array $users): void
    {
        $this->users = $users;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name): void
    {
        $this->name = $name;
    }

    /**
     * @return bool
     */
    public function isInUse(): bool
    {
        return $this->isInUse;
    }

    /**
     * @param bool $isInUse
     */
    public function setIsInUse(bool $isInUse): void
    {
        $this->isInUse = $isInUse;
    }

    /**
     * @return UserGroupCollection
     */
    public function getUserGroupCollection(): UserGroupCollection
    {
        return $this->userGroupCollection;
    }

    /**
     * @param UserGroupCollection $userGroupCollection
     */
    public function setUserGroupCollection(UserGroupCollection $userGroupCollection): void
    {
        $this->userGroupCollection = $userGroupCollection;
    }
}
