<?php

namespace CMS\MenuBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use CMS\MenuBundle\Entity\Menu;
use CMS\MenuBundle\Type\MenuType;
use CMS\ContentBundle\Entity\CMLanguage;

/**
 * @Route("/admin")
 */
class MenuController extends Controller
{

    /**
     * Retourne la liste des menus à afficher dans la page courante
     * 
     * @param Request $request : récupère l'ensemble des paramètres de la requête
     * @param int     $page    : indique le numéro de page courante 
     *
     * @return array
     *
     * @Route("/entries/list/{id}/{page}", name="entries_list", defaults={"id": 1, "page": 1})
     * @Template("CMSMenuBundle:Menu:entries-list.html.twig")
     */
    public function listAction(Request $request, $id, $page)
    {
        $defaultLanguage = $this->_getLanguageDefault();
        
        $results = $this->_getElements($request, $page, $defaultLanguage, $id);

        $menu_taxonomy = $this->getDoctrine()->getRepository('CMSMenuBundle:MenuTaxonomy')->find($id);
        
        $em = $this->getDoctrine()->getManager();
        $total = $em->getRepository('CMSMenuBundle:Menu')->getTotalElements($id,$defaultLanguage->getId());

        $languages = $this->_getLanguages();

        return array(
            'pagination' => $results['pagination'],
            'nb' => $results['nb'],
            'total' => $total,
            'defaultLanguage' => $defaultLanguage,
            'languages' => $languages,
            'menu_taxonomy' => $menu_taxonomy,
            'active' => 'Menus'
        );
    }

    /**
     * Récupère la liste des catégories de la langue par défaut
     *
     * @param Request    $request         : récupère l'ensemble des paramètres de la requête
     * @param int        $page            : indique le numéro de page courante 
     * @param CMLanguage $defaultLanguage : Langue par défaut
     *
     * @return Liste des catégories et nombre total de catégories
     */

    private function _getElements(Request $request, $page, $defaultLanguage, $id)
    {
        $session = $this->get('session');
        $nb_elem = $session->get('nb_elem', 5);

        $filters = $request->request->get('filter');
        $nb_elem = isset($filters['display']) ? $filters['display'] : $nb_elem;
        $nb = $nb_elem;
        if($nb_elem == 'all')
            $nb_elem = 10000;

        $session->set('nb_elem', $nb_elem);
        $session->set('active', 'Menus');

        $em = $this->getDoctrine()->getManager();
        $query = $em->getRepository('CMSMenuBundle:Menu')->getEntriesMenuByLangQuery($id,$defaultLanguage->getId());

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query,
            $this->get('request')->query->get('page', $page),
            $nb_elem
        );

        return array('pagination' => $pagination, 'nb' => $nb);
    }

    private function _getLanguageDefault()
    {
        $language = $this->getDoctrine()->getRepository('CMSContentBundle:CMLanguage')->findBy(array('default_lan'=>'1'));
        $language = current($language);

        return $language;
    }

    private function _getLanguages()
    {
        $languages = $this->getDoctrine()->getRepository('CMSContentBundle:CMLanguage')->findBy(array('default_lan'=>'0', 'published'=>'1'));

        return $languages;
    }

    /**
     * @Route("/entries/new/{lang}/{menu_taxonomy}", name="entries_new")
     * @Template("CMSMenuBundle:Menu:entries-item.html.twig")
     */
    public function newAction(Request $request, $lang, $menu_taxonomy)
    {

        $em = $this->getDoctrine()->getManager();
        $entry = new Menu;
        $language = $this->getDoctrine()->getRepository('CMSContentBundle:CMLanguage')->find($lang);
        $entry->setLanguage($language);
        $menu_taxonomy_obj = $this->getDoctrine()->getRepository('CMSMenuBundle:MenuTaxonomy')->find($menu_taxonomy);
        $form = $this->createForm(new MenuType(), $entry, array('lang_id' => $lang, 'menu_taxonomy' => $menu_taxonomy_obj));

        if ($request->isMethod('POST')) {
            $form->bind($request);
            if ($form->isValid()) {

                $menu = $request->request->get('menu');
                $category = $menu['category'];
                $content = $menu['content'];
                $categ_obj = $this->getDoctrine()->getRepository('CMSContentBundle:CMCategory')->find($category);
                $content_obj = $this->getDoctrine()->getRepository('CMSContentBundle:CMContent')->find($content);

                if($content == '')
                    $content = 0;

                $entry->setCategory($categ_obj);
                $entry->setContent($content_obj);
                $entry->setSlug('');

                if ($entry->getOrdre() != null) {
                    $after = $entry->getOrdre();
                    if ($after->getId() != $after->getRoot()) {
                        $repo = $em->getRepository('CMSMenuBundle:Menu');
                        $repo->persistAsNextSiblingOf($entry, $after);
                    } else {
                        $em->persist($entry);
                    }

                } else {
                    $em->persist($entry);

                }

                $em->flush();

                return $this->redirect($this->generateUrl('entries_list', array('id' => $menu_taxonomy_obj->getId(), 'active' => 'Menus')));
            }
        }

        return array(
            'form' => $form->createView(), 
            'lang' => $lang, 
            'menu_taxonomy' => $menu_taxonomy_obj, 
            'active' => 'Menus'
        );
    }

    /**
     * @Route("/entries/edit/{id}", name="entries_edit")
     * @Template("CMSMenuBundle:Menu:entries-item.html.twig")
     */
    public function editAction(Request $request, $id)
    {
        $entry = $this->getDoctrine()->getRepository('CMSMenuBundle:Menu')->find($id);
        $language = $this->getDoctrine()->getRepository('CMSContentBundle:CMLanguage')->find($entry->getLanguage()->getId());
        $entry->setLanguage($language);
        $menu_taxonomy_obj = $entry->getIdMenuTaxonomy();
        $form = $this->createForm(new MenuType(), $entry, array('lang_id' => $language->getId(), 'menu_taxonomy' => $menu_taxonomy_obj));

        if ($request->isMethod('POST')) {
            $form->bind($request);
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();

                $menu = $request->request->get('menu');
                $category = $menu['category'];
                $content = $menu['content'];
                if ($category != '')
                    $categ_obj = $this->getDoctrine()->getRepository('CMSContentBundle:CMCategory')->find($category);
                else
                    $categ_obj = null;

                if ($content != '')
                    $content_obj = $this->getDoctrine()->getRepository('CMSContentBundle:CMContent')->find($content);
                else
                    $content_obj =null;

                $entry->setCategory($categ_obj);
                $entry->setContent($content_obj);
                $em->persist($entry);
                $em->flush();

                return $this->redirect($this->generateUrl('entries_list',array('id' => $entry->getIdMenuTaxonomy()->getId(), 'active' => 'Menus')));
            }
        }

        return array(
            'form' => $form->createView(), 
            'id' => $id, 
            'menu_taxonomy' => $menu_taxonomy_obj,
            'active' => 'Menus'
        );
    }

    /**
     * Insère une nouvelle traduction
     *
     * @param Request    $request   : récupère l'ensemble des paramètres de la requête
     * @param int        $reference : id du menu de référence
     * @param CMLanguage $lang      : id de la langue de la traduction 
     *
     * @return array
     * 
     * @Route("/entries/translation/{reference}/{lang}", name="entries_translation")
     * @Template("CMSMenuBundle:Menu:entries-item.html.twig")
     */
    public function newItemTranslationAction(Request $request, $reference, $lang)
    {
        $menu = new Menu;
        $language = $this->getDoctrine()->getRepository('CMSContentBundle:CMLanguage')->find($lang);
        $menu->setLanguage($language);
        $form = $this->createForm(new MenuType(), $menu);
        $menuReference = $this->getDoctrine()->getRepository('CMSMenuBundle:Menu')->find($reference);

        if ($request->isMethod('POST')) {
            $form->bind($request);
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();

                $menu = $this->getMetas($category);
                
                $menu->setReferenceMenu($menuReference);

                $em->persist($menu);
                $em->flush();

                return $this->redirect($this->generateUrl('entries_list', array('id' => $menuReference->getMenuTaxonomy())));
            }
        }

        return array('form' => $form->createView(),'category' => $category, 'lang' => $lang, 'referenceMenu'=>$reference, 'menu_taxonomy' => $menuReference->getMenuTaxonomy());
    }

    /**
     * @Route("/entries/delete/{id}", name="entries_delete")
     * @Template()
     */
    public function deleteAction($id)
    {
        $entry = $this->getDoctrine()->getRepository('CMSMenuBundle:Menu')->find($id);
        $idtaxonomy = $entry->getMenuTaxonomy()->getId();
        $em = $this->getDoctrine()->getManager();
        $this->getDoctrine()->getRepository('CMSMenuBundle:Menu')->removeFromTree($entry);
        $em->clear();
        return $this->redirect($this->generateUrl('entries_list', array('id' => $idtaxonomy)));
    }

    /**
     * @Route("/entries/move/{id}/{direction}", name="entries_move")
     */
    public function moveOrder($id, $direction)
    {
        $em = $this->getDoctrine()->getRepository('CMSMenuBundle:Menu');
        $entry = $em->find($id);
        switch($direction) {
            case 'UP':
                $em->moveUp($entry);
                break;
            case 'DOWN':
                $em->moveDown($entry);
                break;    
        }
        return $this->redirect($this->generateUrl('entries_list', array('id' => $entry->getMenuTaxonomy()->getId())));
    }
}
