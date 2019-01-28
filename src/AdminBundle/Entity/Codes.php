<?php

namespace AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Bussines
 *
 * @ORM\Table(name="Codes", uniqueConstraints={@ORM\UniqueConstraint(name="id", columns={"id"})})
 * @ORM\Entity
 */
class Codes
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
     * @var \AdminBundle\Entity\Plan
     *
     * @ORM\ManyToOne(targetEntity="AdminBundle\Entity\Plan")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="Planid", referencedColumnName="id", onDelete="CASCADE")
     * })
     */
    private $planid;

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
     * @return Codes
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
     * @return Codes
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
     * @return Codes
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
     * Set planid
     *
     * @param \AdminBundle\Entity\Plan $planid
     * @return Codes
     */
    public function setPlanid(\AdminBundle\Entity\Plan $planid = null)
    {
        $this->planid = $planid;

        return $this;
    }

    /**
     * Get planid
     *
     * @return \AdminBundle\Entity\Plan
     */
    public function getPlanid()
    {
        return $this->planid;
    }

    /**
     * Set codesfamily
     *
     * @param \AdminBundle\Entity\Codesfamily $codesfamilyid
     * @return Codes
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
