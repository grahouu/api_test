<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Contacts;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations\View;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Delete;
use FOS\RestBundle\Controller\Annotations\Put;
use Symfony\Component\HttpFoundation\RequestStack;

class ContactsController extends FOSRestController
{
    const TOKEN = "azerty";

    private function checkUser(String $token)
    {
        if (strcmp(self::TOKEN, $token) == 0 )
            return true;
        return false;
    }

    public function preExecute(RequestStack $requestStack)
    {
        $req = $requestStack->getCurrentRequest();
        $token = $req->headers->get('token');
        if ($token == NULL || !self::checkUser($token))
            throw $this->createAccessDeniedException('You cannot access this page!');
    }

    private function checkValues($array){
        $nameExist = array_key_exists('name', $array);
        $phoneExist = array_key_exists('phone', $array);

        if ($nameExist && $phoneExist)
            return true;
        else
            return false;
    }

    /**
    * GET Route annotation.
    * @Get("/contacts")
    */
    public function getContactsAction(Request $request)
    {
        $contacts = $this->getDoctrine()->getRepository('AppBundle:Contacts')->findAll();
        $JSON_RETURN["data"] = $contacts;
        $JSON_RETURN["success"] = true;
        return $JSON_RETURN;
    }

    /**
    * GET Route annotation.
    * @Get("/contact/{id}")
    */
    public function getContactAction($id, Request $request)
    {
        $contact = $this->getDoctrine()->getRepository('AppBundle:Contacts')->findById($id);
        $JSON_RETURN["data"] = $contact;
        $JSON_RETURN["success"] = true;
        return $JSON_RETURN;
    }

    /**
    * POST Route annotation.
    * @Post("/contact")
    */
    public function postContactAction(Request $request) {
        $form = $request->request->all();

        if (!self::checkValues($form)){
            $JSON_RETURN["success"] = false;
            return $JSON_RETURN;
        }

        $contact = new Contacts();
        $contact->setName($form["name"]);
        $contact->setPhone($form["phone"]);
        $contact->setUpdated(new \DateTime());
        $contact->setCreated(new \DateTime());
        
        try {
            $em = $this->getDoctrine()->getManager();
            $contactRepository = $em->getRepository('AppBundle:Contacts');
            $em->persist($contact);
            $em->flush();
            $JSON_RETURN["data"] = $contact->getId();
            $JSON_RETURN["success"] = true;
        }catch(\Exception $e) {
            $JSON_RETURN["success"] = false;
        }
        return $JSON_RETURN;
    }


    /**
    * @Delete("/contact/{id}")
    */
    public function deleteContactAction($id, Request $request) {
        $contact = $this->getDoctrine()->getRepository('AppBundle:Contacts')->findById($id);

        if (!empty($contact)){
            $em = $this->getDoctrine()->getEntityManager();
            $em->remove($contact[0]);
            $em->flush();
            $JSON_RETURN["success"] = true;
        }else{
            $JSON_RETURN["success"] = false;
        }
        
        return $JSON_RETURN;
    }

    /**
    * @put("/contact/{id}")
    */
    public function putContactAction($id, Request $request){
        $em = $this->getDoctrine()->getManager();
        $contact = $em->getRepository('AppBundle:Contacts')->find($id);
        $JSON_RETURN["success"] = false;

        if ($contact){
            $form = $request->request->all();

            if (!self::checkValues($form)){
                $JSON_RETURN["success"] = false;
                return $JSON_RETURN;
            }

            $contact->setName($form["name"]);
            $contact->setPhone($form["phone"]);
            $contact->setUpdated(new \DateTime());
            $em->flush();
            $JSON_RETURN["success"] = true;
        }
        
        return $JSON_RETURN;

    }
}