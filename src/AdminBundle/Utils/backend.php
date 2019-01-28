<?php

namespace AdminBundle\Utils;

use Doctrine\ORM\EntityManager;

class backend
{
    protected $em;

    public function __construct(EntityManager $em) {
        $this->em = $em;
    }

    public function diferencia($fecha)
    {
        $date1 = new \DateTime("now");
        $date2 = new \DateTime($fecha);
        $diff = $date1->diff($date2);
        $days = $diff->days;
        return $days;
    }
    public function automatic_degrade(){
        $query = $this->em->createQuery('SELECT p FROM AdminBundle:Userdata p WHERE p.level > 0');
        $data = $query->getResult();
        $today = date('Y-m-d');
        // check if someone is exceed on days. AUTOMATICALLY.
        $queryz = $this->em->createQuery('SELECT p FROM AdminBundle:Plan p ORDER BY p.level ASC');
        $plans = $queryz->getResult();
        $first= $plans[0];
        for($i=0;$i<count($data);$i++){
            $days= $this->diferencia($data[$i]->getPlandate());
            if($days>365 && $data[$i]->getuserid()->getRole()!='ROLE_ADMIN'){
                // Low them all.
                $data[$i]->setPlanid($first);
                $data[$i]->setLevel($first->getLevel());
                $data[$i]->setStats1($first->getXp());
                $data[$i]->setPlandate($today);
                $this->em->persist($data[$i]);
                $this->em->flush();
                // Usuario degradado
                $titulo = "3Swings: Usuario degradado por no renovar la membresia.";
                //
                $to = 'info3swings@gmail.com';
                //
                $cabeceras = 'MIME-Version: 1.0' . "\r\n";
                $cabeceras .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
                $mensaje = "El usuario ".$data[$i]->getUserid()->getUsername()." ha sido degradado por no renovar su membresia en 1 año. Esto el sitio lo hizo automaticamente.";
                //
                mail($to,$titulo,$mensaje,$cabeceras);
            }
        }
    }
}