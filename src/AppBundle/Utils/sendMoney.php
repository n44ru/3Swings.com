<?php
/**
 * Created by PhpStorm.
 * User: carlosmanuel
 * Date: 6/13/17
 * Time: 10:44 p.m.
 */

namespace AppBundle\Utils;

use AdminBundle\Entity\Userlogs;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Validator\Constraints\Date;

class sendMoney
{
    protected $em;

    public function __construct(EntityManager $em) {
        $this->em = $em;
    }

    public function getadminid()
    {
        $query = $this->em->createQuery('SELECT p FROM AdminBundle:User p WHERE p.role = ?1 ');
        $query->setParameter(1, 'ROLE_ADMIN');
        $admin = $query->getResult();

//        $query = $this->em->createQuery('SELECT p FROM AdminBundle:Userdata p WHERE p.userid = ?1 ');
//        $query->setParameter(1, $admin[0]->getId());
//        $data = $query->getResult();
//
//        $current = $data[0]->getMoney();
//        $data[0]->setMoney($current+$money);
//        $this->em->persist($data[0]);
//        $this->em->flush();

        return $admin[0]->getId();
    }
}