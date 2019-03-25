<?php

namespace EntityBundle\Entity\User;

use Doctrine\ORM\Mapping as ORM;

/**
 * Contact
 * @ORM\Entity
 * @ORM\Table(name="User_Contact")
 */
class Contact
{   
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity="EntityBundle\Entity\User\User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user", referencedColumnName="id",unique=true, nullable=false)
     * })
     */
    private $user;

    /**
    * @ORM\Column(type="json_array")
    */
    private $aContact=[];  //array de modulos


    public function asArray($filtro=NULL){

        $response = [
            'id' => $this->id,
            'username' => $this->username->getId(),
            'aContact' => $this->aContact
        ];

        if($filtro){
            $response = array_intersect_key($response, array_flip($filtro));
        }

        return $response;
    }

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
     * Set aContact
     *
     * @param array $aContact
     *
     * @return Contact
     */
    public function setAContact($aContact)
    {
        $this->aContact = $aContact;

        return $this;
    }

    /**
     * Get aContact
     *
     * @return array
     */
    public function getAContact()
    {
        return $this->aContact;
    }

    /**
     * Set user
     *
     * @param \EntityBundle\Entity\User\User $user
     *
     * @return Contact
     */
    public function setUser(\EntityBundle\Entity\User\User $user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \EntityBundle\Entity\User\User
     */
    public function getUser()
    {
        return $this->user;
    }
}
