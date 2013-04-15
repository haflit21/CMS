<?php
/**
 * This sniff prohibits the use of Perl style hash comments.
 *
 * PHP version 5.4
 *
 * @category PHP
 * @package  ContentBundle
 * @author   Damien Corona <leoncorono@gmail.com>
 * @license  http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @version  SVN: 0.1: CMS Symfony
 * @link     https://github.com/damienc38/CMS/blob/master/src/CMS/ContentBundle/Controller/CategoryController.php
 */
namespace CMS\ContentBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;

use CMS\ContentBundle\Entity\CMCategory;
use CMS\ContentBundle\Type\CategoryType;
use Gedmo\Sluggable\Util\Urlizer;

/**
 * Controller de category : gère toutes les actions qui peuvent être faites sur les catégories
 *
 * @category PHP
 * @package  ContentBundle
 * @author   Damien Corona <leoncorono@gmail.com>
 * @license  http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @version  SVN: 0.1: CMS Symfony
 * @link     https://github.com/damienc38/CMS/blob/master/src/CMS/ContentBundle/Controller/CategoryController.php
 * @Route("/admin")
 */

class CategoryController extends Controller
{
    /**
     * Retourne la liste des catégories à afficher
     * 
     * @param Request $request : récupère l'ensemble des paramètres de la requête
     * @param int     $page    : indique le numéro de page courante 
     *
     * @return Tableau contenant la liste des catégories, le nombre, la langue par défaut et les langues utilisées
     *
     * @Route("/categories/list/{page}", name="categories", defaults={"page": 1})
     * @Template("CMSContentBundle:ContentManager:category-list.html.twig")
     */
    public function listCategoriesAction(Request $request, $page)
    {
        $defaultLanguage = $this->_getLanguageDefault();

        if (empty($defaultLanguage)) {
            $this->get('session')->getFlashBag()->add('error', 'No default language exist. Please create one.');

            return array('display'=>false);
        }

        $results = $this->_getElements($request, $page, $defaultLanguage);

        $em = $this->getDoctrine()->getManager();
        $total = $em->getRepository('CMSContentBundle:CMCategory')->getTotalElements($defaultLanguage->getId());

        $languages = $this->_getLanguages();

        return array(
            'pagination' => $results['pagination'],
            'nb' => $results['nb'],
            'total' => $total,
            'defaultLanguage' => $defaultLanguage,
            'languages' => $languages,
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

    private function _getElements(Request $request, $page, $defaultLanguage)
    {
        $session = $this->get('session');
        $nb_elem = $session->get('nb_elem', 5);

        $filters = $request->request->get('filter');
        $nb_elem = isset($filters['display']) ? $filters['display'] : $nb_elem;
        $nb = $nb_elem;
        if($nb_elem == 'all')
            $nb_elem = 10000;

        $session->set('nb_elem', $nb_elem);
        $session->set('active', 'Contenus');

        $em = $this->getDoctrine()->getManager();
        $query = $em->getRepository('CMSContentBundle:CMCategory')->getCategoryByLangIdQuery($defaultLanguage->getId());

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query,
            $this->get('request')->query->get('page', $page),
            $nb_elem
        );

        return array('pagination' => $pagination, 'nb' => $nb);
    }


    /**
     * Récupère la langue par défaut
     *
     * @return retourne la langue par défaut
     */
    private function _getLanguageDefault()
    {
        $language = $this->getDoctrine()->getRepository('CMSContentBundle:CMLanguage')->findBy(array('default_lan'=>'1'));
        $language = current($language);

        return $language;
    }

    /**
     * Récupère toute les langues publiées
     *
     * @return Tableau contenant toutes les langues publiées
     */
    private function _getLanguages()
    {
        $languages = $this->getDoctrine()->getRepository('CMSContentBundle:CMLanguage')->findBy(array('default_lan'=>'0', 'published'=>'1'));

        return $languages;
    }

    /**
     * Insère une nouvelle catégorie
     *
     * @param Request $request : récupère l'ensemble des paramètres de la requête
     * @param int     $lang    : id de la langue
     *
     * @return le formulaire, un objet category vide et la langue
     * @Route("/categories/new/{lang}", name="categories_new")
     * @Template("CMSContentBundle:ContentManager:category-item.html.twig")
     */
    public function newItemAction(Request $request,$lang)
    {
        $category = new CMCategory;
        $language = $this->getDoctrine()->getRepository('CMSContentBundle:CMLanguage')->find($lang);
        $category->setLanguage($language);
        $form = $this->createForm(new CategoryType(), $category, array('lang_id' => $lang));

        if ($request->isMethod('POST')) {

            $form->bind($request);

            if ($form->isValid()) {

                $em = $this->getDoctrine()->getManager();

                $em->persist($category);
                $em->flush();

                return $this->redirect($this->generateUrl('categories'));
            }
        }

        return array('form' => $form->createView(),'category' => $category, 'lang' => $lang);
    }

    /**
     * Insère une nouvelle traduction
     *
     * @param Request    $request   : récupère l'ensemble des paramètres de la requête
     * @param int        $reference : id de la catégorie rde référence
     * @param CMLanguage $lang      : id de la langue de la traduction 
     *
     * @return le formulaire, un objet category vide et la langue
     * @Route("/categories/translation/{reference}/{lang}", name="categories_translation")
     * @Template("CMSContentBundle:ContentManager:category-item.html.twig")
     */
    public function newItemTranslationAction(Request $request, $reference, $lang)
    {
        $category = new CMCategory;
        $language = $this->getDoctrine()->getRepository('CMSContentBundle:CMLanguage')->find($lang);
        $category->setLanguage($language);
        $form = $this->createForm(new CategoryType(), $category);

        if ($request->isMethod('POST')) {
            $form->bind($request);
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();

                $category = $this->getMetas($category);
                $categoryReference = $this->getDoctrine()->getRepository('CMSContentBundle:CMCategory')->find($reference);
                $category->setReferenceCategory($categoryReference);

                $em->persist($category);
                $em->flush();

                return $this->redirect($this->generateUrl('categories'));
            }
        }

        return array('form' => $form->createView(),'category' => $category, 'lang' => $lang, 'referenceCategory'=>$reference);
    }

    /**
     * Met à jour une catégorie
     *
     * @param Request $request : récupère l'ensemble des paramètres de la requête
     * @param int     $id      : id de la catégorie rde référence
     *
     * @return le formulaire, un objet category vide et la langue
     * @Route("/categories/edit/{id}", name="categories_edit")
     * @Template("CMSContentBundle:ContentManager:category-item.html.twig")
     */
    public function editItemAction(Request $request, $id)
    {
        $category = $this->getDoctrine()->getRepository('CMSContentBundle:CMCategory')->find($id);
        $form = $this->createForm(new CategoryType(), $category, array('lang_id' => $category->getLanguage()->getId()));

        if ($request->isMethod('POST')) {
            $form->bind($request);
            if ($form->isValid()) {

                $em = $this->getDoctrine()->getManager();
                $category = $this->getMetas($category);
                $em->persist($category);
                $em->flush();

                return $this->redirect($this->generateUrl('categories'));
            }
        }

        return array('form' => $form->createView(),'category' => $category);
    }

    /**
     * Supprime une catégorie
     *
     * @param int $id : id de la catégorie à supprimer
     *
     * @return vers la liste des catégories
     * @Route("/categories/delete/{id}", name="categories_delete")
     * @Template()
     */
    public function deleteItemAction($id)
    {
        $category = $this->getDoctrine()->getRepository('CMSContentBundle:CMCategory')->find($id);

        $em = $this->getDoctrine()->getManager();
        $em->remove($category);
        $em->flush();

        return $this->redirect($this->generateUrl('categories'));
    }


    /**
     * Récupère toutes les métas d'une catégorie
     *
     * @param CMCategory $category : categorie
     *
     * @return la catégorie avec les métas mises à jour
     */
    private function _getMetas($category)
    {
        if (!$category->getMetatitle()) {
            $title = $category->getTitle();
            $category->setMetatitle($title);
        }
        if (!$category->getMetadescription()) {
            $category->getMetadescription(" ");
        }

        $url = ' ';
        if (!$category->getUrl()) {
            $url = str_replace(' ,\'\\', '-', $category->getTitle());
            $url = strtolower($url);
            $url = \Gedmo\Sluggable\Util\Urlizer::urlize($url);
            $url .= '.html';
            $category->setUrl($url);
        }

        if (!$category->getCanonical()) {
                $category->setCanonical($category->getUrl());
        }

        return $category;
    }
}
