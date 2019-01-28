<?php

namespace AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * User
 *
 * @ORM\Table(name="Userdata", uniqueConstraints={@ORM\UniqueConstraint(name="id", columns={"id"})})
 * @ORM\Entity
 */

class Userdata
{
    /**
     * @var double
     *
     * @ORM\Column(name="money", type="float", length=10, nullable=false)
     */
    private $money;

    /**
     * @var integer
     *
     * @ORM\Column(name="level", type="integer", length=10, nullable=false)
     */
    private $level;

    /**
     * @var integer
     *
     * @ORM\Column(name="stats1", type="integer", length=10, nullable=false)
     */
    private $stats1;

    /**
     * @var integer
     *
     * @ORM\Column(name="stats2", type="integer", length=10, nullable=false)
     */
    private $stats2;

    /**
     * @var integer
     *
     * @ORM\Column(name="stats3", type="integer", length=10, nullable=false)
     */
    private $stats3;

    /**
     * @var integer
     *
     * @ORM\Column(name="first", type="float", nullable=false)
     */
    private $first;

    /**
     * @var string
     *
     * @ORM\Column(name="invitationcode", type="string", length=255 , nullable=true)
     */
    private $invitationcode;

    /**
     * @var string
     *
     * @ORM\Column(name="activationcode", type="string", length=255 , nullable=true)
     */
    private $activationcode;

    /**
     * @var string
     *
     * @ORM\Column(name="extractiondate", type="string", length=255 , nullable=true)
     */
    private $extractiondate;

    /**
     * @var string
     *
     * @ORM\Column(name="dad", type="string", length=255 , nullable=true)
     */
    private $dad;

    /**
     * @var string
     *
     * @ORM\Column(name="levelup", type="string", length=255 , nullable=true)
     */
    private $levelup;

    /**
     * @var string
     *
     * @ORM\Column(name="plandate", type="string", length=255 , nullable=true)
     */
    private $plandate;

    /**
     * @var integer
     *
     * @ORM\Column(name="yes", type="integer", length=10, nullable=false)
     */
    private $yes;

    /**
     * @var integer
     *
     * @ORM\Column(name="no", type="integer", length=10, nullable=false)
     */
    private $no;

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
     * User constructor.
     */
    public function __construct()
    {
    }

    /**
     * Set Money
     *
     * @param double $money
     * @return Userdata
     */
    public function setMoney($money)
    {
        $this->money = $money;

        return $this;
    }

    /**
     * Get Money
     *
     * @return double
     */
    public function getMoney()
    {
        return $this->money;
    }

    /**
     * Set Level
     * @param integer $level
     * @return Userdata
     */
    public function setLevel($level)
    {
        $this->level = $level;

        return $this;
    }

    /**
     * Get Level
     * @return integer
     */
    public function getLevel()
    {
        return $this->level;
    }

    /**
     * Set s1
     * @param integer $stats1
     * @return Userdata
     */
    public function setStats1($stats1)
    {
        $this->stats1 = $stats1;

        return $this;
    }

    /**
     * Get s1
     * @return integer
     */
    public function getStats1()
    {
        return $this->stats1;
    }

    /**
     * Set s2
     * @param integer $stats2
     * @return Userdata
     */
    public function setStats2($stats2)
    {
        $this->stats2 = $stats2;

        return $this;
    }

    /**
     * Get s2
     * @return integer
     */
    public function getStats2()
    {
        return $this->stats2;
    }

    /**
     * Set s3
     * @param integer $stats3
     * @return Userdata
     */
    public function setStats3($stats3)
    {
        $this->stats3 = $stats3;

        return $this;
    }

    /**
     * Get s3
     * @return integer
     */
    public function getStats3()
    {
        return $this->stats3;
    }

    /**
     * Set First Payment
     * @param double $first
     * @return Userdata
     */
    public function setFirst($first)
    {
        $this->first = $first;

        return $this;
    }

    /**
     * Get First Payment
     * @return integer
     */
    public function getFirst()
    {
        return $this->first;
    }

    /**
     * Set Invitation Code
     * @param string $invitationcode
     * @return Userdata
     */
    public function setInvitationcode($invitationcode)
    {
        $this->invitationcode = $invitationcode;

        return $this;
    }

    /**
     * Get Invitation Code
     * @return string
     */
    public function getInvitationcode()
    {
        return $this->invitationcode;
    }

    /**
     * Set Activation Code
     * @param string $activationcode
     * @return Userdata
     */
    public function setActivationcode($activationcode)
    {
        $this->activationcode = $activationcode;

        return $this;
    }

    /**
     * Get Activation Code
     * @return string
     */
    public function getActivationcode()
    {
        return $this->activationcode;
    }

    /**
     * Set Invitation Code
     * @param string $extractiondate
     * @return Userdata
     */
    public function setExtractiondate($extractiondate)
    {
        $this->extractiondate = $extractiondate;

        return $this;
    }

    /**
     * Get Invitation Code
     * @return string
     */
    public function getExtractiondate()
    {
        return $this->extractiondate;
    }

    /**
     * Set Dad
     * @param string $dad
     * @return Userdata
     */
    public function setDad($dad)
    {
        $this->dad = $dad;
        return $this;
    }

    /**
     * Get Dad
     * @return string
     */
    public function getDad()
    {
        return $this->dad;
    }

    /**
     * Set Levelup
     * @param string $levelup
     * @return Userdata
     */
    public function setLevelup($levelup)
    {
        $this->levelup = $levelup;
        return $this;
    }

    /**
     * Get Plandate
     * @return string
     */
    public function getPlandate()
    {
        return $this->plandate;
    }

    /**
     * Set Plandate
     * @param string $plandate
     * @return Userdata
     */
    public function setPlandate($plandate)
    {
        $this->plandate = $plandate;
        return $this;
    }

    /**
     * Get Levelup
     * @return string
     */
    public function getLevelup()
    {
        return $this->levelup;
    }

    /**
     * Set Yes
     * @param integer $yes
     * @return Userdata
     */
    public function setYes($yes)
    {
        $this->yes = $yes;
        return $this;
    }

    /**
     * Get Yes
     * @return integer
     */
    public function getYes()
    {
        return $this->yes;
    }

    /**
     * Set No
     * @param integer $no
     * @return Userdata
     */
    public function setNo($no)
    {
        $this->no = $no;
        return $this;
    }

    /**
     * Get No
     * @return integer
     */
    public function getNo()
    {
        return $this->no;
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
     * @return Userdata
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
     * @return Userdata
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
}
