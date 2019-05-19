<?php

namespace EntityBundle\Entity\Config;

use Doctrine\ORM\Mapping as ORM;

/**
 * Platform
 * @ORM\Entity
 * @ORM\Table(name="Config_Platform")
 */
class Platform
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
     * @ORM\Column(type="string", length=20)
     */
    private $domain;

    /**
     * @ORM\Column(type="string", length=20)
     */
    private $subdomain;


    public function asArray($filtro=NULL){

        $response = [
            //'id' => $this->id,
            //'cod' => $this->cod,
            'domain' => $this->domain,
            'subdomain' => $this->subdomain,
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
     * @return Platform
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
     * @return Platform
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

    /**
     * Set subdomain.
     *
     * @param string $subdomain
     *
     * @return Platform
     */
    public function setSubdomain($subdomain)
    {
        $this->subdomain = $subdomain;

        return $this;
    }

    /**
     * Get subdomain.
     *
     * @return string
     */
    public function getSubdomain()
    {
        return $this->subdomain;
    }
}
