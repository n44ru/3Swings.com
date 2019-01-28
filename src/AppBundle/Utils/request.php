<?php

namespace AppBundle\Utils;

use Doctrine\ORM\EntityManager;

class request
{
    protected $em;

    public function __construct(EntityManager $em) {
        $this->em = $em;
    }

    public function myrequests($id)
    {
        // Ver si tiene negocios
        $query = $this->em->createQuery('SELECT p FROM AdminBundle:Bussines p WHERE p.userid = ?1');
        $query->setParameter(1, $id);
        $negocios = $query->getResult();
        // Todos los linked users con 2
        $query2 = $this->em->createQuery('SELECT p FROM AdminBundle:LinkedUsers p WHERE p.linked = 2');
        $linked = $query2->getResult();
        //
        $users = array();
        $temp=0;
        if(count($negocios)>0){
            for($i=0;$i<count($negocios);$i++){
                $id=$negocios[$i]->getId();
                for($j=0;$j<count($linked);$j++){
                    if($linked[$j]->getBussinesid()->getId()== $id){
                        $users[$temp]=$linked[$j];
                        $temp++;
                    }
                }
            }
        }
        return $users;
    }
}