<?php

namespace EntityBundle\Entity\Config;

use Doctrine\ORM\Mapping as ORM;

/**
 * Sms
 * @ORM\Entity
 * @ORM\Table(name="Config_Sms")
 */
class Sms
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
    private $host;

    /**
     * @ORM\Column(type="string", length=5)
     */
    private $port;

    /**
     * @ORM\Column(type="string", length=11, nullable=true)
     */
    private $hash;


    public function asArray($filtro=NULL){

        $response = [
            //'id' => $this->id,
            //'cod' => $this->cod,
            'host' => $this->host,
            'port' => $this->port,
            'hash' => $this->hash
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
     * @return Sms
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
     * Set host.
     *
     * @param string $host
     *
     * @return Sms
     */
    public function setHost($host)
    {
        $this->host = $host;

        return $this;
    }

    /**
     * Get host.
     *
     * @return string
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * Set port.
     *
     * @param string $port
     *
     * @return Sms
     */
    public function setPort($port)
    {
        $this->port = $port;

        return $this;
    }

    /**
     * Get port.
     *
     * @return string
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * Set hash.
     *
     * @param string|null $hash
     *
     * @return Sms
     */
    public function setHash($hash = null)
    {
        $this->hash = $hash;

        return $this;
    }

    /**
     * Get hash.
     *
     * @return string|null
     */
    public function getHash()
    {
        return $this->hash;
    }
}
