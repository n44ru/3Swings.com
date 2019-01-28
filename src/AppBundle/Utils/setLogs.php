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

class setLogs
{
    protected $em;

    public function __construct(EntityManager $em) {
        $this->em = $em;
    }

    public function setuserlogs($log, $userid)
    {
        $user = $this->em->getRepository('AdminBundle:User')->find($userid);
        $today = date("d/m/y");
        $userlogs = new Userlogs();
        $userlogs->setLog($log);
        $userlogs->setUserid($user);
        $userlogs->setFecha($today);
        $userlogs->setNewlog(1);
        $this->em->persist($userlogs);
        $this->em->flush();
    }
}