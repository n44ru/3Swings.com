<?php

namespace AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Category
 *
 * @ORM\Table(name="Plan", uniqueConstraints={@ORM\UniqueConstraint(name="id", columns={"id"})})
 * @ORM\Entity
 */

class Plan
{
    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=true)
     */
    private $name;

    /**
     * @var double
     *
     * @ORM\Column(name="precio", type="float", length=10, nullable=false)
     */
    private $precio;

    /**
     * @var integer
     *
     * @ORM\Column(name="level", type="integer", length=10, nullable=false)
     */
    private $level;

    /**
     * @var integer
     *
     * @ORM\Column(name="ganancia", type="integer", length=10, nullable=false)
     */
    private $ganancia;

    /**
     * @var integer
     *
     * @ORM\Column(name="xp", type="integer", nullable=false)
     */
    private $xp;

    /**
     * @var integer
     *
     * @ORM\Column(name="descuento", type="integer", nullable=false)
     */
    private $descuento;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;


    /**
     * Set name
     *
     * @param string $name
     * @return Plan
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
     * Set Precio
     *
     * @param double $precio
     * @return Plan
     */
    public function setPrecio($precio)
    {
        $this->precio = $precio;

        return $this;
    }

    /**
     * Get Precio
     *
     * @return double
     */
    public function getPrecio()
    {
        return $this->precio;
    }

    /**
     * Set Level
     *
     * @param integer $level
     * @return Plan
     */
    public function setLevel($level)
    {
        $this->level = $level;

        return $this;
    }

    /**
     * Get Level
     *
     * @return integer
     */
    public function getLevel()
    {
        return $this->level;
    }

    /**
     * Set Ganancia
     *
     * @param integer $ganancia
     * @return Plan
     */
    public function setGanancia($ganancia)
    {
        $this->ganancia = $ganancia;

        return $this;
    }

    /**
     * Get Ganancia
     *
     * @return integer
     */
    public function getGanancia()
    {
        return $this->ganancia;
    }

    /**
     * Set Xp
     *
     * @param integer $xp
     * @return Plan
     */

    public function setXp($xp)
    {
        $this->xp = $xp;

        return $this;
    }

    /**
     * Get xp
     *
     * @return integer
     */
    public function getXp()
    {
        return $this->xp;
    }

    /**
     * Set Descuento
     *
     * @param integer $descuento
     * @return Plan
     */

    public function setDescuento($descuento)
    {
        $this->descuento = $descuento;

        return $this;
    }

    /**
     * Get Descuento
     *
     * @return integer
     */
    public function getDescuento()
    {
        return $this->descuento;
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
    public function __toString()
    {
        return $this->name;
    }
}
