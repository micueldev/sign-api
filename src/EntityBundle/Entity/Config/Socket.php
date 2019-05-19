<?php

namespace EntityBundle\Entity\Config;

use Doctrine\ORM\Mapping as ORM;

/**
 * Socket
 * @ORM\Entity
 * @ORM\Table(name="Config_Socket")
 */
class Socket
{
	/**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=20)
     */
    private $cod;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $domain;

    public function asArray($filtro=NULL){

        $response = [
            //'id' => $this->id,
            //'cod' => $this->cod,
            'domain' => $this->domain,
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
     * Set cod.
     *
     * @param string $cod
     *
     * @return Socket
     */
    public function setCod($cod)
    {
        $this->cod = $cod;

        return $this;
    }

    /**
     * Get cod.
     *
     * @return string
     */
    public function getCod()
    {
        return $this->cod;
    }

    /**
     * Set domain.
     *
     * @param string $domain
     *
     * @return Socket
     */
    public function setDomain($domain)
    {
        $this->domain = $domain;

        return $this;
    }

    /**
     * Get domain.
     *
     * @return string
     */
    public function getDomain()
    {
        return $this->domain;
    }
}
