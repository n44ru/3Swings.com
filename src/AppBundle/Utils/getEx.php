<?php

namespace AppBundle\Utils;

use Doctrine\ORM\EntityManager;
//use AdminBundle\Entity\Userextractions;

class getEx
{
    protected $em;

    public function __construct(EntityManager $em) {
        $this->em = $em;
    }

    public function Extraction()
    {
        $users = $this->em->getRepository('AdminBundle:Userextractions')->findAll();
        return $users;
    }
}