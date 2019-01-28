<?php

namespace AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Recomended
 *
 * @ORM\Table(name="Recomended", uniqueConstraints={@ORM\UniqueConstraint(name="id", columns={"id"})})
 * @ORM\Entity
 */

class Recomended
{
    /**
     * @var integer
     *
     * @ORM\Column(name="weigth", type="integer", length=10, nullable=false)
     */
    private $weigth;

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
     * Set weigth
     *
     * @param integer $weigth
     * @return Recomended
     */
    public function setWeigth($weigth)
    {
        $this->weigth = $weigth;

        return $this;
    }

    /**
     * Get weigth
     *
     * @return integer 
     */
    public function getWeigth()
    {
        return $this->weigth;
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
     * @return Recomended
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
}
