<?php

namespace CMS\MenuBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use CMS\MenuBundle\Entity\MenuTaxonomy;
use CMS\MenuBundle\Type\MenuTaxonomyType;

/**
 * @Route("/admin")
 */
class MenuTaxonomyController extends Controller
{
    /**
     * @Route("/menus/list", name="menus")
     * @Template("CMSMenuBundle:Menu:menus-list.html.twig")
     */
    public function listAction()
    {
        $defaultLanguage = $this->getLanguageDefault();
        $menus = $this->getDoctrine()->getRepository('CMSMenuBundle:MenuTaxonomy')->findAll();
        
        $total = count($menus);
        $nb = 10;

        if (empty($menus)) {
            return array('menus' => null);
        }

        $session = $this->get('session');
        $session->set('active', 'Menus');

        return array('menus' => $menus, 'defaultLanguage'=>$defaultLanguage, 'active' => 'Menus', 'total' => $total, 'nb' => $nb);
    }

    private function getLanguageDefault()
    {
        $language = $this->getDoctrine()->getRepository('CMSContentBundle:CMLanguage')->findBy(array('default_lan'=>'1'));
        $language = current($language);

        return $language;
    }

    private function getLanguages()
    {
        $languages = $this->getDoctrine()->getRepository('CMSContentBundle:CMLanguage')->findBy(array('default_lan'=>'0', 'published'=>'1'));

        return $languages;
    }

    /**
     * @Route("/menus/new", name="menus_new")
     * @Template("CMSMenuBundle:Menu:menus-item.html.twig")
     */
    public function newAction(Request $request)
    {
        $menu = new MenuTaxonomy;
        $form = $this->createForm(new MenuTaxonomyType(), $menu);

        if ($request->isMethod('POST')) {
            $form->bind($request);
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $menu->setAlias('');
                $em->persist($menu);
                $em->flush();

                return $this->redirect($this->generateUrl('menus'));
            }
        }

        return array('form' => $form->createView());
    }

    /**
     * @Route("/menus/edit/{id}", name="menus_edit")
     * @Template("CMSMenuBundle:Menu:menus-item.html.twig")
     */
    public function editAction(Request $request, $id)
    {
        $menu = $this->getDoctrine()->getRepository('CMSMenuBundle:MenuTaxonomy')->find($id);
        $form = $this->createForm(new MenuTaxonomyType(), $menu);

        if ($request->isMethod('POST')) {
            $form->bind($request);
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();

                $em->persist($menu);
                $em->flush();

                return $this->redirect($this->generateUrl('menus'));
            }
        }

        return array('form' => $form->createView(), 'menu' => $menu);
    }
}
