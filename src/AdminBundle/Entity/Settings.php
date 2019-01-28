<?php

namespace AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Banner
 *
 * @ORM\Table(name="Settings", uniqueConstraints={@ORM\UniqueConstraint(name="id", columns={"id"})})
 * @ORM\Entity
 */

class Settings
{
    /**
     * @var string
     *
     * @ORM\Column(name="terms", type="text", nullable=true)
     */
    private $terms;

    /**
     * @var string
     *
     * @ORM\Column(name="termsen", type="text", nullable=true)
     */
    private $termsen;

    /**
     * @var string
     *
     * @ORM\Column(name="termstwo", type="text", nullable=true)
     */
    private $termstwo;

    /**
     * @var string
     *
     * @ORM\Column(name="termstwoen", type="text", nullable=true)
     */
    private $termstwoen;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * Set terms
     *
     * @param string $terms
     *
     * @return Settings
     */
    public function setTerms($terms)
    {
        $this->terms = $terms;

        return $this;
    }

    /**
     * Get terms
     *
     * @return string
     */
    public function getTerms()
    {
        return $this->terms;
    }

    /**
     * Set termsen
     *
     * @param string $termsen
     *
     * @return Settings
     */
    public function setTermsen($termsen)
    {
        $this->termsen = $termsen;

        return $this;
    }

    /**
     * Get termsen
     *
     * @return string
     */
    public function getTermsen()
    {
        return $this->termsen;
    }

    /**
     * Set termstwo
     *
     * @param string $termstwo
     *
     * @return Settings
     */
    public function setTermstwo($termstwo)
    {
        $this->termstwo = $termstwo;

        return $this;
    }

    /**
     * Get termstwo
     *
     * @return string
     */
    public function getTermstwo()
    {
        return $this->termstwo;
    }

    /**
     * Set termstwoen
     *
     * @param string $termstwoen
     *
     * @return Settings
     */
    public function setTermstwoen($termstwoen)
    {
        $this->termstwoen = $termstwoen;

        return $this;
    }

    /**
     * Get termstwoen
     *
     * @return string
     */
    public function getTermstwoen()
    {
        return $this->termstwoen;
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
}
