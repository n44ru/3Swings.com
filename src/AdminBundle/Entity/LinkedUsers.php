<?php
/**
 * Created by PhpStorm.
 * User: carlosmanuel
 * Date: 5/31/17
 * Time: 11:42 p.m.
 */

namespace AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Bussines
 *
 * @ORM\Table(name="LinkedUsers", uniqueConstraints={@ORM\UniqueConstraint(name="id", columns={"id"})})
 * @ORM\Entity
 */

class LinkedUsers
{
    /**
     * @var string
     *
     * @ORM\Column(name="joindate", type="string", length=255, nullable=true)
     */
    private $joindate;

    /**
     * @var integer
     *
     * @ORM\Column(name="linked", type="integer", length=1, nullable=false)
     */
    private $linked;

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
     * @var \AdminBundle\Entity\Bussines
     *
     * @ORM\ManyToOne(targetEntity="AdminBundle\Entity\Bussines")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="Bussinesid", referencedColumnName="id", onDelete="CASCADE")
     * })
     */
    private $bussinesid;

    /**
     * Set new
     * @param string $joindate
     * @return LinkedUsers
     */
    public function setJoindate($joindate)
    {
        $this->joindate = $joindate;

        return $this;
    }

    /**
     * Get s1
     * @return string
     */
    public function getJoinDate()
    {
        return $this->joindate;
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
     * @return LinkedUsers
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
     * Set bussinesid
     *
     * @param \AdminBundle\Entity\Bussines $bussinesid
     * @return LinkedUsers
     */
    public function setBussinesid(\AdminBundle\Entity\Bussines $bussinesid = null)
    {
        $this->bussinesid = $bussinesid;

        return $this;
    }

    /**
     * Get bussinesid
     *
     * @return \AdminBundle\Entity\Bussines 
     */
    public function getBussinesid()
    {
        return $this->bussinesid;
    }

    /**
     * Set linked
     *
     * @param integer $linked
     *
     * @return LinkedUsers
     */
    public function setLinked($linked)
    {
        $this->linked = $linked;

        return $this;
    }

    /**
     * Get linked
     *
     * @return integer
     */
    public function getLinked()
    {
        return $this->linked;
    }
}
