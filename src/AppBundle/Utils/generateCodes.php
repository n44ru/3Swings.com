<?php

namespace AppBundle\Utils;

use AdminBundle\Entity\Codes;
use AdminBundle\Entity\Codesfamily;
use AdminBundle\Entity\Freecodes;
use Doctrine\ORM\EntityManager;

class generateCodes
{
    protected $em;

    public function __construct(EntityManager $em) {
        $this->em = $em;
    }

    public function generatefirstCodes($plan, $userid, $encoder)
    {
        $user = $this->em->getRepository('AdminBundle:User')->find($userid);
        //FIX CODES FAMILY
        $family = new Codesfamily();
        $family->setName($user->getUsername());
        $this->em->persist($family);
        $this->em->flush();
        //
        for ($i = 1; $i <= 3; $i++) {
            // Preparing the code.
            $time = date("d:m:y h:i:s A");
            $code_raw = $time . ";userid=" . $userid .','.$i;
            // Encrypt the code.
            //$factory = $this->get('security.encoder_factory');
            $code_bcrypt = $encoder->encodePassword($code_raw, null);
            $code_bcrypt = substr($code_bcrypt,6,30);
            $codes = new Codes();
            $codes->setCode($code_bcrypt);
            $codes->setUserid($user);
            $codes->setActive(0);
            $codes->setPlanid($plan);
            // Set the code family
            $codes->setCodesfamilyid($family);
            //
            $this->em->persist($codes);
            $this->em->flush();
        }
    }
    public function getremaining($family, $owner)
    {
        $query = $this->em->createQuery('SELECT p FROM AdminBundle:Codes p WHERE p.codesfamilyid = ?1 and p.userid = ?2 and p.active = 1');
        $query->setParameter(1, $family);
        $query->setParameter(2, $owner);
        $result = $query->getResult();
        return count($result);
    }
    // FREE CODES NEW!!!!
    public function generatefreefirstCodes($userid, $encoder)
    {
        $user = $this->em->getRepository('AdminBundle:User')->find($userid);
        //FIX CODES FAMILY
        $family = new Codesfamily();
        $family->setName($user->getUsername());
        $this->em->persist($family);
        $this->em->flush();
        //
        for ($i = 1; $i <= 3; $i++) {
            // Preparing the code.
            $time = date("d:m:y h:i:s A");
            $code_raw = $time . ";userid=" . $userid .','.$i;
            // Encrypt the code.
            //$factory = $this->get('security.encoder_factory');
            $code_bcrypt = $encoder->encodePassword($code_raw, null);
            $code_bcrypt = substr($code_bcrypt,6,20);
            $codes = new Freecodes();
            $codes->setCode($code_bcrypt);
            $codes->setUserid($user);
            $codes->setActive(0);
            // Set the code family
            $codes->setCodesfamilyid($family);
            //
            $this->em->persist($codes);
            $this->em->flush();
        }
    }
    public function getfreeremaining($family, $owner)
    {
        $query = $this->em->createQuery('SELECT p FROM AdminBundle:Freecodes p WHERE p.codesfamilyid = ?1 and p.userid = ?2 and p.active = 1');
        $query->setParameter(1, $family);
        $query->setParameter(2, $owner);
        $result = $query->getResult();
        return count($result);
    }
    // GENERATE THE $10 codes on 3 free codes used.
    public function generateTenCodes($plan,$userid, $encoder)
    {
        $user = $this->em->getRepository('AdminBundle:User')->find($userid);
        //FIX CODES FAMILY
        $family = new Codesfamily();
        $family->setName($user->getUsername().'-FREE');
        $this->em->persist($family);
        $this->em->flush();
        //
        for ($i = 1; $i <= 3; $i++) {
            // Preparing the code.
            $time = date("d:m:y h:i:s A");
            $code_raw = $time . ";userid=" . $userid .','.$i;
            // Encrypt the code.
            //$factory = $this->get('security.encoder_factory');
            $code_bcrypt = $encoder->encodePassword($code_raw, null);
            $code_bcrypt = substr($code_bcrypt,6,30);
            $codes = new Codes();
            $codes->setCode($code_bcrypt);
            $codes->setUserid($user);
            $codes->setActive(0);
            $codes->setPlanid($plan);
            // Set the code family
            $codes->setCodesfamilyid($family);
            //
            $this->em->persist($codes);
            $this->em->flush();
        }
    }
    /* This part is for the registration security codes sent by email to the user.*/
    public function mailcode($userid)
    {
        $rand = mt_rand(1000,100000);
        $number = $userid.$rand;
        return $number;
    }

}