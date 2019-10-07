<?php

namespace EntityBundle\Entity\User;

use Doctrine\ORM\Mapping as ORM;

/**
 * User
 * @ORM\Entity
 * @ORM\Table(name="User_User")
 */
class User
{   
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=20, unique=true)
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $password;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isActive=TRUE;

    /**
     * @ORM\Column(type="point", nullable=true)
     *
     * @var EntityBundle\Model\Object\Point
     */
    private $lastLocation;

    /**
     * @ORM\Column(type="string", length=10, nullable=true)
     */
    private $accuracy;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isAlert=FALSE;


    public function asArray($filtro=NULL){

        $response = [
            'id' => $this->id,
            'username' => $this->username,
            'isActive' => $this->isActive,
            //'lastLocation'=>$this->lastLocation,
            //'accuracy'=>$this->accuracy,
            //'isAlert'=>$this->isAlert,
        ];

        if($filtro){
            $response = array_intersect_key($response, array_flip($filtro));
        }

        return $response;
    }

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set username.
     *
     * @param string $username
     *
     * @return User
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Get username.
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set password.
     *
     * @param string $password
     *
     * @return User
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get password.
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set isActive.
     *
     * @param bool $isActive
     *
     * @return User
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;

        return $this;
    }

    /**
     * Get isActive.
     *
     * @return bool
     */
    public function getIsActive()
    {
        return $this->isActive;
    }

    /**
     * Set lastLocation.
     *
     * @param point|null $lastLocation
     *
     * @return User
     */
    public function setLastLocation($lastLocation = null)
    {
        $this->lastLocation = $lastLocation;

        return $this;
    }

    /**
     * Get lastLocation.
     *
     * @return point|null
     */
    public function getLastLocation()
    {
        return $this->lastLocation;
    }

    /**
     * Set accuracy.
     *
     * @param string|null $accuracy
     *
     * @return User
     */
    public function setAccuracy($accuracy = null)
    {
        $this->accuracy = $accuracy;

        return $this;
    }

    /**
     * Get accuracy.
     *
     * @return string|null
     */
    public function getAccuracy()
    {
        return $this->accuracy;
    }

    /**
     * Set isAlert.
     *
     * @param bool $isAlert
     *
     * @return User
     */
    public function setIsAlert($isAlert)
    {
        $this->isAlert = $isAlert;

        return $this;
    }

    /**
     * Get isAlert.
     *
     * @return bool
     */
    public function getIsAlert()
    {
        return $this->isAlert;
    }
}
