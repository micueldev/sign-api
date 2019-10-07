<?php

namespace EntityBundle\Entity\Item;

use Doctrine\ORM\Mapping as ORM;

/**
 * Alert
 * @ORM\Entity
 * @ORM\Table(name="Item_Alert")
 */
class Alert
{   
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="EntityBundle\Entity\User\User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user", referencedColumnName="id", nullable=false)
     * })
     */
    private $user;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isActive=TRUE;

    /**
    * @ORM\Column(type="datetime", nullable=true)
     */
    private $f;

    /**
    * @ORM\Column(type="datetime", nullable=true)
     */
    private $fT;

    /**
    * @ORM\Column(type="json_array")
    */
    private $aUserAlert=[];

    /**
    * @ORM\Column(type="json_array")
    */
    private $aLocation=[];


    public function asArray($filtro=NULL){

        $response = [
            'id' => $this->id,
            'user' => $this->user->getUsername(),
            'isActive' => $this->isActive,
            'f' => $this->f,
            'fT' => $this->fT,
            'aUserAlert' => $this->aUserAlert,
            'aLocation' => $this->aLocation
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
     * Set isActive.
     *
     * @param bool $isActive
     *
     * @return Alert
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
     * Set f.
     *
     * @param \DateTime|null $f
     *
     * @return Alert
     */
    public function setF($f = null)
    {
        $this->f = $f;

        return $this;
    }

    /**
     * Get f.
     *
     * @return \DateTime|null
     */
    public function getF()
    {
        return $this->f;
    }

    /**
     * Set fT.
     *
     * @param \DateTime|null $fT
     *
     * @return Alert
     */
    public function setFT($fT = null)
    {
        $this->fT = $fT;

        return $this;
    }

    /**
     * Get fT.
     *
     * @return \DateTime|null
     */
    public function getFT()
    {
        return $this->fT;
    }

    /**
     * Set aUserAlert.
     *
     * @param array $aUserAlert
     *
     * @return Alert
     */
    public function setAUserAlert($aUserAlert)
    {
        $this->aUserAlert = $aUserAlert;

        return $this;
    }

    /**
     * Get aUserAlert.
     *
     * @return array
     */
    public function getAUserAlert()
    {
        return $this->aUserAlert;
    }

    /**
     * Set aLocation.
     *
     * @param array $aLocation
     *
     * @return Alert
     */
    public function setALocation($aLocation)
    {
        $this->aLocation = $aLocation;

        return $this;
    }

    /**
     * Get aLocation.
     *
     * @return array
     */
    public function getALocation()
    {
        return $this->aLocation;
    }

    /**
     * Set user.
     *
     * @param \EntityBundle\Entity\User\User $user
     *
     * @return Alert
     */
    public function setUser(\EntityBundle\Entity\User\User $user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user.
     *
     * @return \EntityBundle\Entity\User\User
     */
    public function getUser()
    {
        return $this->user;
    }
}
