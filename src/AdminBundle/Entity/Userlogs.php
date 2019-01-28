<?php

namespace AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * User
 *
 * @ORM\Table(name="Userlogs", uniqueConstraints={@ORM\UniqueConstraint(name="id", columns={"id"})})
 * @ORM\Entity
 */

class Userlogs
{
    /**
     * @var string
     *
     * @ORM\Column(name="log", type="text", length=1000, nullable=false)
     */
    private $log;

    /**
     * @var integer
     *
     * @ORM\Column(name="newlog", type="integer", length=1, nullable=false)
     */
    private $newlog;

    /**
     * @var string
     *
     * @ORM\Column(name="fecha", type="text", length=10, nullable=false)
     */
    private $fecha;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \AdminBundle\Entity\User
     *
     * @ORM\ManyToOne(targetEntity="AdminBundle\Entity\User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="Userid", referencedColumnName="id", onDelete="CASCADE")
     * })
     */
    private $userid;

    /**
     * User constructor.
     */
    public function __construct()
    {
    }

    /**
     * Set Log
     *
     * @param string $log
     * @return Userlogs
     */
    public function setLog($log)
    {
        $this->log = $log;

        return $this;
    }

    /**
     * Get Log
     *
     * @return string
     */
    public function getLog()
    {
        return $this->log;
    }

    /**
     * Set Log
     *
     * @param integer $newlog
     * @return Userlogs
     */
    public function setNewlog($newlog)
    {
        $this->newlog = $newlog;

        return $this;
    }

    /**
     * Get Log
     *
     * @return integer
     */
    public function getNewlog()
    {
        return $this->newlog;
    }

    /**
     * Set Fecha
     *
     * @param string $fecha
     * @return Userlogs
     */
    public function setFecha($fecha)
    {
        $this->fecha = $fecha;

        return $this;
    }

    /**
     * Get Log
     *
     * @return string
     */
    public function getFecha()
    {
        return $this->fecha;
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
     * Set userid
     *
     * @param \AdminBundle\Entity\User $userid
     * @return Userlogs
     */
    public function setUserid(\AdminBundle\Entity\User $userid = null)
    {
        $this->userid = $userid;

        return $this;
    }

    /**
     * Get userid
     *
     * @return \AdminBundle\Entity\User
     */
    public function getUserid()
    {
        return $this->userid;
    }
}
