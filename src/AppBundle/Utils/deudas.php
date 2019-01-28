<?php
/**
 * Created by PhpStorm.
 * User: Carlos
 * Date: 23/10/2017
 * Time: 11:09
 */

namespace AppBundle\Utils;

use Doctrine\ORM\EntityManager;

class deudas
{
    protected $em;

    public function __construct(EntityManager $em) {
        $this->em = $em;
    }

    public function deuda($id)
    {
        $query = $this->em->createQuery('SELECT p FROM AdminBundle:Deudas p WHERE p.userid = ?1 ');
        $query->setParameter(1, $id);
        $deudas = $query->getResult();
        return count($deudas);
    }
}