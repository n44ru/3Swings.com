<?php

namespace AppBundle\Utils;

use Doctrine\ORM\EntityManager;

class getLogs
{
    protected $em;

    public function __construct(EntityManager $em) {
        $this->em = $em;
    }

    public function userlogs($id)
    {
        $query = $this->em->createQuery('SELECT p FROM AdminBundle:Userlogs p WHERE p.userid = ?1 and p.newlog = 1');
        $query->setParameter(1, $id);
        $logs = $query->getResult();
        return $logs;
    }
}