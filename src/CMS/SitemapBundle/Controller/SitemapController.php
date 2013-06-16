<?php

namespace CMS\SitemapBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use CMS\SitemapBundle\Entity\Sitemap;
use CMS\SitemapBundle\Form\SitemapType;

/**
 * Sitemap controller.
 *
 * @Route("/admin/sitemap")
 */
class SitemapController extends Controller
{

    /**
     * Lists all Sitemap entities.
     *
     * @Route("/list", name="sitemap")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('CMSSitemapBundle:Sitemap')->findAll();

        return array(
            'entities' => $entities,
        );
    }

    /**
     * Insert a new Sitemap
     * @param  Request $request request object
     * @return array            array of objects
     *
     * @Route("/new", name="sitemap_new")
     * @Template()
     */
    public function newAction(Request $request)
    {
        $sitemap = new Sitemap();
        $form = $this->createForm(new SitemapType(), $sitemap);

        if ($request->getMethod() == 'POST') {
            $form->bind($request);
            if ($form->isValid()) {
                $em = $this->getDoctrine()
                           ->getManager();
                $em->persist($sitemap);
                $em->flush();
                $session = $this->getRequest()->getSession();
                $session->getFlashBag()->add('success', 'sitemap_successfully_added');
                return $this->redirect($this->generateUrl('sitemap'));
            }
        }

        return array('form' => $form->createView());           
    }


    /**
     * Edit a Sitemap
     * @param  Request $request request object
     * @param  int     $id      sitemap id to edit
     * @return array            array of objects
     *
     * @Route("/edit/{id}", name="sitemap_edit")
     * @Template("CMSSitemapBundle:Sitemap:new.html.twig")
     */
    public function editAction(Request $request, $id)
    {
        $sitemap = $this->getDoctrine()
                        ->getRepository('CMSSitemapBundle:Sitemap')
                        ->find($id);
        $form = $this->createForm(new SitemapType(), $sitemap);

        if ($request->getMethod() == 'POST') {
            $form->bind($request);
            if ($form->isValid()) {
                $em = $this->getDoctrine()
                           ->getManager();
                $em->persist($sitemap);
                $em->flush();
                $session = $this->getRequest()->getSession();
                $session->getFlashBag()->add('success', 'sitemap_successfully_updated');
                return $this->redirect($this->generateUrl('sitemap'));
            }
        }

        return array(
            'form' => $form->createView(),
            'id' => $id
        );           
    }

    /**
     * Delete a Sitemap
     * @param  int     $id      sitemap id to edit
     * @return array            array of objects
     *
     * @Route("/delete/{id}", name="sitemap_delete")
     * @Template()
     */
    public function deleteAction($id)
    {
        $sitemap = $this->getDoctrine()
                        ->getRepository('CMSMenuBundle:Sitemap')
                        ->find($id);

        $em = $this->getDoctrine()->getManager();
        $em->remove($sitemap);
        $em->flush();
        $session = $this->getRequest()->getSession();
        $session->getFlashBag()->add('success', 'sitemap_successfully_removed');
        return $this->redirect($this->generateUrl('sitemap'));
    }
}
