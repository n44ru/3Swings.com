<?php

namespace AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Banner
 *
 * @ORM\Table(name="Freecodes", uniqueConstraints={@ORM\UniqueConstraint(name="id", columns={"id"})})
 * @ORM\Entity
 */
class Freecodes
{
    /**
     * @var string
     *
     * @ORM\Column(name="code", type="string", length=255, nullable=true)
     */
    private $code;

    /**
     * @var integer
     *
     * @ORM\Column(name="active", type="integer", length=1, nullable=true)
     */
    private $active;

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
     * @var \AdminBundle\Entity\Codesfamily
     *
     * @ORM\ManyToOne(targetEntity="AdminBundle\Entity\Codesfamily")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="Codesfamilyid", referencedColumnName="id", onDelete="CASCADE")
     * })
     */
    private $codesfamilyid;

    /**
     * Set code
     *
     * @param string $code
     * @return Freecodes
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set active
     *
     * @param integer $active
     * @return Freecodes
     */
    public function setActive($active)
    {
        $this->active = $active;

        return $this;
    }

    /**
     * Get active
     *
     * @return integer
     */
    public function getActive()
    {
        return $this->active;
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
     * @return Freecodes
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

    /**
     * Set codesfamily
     *
     * @param \AdminBundle\Entity\Codesfamily $codesfamilyid
     * @return Freecodes
     */
    public function setCodesfamilyid(\AdminBundle\Entity\Codesfamily $codesfamilyid = null)
    {
        $this->codesfamilyid = $codesfamilyid;

        return $this;
    }

    /**
     * Get codesfamily
     *
     * @return \AdminBundle\Entity\Codesfamily
     */
    public function getCodesfamilyid()
    {
        return $this->codesfamilyid;
    }
}

