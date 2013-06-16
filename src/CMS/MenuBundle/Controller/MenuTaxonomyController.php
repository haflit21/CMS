<?php

namespace CMS\MenuBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use CMS\MenuBundle\Entity\MenuTaxonomy;
use CMS\MenuBundle\Entity\Menu;
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
                $em->persist($menu);
                $em->flush();

                $lang_default = $this->getLanguageDefault();

                $entry = new Menu();
                $entry->setTitle($menu->getName());
                $entry->setSlug($menu->getAlias());
                $entry->setPublished(1);
                $entry->setRoot(true);
                $entry->setIdMenuTaxonomy($menu);
                $entry->setDefaultPage(false);
                $entry->setLanguage($lang_default);
                $entry->setIntern(true);
                $entry->setNameRoute('');
                $entry->setIsRoot(true);
                $entry->setDisplayIcon(false);
                $entry->setDisplayName(false);

                $menu->addMenu($entry);

                $em->persist($entry);
                $em->persist($menu);
                $em->flush();

                $menuAdmin = $this->getDoctrine()
                                  ->getRepository('CMSMenuBundle:MenuTaxonomy')
                                  ->findBy(array('is_menu_admin' => 1));
                $menuAdmin = current($menuAdmin);

                $entryParent = $this->getDoctrine()
                                    ->getRepository('CMSMenuBundle:Menu')
                                    ->findBy(array('slug' => 'menus', 'id_menu_taxonomy' => $menuAdmin));
                                    
                $entryParent = current($entryParent);                    

                $entryMenu = new Menu();
                $entryMenu->setTitle($menu->getName());
                $entryMenu->setSlug($menu->getAlias().'-alias');
                $entryMenu->setPublished(1);
                $entryMenu->setCategory(null);
                $entryMenu->setContent(null);
                $entryMenu->setParent($entryParent);
                $entryMenu->setRoot(false);
                $entryMenu->setIdMenuTaxonomy($menuAdmin);
                $entryMenu->setDefaultPage(false);
                $entryMenu->setLanguage($lang_default);
                $entryMenu->setIntern(true);
                $entryMenu->setNameRoute('/admin/entries/list/'.$menu->getId());
                $entryMenu->setIsRoot(false);
                $entryMenu->setDisplayIcon(false);
                $entryMenu->setDisplayName(true);

                $menuAdmin->addMenu($entryMenu);

                $em->persist($entryMenu);
                $em->persist($menuAdmin);
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


    /**
     * delete a menu and all his entries
     * @param  Request $request Request object
     * @param  Int  $id      id of the menu to delete
     * @return Object redirect to the list of menus
     *
     * @Route("/menus/delete/{id}", name="menus_delete")
     * @Template()
     */
    public function deleteAction(Request $request, $id)
    {
        $menu = $this->getDoctrine()->getRepository('CMSMenuBundle:MenuTaxonomy')->find($id);
        $em = $this->getDoctrine()->getManager();
        $session = $this->getRequest()->getSession();
        if($menu->getId()) {
            
            $entryMenuAdmin = $this->getDoctrine()->getRepository('CMSMenuBundle:Menu')->findBy(array('slug' => $menu->getAlias().'-alias'));
            $entryMenuAdmin = current($entryMenuAdmin);

            $this->getDoctrine()->getRepository('CMSMenuBundle:Menu')->removeFromTree($entryMenuAdmin);

            $em->remove($menu);
            $em->flush();


            $session->getFlashBag()->add('success', 'menu_successfully_removed');
        } else {
            $session->getFlashBag()->add('error', 'menu_does_not_exist');
        }
       
        return $this->redirect($this->generateUrl('menus'));
    }
}
