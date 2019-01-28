<?php

namespace AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Bussines
 *
 * @ORM\Table(name="Bussines", uniqueConstraints={@ORM\UniqueConstraint(name="id", columns={"id"})})
 * @ORM\Entity
 */
class Bussines
{
    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=true)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=1000, nullable=true)
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="descriptionen", type="string", length=1000, nullable=true)
     */
    private $descriptionen;

    /**
     * @var string
     *
     * @ORM\Column(name="address", type="string", length=1000, nullable=true)
     */
    private $address;

    /**
     * @var integer
     *
     * @ORM\Column(name="price", type="integer", length=1000, nullable=true)
     */
    private $price;

    /**
     * @var string
     *
     * @ORM\Column(name="descuento", type="string", length=1000, nullable=true)
     */
    private $descuento;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=1000, nullable=true)
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="website", type="string", length=1000, nullable=true)
     */
    private $website;

    /**
     * @var string
     *
     * @ORM\Column(name="country", type="string", length=1000, nullable=true)
     */
    private $country;

    /**
     * @var string
     *
     * @ORM\Column(name="estate", type="string", length=1000, nullable=true)
     */
    private $estate;

    /**
     * @var string
     *
     * @ORM\Column(name="postalcode", type="string", length=1000, nullable=true)
     */
    private $postalcode;

    /**
     * @var string
     *
     * @ORM\Column(name="rating", type="integer", length=10, nullable=false)
     */
    private $rating;

    /**
     * @var string
     *
     * @ORM\Column(name="pago", type="integer", length=1, nullable=false)
     */
    private $pago;

    /**
     * @var string
     *
     * @ORM\Column(name="facebook", type="string", length=1000, nullable=true)
     */
    private $facebook;

    /**
     * @var string
     *
     * @ORM\Column(name="instagram", type="string", length=1000, nullable=true)
     */
    private $instagram;

    /**
     * @var integer
     *
     * @ORM\Column(name="no", type="integer", length=10, nullable=false)
     */
    private $no;

    /**
     *
     * @Assert\File(maxSize="6000000")
     */
    private $file;

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
     * @var \AdminBundle\Entity\Category
     *
     * @ORM\ManyToOne(targetEntity="AdminBundle\Entity\Category")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="Categoryid", referencedColumnName="id", onDelete="CASCADE")
     * })
     */
    private $categoryid;



    /**
     * Set name
     *
     * @param string $name
     * @return Bussines
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Bussines
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set descriptionen
     *
     * @param string $descriptionen
     * @return Bussines
     */
    public function setDescriptionen($descriptionen)
    {
        $this->descriptionen = $descriptionen;

        return $this;
    }

    /**
     * Get descriptionen
     *
     * @return string
     */
    public function getDescriptionen()
    {
        return $this->descriptionen;
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
     * @return Bussines
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
     * Set categoryid
     *
     * @param \AdminBundle\Entity\Category $categoryid
     * @return Bussines
     */
    public function setCategoryid(\AdminBundle\Entity\Category $categoryid = null)
    {
        $this->categoryid = $categoryid;

        return $this;
    }

    /**
     * Get categoryid
     *
     * @return \AdminBundle\Entity\Category
     */
    public function getCategoryid()
    {
        return $this->categoryid;
    }

    /**
     * Sets file.
     *
     * @param UploadedFile $file
     */
    public function setFile(UploadedFile $file = null)
    {
        $this->file = $file;
    }

    /**
     * Get file.
     *
     * @return UploadedFile
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * Set address
     *
     * @param string $address
     * @return Bussines
     */
    public function setAddress($address)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * Get address
     *
     * @return string 
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Set price
     *
     * @param integer $price
     * @return Bussines
     */
    public function setPrice($price)
    {
        $this->price = $price;

        return $this;
    }

    /**
     * Get price
     *
     * @return integer
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Set descuento
     *
     * @param string $descuento
     * @return Bussines
     */
    public function setDescuento($descuento)
    {
        $this->descuento = $descuento;

        return $this;
    }

    /**
     * Get descuento
     *
     * @return string 
     */
    public function getDescuento()
    {
        return $this->descuento;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return Bussines
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string 
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set website
     *
     * @param string $website
     * @return Bussines
     */
    public function setWebsite($website)
    {
        $this->website = $website;

        return $this;
    }

    /**
     * Get website
     *
     * @return string 
     */
    public function getWebsite()
    {
        return $this->website;
    }

    /**
     * Set country
     *
     * @param string $country
     * @return Bussines
     */
    public function setCountry($country)
    {
        $this->country = $country;

        return $this;
    }

    /**
     * Get country
     *
     * @return string 
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * Set estate
     *
     * @param string $estate
     * @return Bussines
     */
    public function setEstate($estate)
    {
        $this->estate = $estate;

        return $this;
    }

    /**
     * Get estate
     *
     * @return string 
     */
    public function getEstate()
    {
        return $this->estate;
    }

    /**
     * Set postalcode
     *
     * @param string $postalcode
     * @return Bussines
     */
    public function setPostalcode($postalcode)
    {
        $this->postalcode = $postalcode;

        return $this;
    }

    /**
     * Get postalcode
     *
     * @return string 
     */
    public function getPostalcode()
    {
        return $this->postalcode;
    }

    /**
     * Set rating
     *
     * @param int $rating
     * @return Bussines
     */
    public function setRating($rating)
    {
        $this->rating = $rating;

        return $this;
    }

    /**
     * Get postalcode
     *
     * @return int
     */
    public function getRating()
    {
        return $this->rating;
    }

    /**
     * Set pago
     *
     * @param int $pago
     * @return Bussines
     */
    public function setPago($pago)
    {
        $this->pago = $pago;

        return $this;
    }

    /**
     * Get pago
     *
     * @return int
     */
    public function getPago()
    {
        return $this->pago;
    }

    public function __toString()
    {
        return $this->name;
    }

    /**
     * Set facebook
     *
     * @param string $facebook
     *
     * @return Bussines
     */
    public function setFacebook($facebook)
    {
        $this->facebook = $facebook;

        return $this;
    }

    /**
     * Get facebook
     *
     * @return string
     */
    public function getFacebook()
    {
        return $this->facebook;
    }

    /**
     * Set instagram
     *
     * @param string $instagram
     *
     * @return Bussines
     */
    public function setInstagram($instagram)
    {
        $this->instagram = $instagram;

        return $this;
    }

    /**
     * Get instagram
     *
     * @return string
     */
    public function getInstagram()
    {
        return $this->instagram;
    }

    /**
     * Set No
     *
     * @param integer $no
     *
     * @return Bussines
     */
    public function setNo($no)
    {
        $this->no = $no;

        return $this;
    }

    /**
     * Get No
     *
     * @return integer
     */
    public function getNo()
    {
        return $this->no;
    }
}
