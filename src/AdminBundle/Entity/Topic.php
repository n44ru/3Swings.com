<?php

namespace AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Banner
 *
 * @ORM\Table(name="Topic", uniqueConstraints={@ORM\UniqueConstraint(name="id", columns={"id"})})
 * @ORM\Entity
 */

class Topic
{
    /**
     * @var string
     *
     * @ORM\Column(name="topices", type="string", length=1000, nullable=true)
     */
    private $topices;

    /**
     * @var string
     *
     * @ORM\Column(name="topicen", type="string", length=1000, nullable=true)
     */
    private $topicen;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * Set topices
     *
     * @param string $topices
     *
     * @return Topic
     */
    public function setTopices($topices)
    {
        $this->topices = $topices;

        return $this;
    }

    /**
     * Get topices
     *
     * @return string
     */
    public function getTopices()
    {
        return $this->topices;
    }

    /**
     * Set topicen
     *
     * @param string $topicen
     *
     * @return Topic
     */
    public function setTopicen($topicen)
    {
        $this->topicen = $topicen;

        return $this;
    }

    /**
     * Get topicen
     *
     * @return string
     */
    public function getTopicen()
    {
        return $this->topicen;
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
        return $this->topices;
    }
}
