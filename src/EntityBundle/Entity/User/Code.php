<?php

namespace EntityBundle\Entity\User;

use Doctrine\ORM\Mapping as ORM;

/**
 * Code
 * @ORM\Entity
 * @ORM\Table(name="User_Code")
 */
class Code
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
     * @ORM\Column(type="string", length=10)
     */
    private $code;

    /**
    * @ORM\Column(type="datetime")
     */
    private $f;

    public function asArray($filtro=NULL){

        $response = [
            'id' => $this->id,
            'username' => $this->username,
            'code' => $this->code,
            'f' => $this->f
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
     * Set username
     *
     * @param string $username
     *
     * @return Code
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Get username
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set code
     *
     * @param string $code
     *
     * @return Code
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get code
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set f
     *
     * @param \DateTime $f
     *
     * @return Code
     */
    public function setF($f)
    {
        $this->f = $f;

        return $this;
    }

    /**
     * Get f
     *
     * @return \DateTime
     */
    public function getF()
    {
        return $this->f;
    }
}
