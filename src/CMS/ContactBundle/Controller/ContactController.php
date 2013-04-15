<?php

namespace CMS\ContactBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;

use CMS\ContactBundle\Entity\Contact;
use CMS\ContactBundle\Type\ContactType;

class ContactController extends Controller
{
    /**
     * @Route("/admin/contacts/list", name="contacts_list")
     * @Template("CMSContactBundle:admin:contact-list.html.twig")
     */
    public function listAction()
    {
        $messages = $this->getDoctrine()
                         ->getRepository('CMSContactBundle:Contact')
                         ->findAll();

        return array('active' => 'contact', 'messages' => $messages);
    }

    /**
     * @Route("/admin/contact/detail/{id}", name="contacts_detail")
     */
    public function detailAction($id)
    {
        $message = $this->getDoctrine()
                        ->getRepository('CMSContactBundle:Contact')
                        ->find($id);

        return array('detail' => $message);
    }

    /**
     * @Route("/admin/contacts/delete/{id}", name="contacts_delete")
     */
    public function deleteAction($id)
    {
        $message = $this->getDoctrine()
                        ->getRepository('CMSContactBundle:Contact')
                        ->find($id);
        $em = $this->getDoctrine()
                   ->getEntityManager();
        $em->remove($message);
        $em->flush();

        return $this->redirect($this->generateUrl('contacts_list'));
    }

    /**
     * @Route("/admin/contacts/new", name="contacts_new")
     */
    public function newItemAction(Request $request)
    {
        $contact = new Contact;
        $form = $this->createForm(new ContactType(), $contact);
        if ($request->isMethod('POST')) {

            $form->bind($request);

            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $contact->setStatut(0);
                $em->persist($contact);
                $em->flush();
            }
        }

        return array();
    }

    /**
     * @Route("/admin/contacts/item/{id}", name="contacts_item")
     */
    public function contactItemAction($id)
    {
        $contact = $this->getDoctrine()
                        ->getRepository('CMSContactBundle:Contact')
                        ->find($id);
        $contact_tab = array('id' => $contact->getId(), 'subject' => $contact->getSubject(), 'from' => htmlentities($contact->getFirstname().' '.$contact->getLastname().' <'.$contact->getSender().'>'), 'message' => $contact->getMessage());
        echo json_encode($contact_tab); die;

        return array();

    }

    /**
     * @Route("/admin/contacts/statut/{id}", name="contacts_read")
     */
    public function changeStatutAction($id)
    {
        $contact = $this->getDoctrine()
                        ->getRepository('CMSContactBundle:Contact')
                        ->find($id);
        if (is_object($contact)) {
            $em = $this->getDoctrine()->getEntityManager();
            $contact->setStatut(1);
            $em->persist($contact);
            $em->flush();

            return $this->redirect($this->generateUrl('contacts_list'));
        }
    }

}
