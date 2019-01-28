<?php

namespace AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Banner
 *
 * @ORM\Table(name="Faq", uniqueConstraints={@ORM\UniqueConstraint(name="id", columns={"id"})})
 * @ORM\Entity
 */

class Faq
{
    /**
     * @var string
     *
     * @ORM\Column(name="qes", type="string", length=1000, nullable=true)
     */
    private $qes;

    /**
     * @var string
     *
     * @ORM\Column(name="qen", type="string", length=1000, nullable=true)
     */
    private $qen;

    /**
     * @var string
     *
     * @ORM\Column(name="aes", type="string", length=4000, nullable=true)
     */
    private $aes;

    /**
     * @var string
     *
     * @ORM\Column(name="aen", type="string", length=4000, nullable=true)
     */
    private $aen;

    /**
     * @var \AdminBundle\Entity\Topic
     *
     * @ORM\ManyToOne(targetEntity="AdminBundle\Entity\Topic")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="Topicid", referencedColumnName="id", onDelete="CASCADE")
     * })
     */
    private $topicid;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * Set qes
     *
     * @param string $qes
     * @return Faq
     */
    public function setQes($qes)
    {
        $this->qes = $qes;

        return $this;
    }

    /**
     * Get qes
     *
     * @return string 
     */
    public function getQes()
    {
        return $this->qes;
    }

    /**
     * Set qen
     *
     * @param string $qen
     * @return Faq
     */
    public function setQen($qen)
    {
        $this->qen = $qen;

        return $this;
    }

    /**
     * Get qen
     *
     * @return string 
     */
    public function getQen()
    {
        return $this->qen;
    }

    /**
     * Set aes
     *
     * @param string $aes
     * @return Faq
     */
    public function setAes($aes)
    {
        $this->aes = $aes;

        return $this;
    }

    /**
     * Get aes
     *
     * @return string 
     */
    public function getAes()
    {
        return $this->aes;
    }

    /**
     * Set aen
     *
     * @param string $aen
     * @return Faq
     */
    public function setAen($aen)
    {
        $this->aen = $aen;

        return $this;
    }

    /**
     * Get aen
     *
     * @return string 
     */
    public function getAen()
    {
        return $this->aen;
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
     * Set topicid
     *
     * @param \AdminBundle\Entity\Topic $topicid
     *
     * @return Faq
     */
    public function setTopicid(\AdminBundle\Entity\Topic $topicid = null)
    {
        $this->topicid = $topicid;

        return $this;
    }

    /**
     * Get topicid
     *
     * @return \AdminBundle\Entity\Topic
     */
    public function getTopicid()
    {
        return $this->topicid;
    }
}
