<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use AppBundle\Form\ContactType;
use AppBundle\Entity\Contacts;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        $contact = new Contacts();
        $form = $this->createForm(ContactType::class, $contact);

        return $this->render(
            'default/index.html.twig',
            array('form' => $form->createView())
        );

    }
}
