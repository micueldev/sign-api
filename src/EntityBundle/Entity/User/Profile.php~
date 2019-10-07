<?php

namespace EntityBundle\Entity\User;

use Doctrine\ORM\Mapping as ORM;

/**
 * Profile
 * @ORM\Entity
 * @ORM\Table(name="User_Profile")
 */
class Profile
{   
    /**
     * @ORM\OneToOne(targetEntity="EntityBundle\Entity\User\User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id", referencedColumnName="id", onDelete="CASCADE")
     * })
     * @ORM\Id
     */
    private $user;

    /**
     * @ORM\Column(type="string", length=120)
     */
    private $apepat;

    /**
     * @ORM\Column(type="string", length=120, nullable=true)
     */
    private $apemat;

    /**
     * @ORM\Column(type="string", length=120)
     */
    private $nombres;

    /**
     * @ORM\Column(type="string", length=100, unique=true, nullable=true)
     */
    private $email;


    public function asArray($filtro=NULL){

        $response = [
            'apepat' => $this->apepat,
            'apemat' => $this->apemat,
            'nombres' => $this->nombres,
            'email' => $this->email
        ];

        if($filtro){
            $response = array_intersect_key($response, array_flip($filtro));
        }

        return $response;
    }

    /**
     * Set apepat.
     *
     * @param string $apepat
     *
     * @return Profile
     */
    public function setApepat($apepat)
    {
        $this->apepat = $apepat;

        return $this;
    }

    /**
     * Get apepat.
     *
     * @return string
     */
    public function getApepat()
    {
        return $this->apepat;
    }

    /**
     * Set apemat.
     *
     * @param string|null $apemat
     *
     * @return Profile
     */
    public function setApemat($apemat = null)
    {
        $this->apemat = $apemat;

        return $this;
    }

    /**
     * Get apemat.
     *
     * @return string|null
     */
    public function getApemat()
    {
        return $this->apemat;
    }

    /**
     * Set nombres.
     *
     * @param string $nombres
     *
     * @return Profile
     */
    public function setNombres($nombres)
    {
        $this->nombres = $nombres;

        return $this;
    }

    /**
     * Get nombres.
     *
     * @return string
     */
    public function getNombres()
    {
        return $this->nombres;
    }

    /**
     * Set email.
     *
     * @param string|null $email
     *
     * @return Profile
     */
    public function setEmail($email = null)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email.
     *
     * @return string|null
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set user.
     *
     * @param \EntityBundle\Entity\User\User $user
     *
     * @return Profile
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
