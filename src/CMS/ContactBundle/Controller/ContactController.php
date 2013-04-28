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
     * @Route("/admin/contacts/list/{page}", name="contacts_list", defaults={"page": 1})
     * @Template("CMSContactBundle:admin:contact-list.html.twig")
     */
    public function listAction(Request $request, $page)
    {
        $results = $this->_getElements($request, $page);

        return array(
            'pagination' => $results['pagination'], 
            'nb'         => $results['nb']
        );
    }

    /**
     * Récupère les éléments de la page courante et de la langue par défaut
     * 
     * @param Request    $request         object request
     * @param int        $page            page courante
     * @param CMLanguage $defaultLanguage Langue par défaut
     * 
     * @return array 
     */
    private function _getElements(Request $request, $page)
    {
        $session = $this->get('session');
        $nb_elem = $session->get('nb_elem', 5);

        $filters = $request->request->get('filter');
        $nb_elem = isset($filters['display']) ? $filters['display'] : $nb_elem;
        $nb = $nb_elem;
        if($nb_elem == 'all') {
            $nb_elem = 10000;
            $nb = 'all';
        }    

        //$nb_elem = 10;
        $session->set('nb_elem', $nb_elem);

        //echo $nb_elem; die;

        $em = $this->getDoctrine()->getManager();
        $query = $em->getRepository('CMSContactBundle:Contact')->getAllMessagesQuery();

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query,
            $this->get('request')->query->get('page', $page),
            $nb_elem
        );
        return array('pagination' => $pagination, 'nb' => $nb);
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
        $contact_tab = array(
            'id'      => $contact->getId(), 
            'subject' => $contact->getSubject(), 
            'from'    => htmlentities($contact->getFirstname().' '.$contact->getLastname().' <'.$contact->getSender().'>'), 
            'message' => $contact->getMessage(),
            'statut'  => $contact->getStatut()
            );
        echo json_encode($contact_tab); die;

        return array();

    }

    /**
     * @Route("/admin/contacts/statut/{id}/{statut}", name="contacts_read")
     */
    public function changeStatutAction($id, $statut)
    {
        $contact = $this->getDoctrine()
                        ->getRepository('CMSContactBundle:Contact')
                        ->find($id);
        if (is_object($contact)) {
            $em = $this->getDoctrine()->getEntityManager();
            $contact->setStatut($statut);
            $em->persist($contact);
            $em->flush();

            return $this->redirect($this->generateUrl('contacts_list'));
        }
    }

}
