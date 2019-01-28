<?php

namespace AppBundle\Utils;

use AdminBundle\Entity\Codes;
use Doctrine\ORM\EntityManager;

class clean
{
    protected $em;

    public function __construct(EntityManager $em) {
        $this->em = $em;
    }

    public function users()
    {
        // Delete temporal users.
        $query = $this->em->createQuery('DELETE FROM AdminBundle:User p WHERE p.temp = 1');
        $query->getResult();
    }
    public function codes($familyid)
    {
        // Delete used codes.
        $query = $this->em->createQuery('DELETE FROM AdminBundle:CodesFamily p WHERE p.id = ?1');
        $query->setParameter('1', $familyid);
        $query->getResult();
    }
}