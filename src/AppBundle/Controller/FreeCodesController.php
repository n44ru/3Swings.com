<?php

namespace AppBundle\Controller;

use AdminBundle\Entity\Codesfamily;
use AdminBundle\Entity\Comments;
use AdminBundle\Entity\Freecodes;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class FreeCodesController extends Controller
{
    /**
     * Get 3 free Codes
     *
     * @Route("/user/profile/codes/free/{_locale}", name="free_codes")
     * defaults={"_locale"="en"}, requirements={"_locale"="(es|en)"}
     * @Method({"GET", "POST"})
     */
    public function freecodesAction()
    {
        if ($this->getUser())
            $id = $this->getUser()->getId();
        else return $this->redirectToRoute('404');

        $em = $this->getDoctrine()->getManager();
        //FIX CODES FAMILY
        $family = new Codesfamily();
        $family->setName($this->getUser()->getUsername());
        $em->persist($family);
        $em->flush();
        //
        for ($i = 1; $i <= 3; $i++) {
            // Preparing the code.
            $time = date("d:m:y h:i:s A");
            $code_raw = $time . ";userid=" . $id . ',' . $i;
            // Encrypt the code.
            $factory = $this->get('security.encoder_factory');
            $encoder = $factory->getEncoder($this->getUser());
            $code_bcrypt = $encoder->encodePassword($code_raw, null);
            $code_bcrypt = substr($code_bcrypt,6,20);
            $codes = new Freecodes();
            $codes->setCode($code_bcrypt);
            $codes->setUserid($this->getUser());
            $codes->setActive(0);
            // Set the code family
            $codes->setCodesfamilyid($family);
            //
            $em->persist($codes);
            $em->flush();
        }
        return $this->redirectToRoute('user_panel');
    }
    /**
     * Jquery Load
     *
     * @Route("/dynamic_freecode_check/{_locale}", name="dynamic_freecode")
     * defaults={"_locale"="en"}, requirements={"_locale"="(es|en)"}
     * @Method({"GET", "POST"})
     */
    public function checkAction(Request $request)
    {
        $code_raw = $request->request->get('user_code');
        $code = $this->getCode($code_raw);
        if(count($code)>0)
        {
            $correct = true;
        }
        else $correct = false;
        //fix
        if($code!=null){
            $the_code= $code[0];
        }
        else $the_code=null;
        return $this->render('front/dynamic/message_free.html.twig', array('correct' => $correct, 'code'=> $the_code));
    }

    // Check if the code is correct.
    function getCode($code){
        $em = $this->getDoctrine()->getManager();
        $code = trim($code);
        $query5 = $em->createQuery('SELECT p FROM AdminBundle:Freecodes p WHERE p.code = ?1 and p.active = 0');
        $query5->setParameter(1, $code);
        $result = $query5->getResult();
        return $result;
    }
}