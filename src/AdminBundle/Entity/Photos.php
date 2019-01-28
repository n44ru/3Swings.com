<?php

namespace AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Photos
 *
 * @ORM\Table(name="Photos", uniqueConstraints={@ORM\UniqueConstraint(name="id", columns={"id"})})
 * @ORM\Entity
 */
class Photos
{
    /**
     * @var string
     *
     * @ORM\Column(name="path", type="string", length=255, nullable=true)
     */
    private $path;

    /**
     * @var string
     *
     * @ORM\Column(name="tag", type="string", length=10, nullable=true)
     */
    private $tag;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

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
     * @var \AdminBundle\Entity\User
     *
     * @ORM\ManyToOne(targetEntity="AdminBundle\Entity\User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="Userid", referencedColumnName="id", onDelete="CASCADE")
     * })
     */
    private $userid;



    /**
     * Set path
     *
     * @param string $path
     * @return Photos
     */
    public function setPath($path)
    {
        $this->path = $path;

        return $this;
    }

    /**
     * Get path
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Set tag
     *
     * @param string $tag
     * @return Photos
     */
    public function setTag($tag)
    {
        $this->tag = $tag;

        return $this;
    }

    /**
     * Get tag
     *
     * @return string 
     */
    public function getTag()
    {
        return $this->tag;
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
     * Set bussinesid
     *
     * @param \AdminBundle\Entity\Bussines $bussinesid
     * @return Photos
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
     * Set userid
     *
     * @param \AdminBundle\Entity\User $userid
     * @return Photos
     */
    public function setUserid(\AdminBundle\Entity\User $userid = null)
    {
        $this->userid = $userid;

        return $this;
    }

    /**
     * Get Userid
     *
     * @return \AdminBundle\Entity\User
     */
    public function getUserid()
    {
        return $this->userid;
    }
}
