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


    /**
     * Generate an xml sitemap
     * @return array
     *
     * @Route("/generate/{id}", name="sitemap_generate")
     * @Template()
     */
    public function generateAction($id)
    {
        $path_root = $this->get('kernel')->getRootDir();
        $path_web = $path_root . '/../web' . $this->getRequest()->getBasePath();
        $filename = 'sitemap.xml';
        
        $sitemap = $this->getDoctrine()->getRepository('CMSSitemapBundle:Sitemap')->find($id);

        if (is_object($sitemap)) {
            $handle = fopen($path_root.'/'.$filename, "w");
            if(is_resource($handle)) {
                fwrite($handle, '<?xml version="1.0" encoding="UTF-8"?>');
                fwrite($handle, "\n");
                fwrite($handle, '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">');
                fwrite($handle, "\n");

                $url_base = $this->container->getParameter("site_url");
                $language = $this->container->get('cmsontent_bundle.language_controller')->getDefault();
                
                $menuTaxes = $sitemap->getMenusTaxonomy();
                
                foreach ($menuTaxes as $key => $menuTax) {
                    $entries = $menuTax->getMenus();
                    foreach ($entries as $entry) {
                        $url = $url_base.$language->getCode().'/'.$entry->getUrl();
                        fwrite($handle,"<url>\n");
                        fwrite($handle, "<loc>".$url."</loc>\n");
                        fwrite($handle, "</url>\n");
                    }
                    
                }
                fwrite($handle, "</urlset>");
                fclose($handle);
            } else {
                $session = $this->getRequest()->getSession();
                $session->getFlashBag()->add('error', 'sitemap_not_generated');
            }   
        }
        
        $session = $this->getRequest()->getSession();
        $session->getFlashBag()->add('success', 'sitemap_successfully_generated');
        return $this->redirect($this->generateUrl('sitemap'));
    }
}
