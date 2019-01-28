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
 * @ORM\Table(name="Comments", uniqueConstraints={@ORM\UniqueConstraint(name="id", columns={"id"})})
 * @ORM\Entity
 */

class Comments
{
    /**
     * @var integer
     *
     * @ORM\Column(name="stars", type="integer", length=10, nullable=false)
     */
    private $stars;

    /**
     * @var integer
     *
     * @ORM\Column(name="active", type="integer", length=10, nullable=false)
     */
    private $active;

    /**
     * @var string
     *
     * @ORM\Column(name="message", type="string", length=255, nullable=false)
     */
    private $message;

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
     * @return Comments
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
     * @return Comments
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
     * Set stars
     * @param integer $stars
     * @return Comments
     */
    public function setStars($stars)
    {
        $this->stars = $stars;

        return $this;
    }

    /**
     * Get stars
     * @return integer
     */
    public function getStars()
    {
        return $this->stars;
    }

    /**
     * Set Comments
     * @param string $message
     * @return Comments
     */
    public function setMessage($message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * Get s1
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Set active
     * @param integer $active
     * @return Comments
     */
    public function setActive($active)
    {
        $this->active = $active;

        return $this;
    }

    /**
     * Get stars
     * @return integer
     */
    public function getActive()
    {
        return $this->active;
    }
}
