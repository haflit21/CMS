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

use CMS\ContentBundle\Classes\ExtraMetas;

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
        $defaultLanguage = $this->container->get('cmsontent_bundle.language_controller')->getDefault();

        if (empty($defaultLanguage)) {
            $this->get('session')->getFlashBag()->add('error', 'No default language exist. Please create one.');

            return array('display'=>false);
        }

        $results = $this->_getElements($request, $page, $defaultLanguage);

        $em = $this->getDoctrine()->getManager();
        $total = $em->getRepository('CMSContentBundle:CMCategory')->getTotalElements($defaultLanguage->getId());

        $languages = $this->container->get('cmsontent_bundle.language_controller')->getAll();

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


    private function _getFieldsClassement() {
        $fields = array('id' => 'id', 'title' => 'title');
        $fields_content_res = $this->getDoctrine()->getRepository('CMSContentBundle:CMField')->getAllFieldsArray();
        $fields_content = array();
        foreach ($fields_content_res as $fields_array)
            $fields_content = array_merge($fields_content, array($fields_array['name'] => $fields_array['title']));
        $fields = array_merge($fields,$fields_content);
        return $fields;
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

        $fields = $this->_getFieldsClassement();

        $form = $this->createForm(new CategoryType(), $category, array('lang_id' => $lang,'fields' => $fields));
        
        $metas = ExtraMetas::loadMetas($this);

        if ($request->isMethod('POST')) {

            $form->bind($request);

            if ($form->isValid()) {

                $em = $this->getDoctrine()->getManager();
                
                $em->persist($category);
                $em->flush();

                ExtraMetas::saveMetasCategory($this, $em, $request, $category);
                $session = $this->getRequest()->getSession();
                $session->getFlashBag()->add('success', $this->get('translator')->trans('category_successfully_added'));
                return $this->redirect($this->generateUrl('categories'));
            }
        }

        return array(
            'form'     => $form->createView(),
            'metas'    => $metas,
            'category' => $category, 
            'lang'     => $lang
        );
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
        $categoryReference = $this->getDoctrine()->getRepository('CMSContentBundle:CMCategory')->find($reference);

        $fields = $this->_getFieldsClassement();
        $form = $this->createForm(new CategoryType(), $category, array('lang_id' => $lang,'fields' => $fields));
        
        $metas = ExtraMetas::loadMetas($this);

        if ($request->isMethod('POST')) {
            $form->bind($request);
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();

                $category = $this->_getMetas($category);
                
                $category->setReferenceCategory($categoryReference);

                $em->persist($category);
                $em->flush();

                $session = $this->getRequest()->getSession();
                $session->getFlashBag()->add('success', $this->get('translator')->trans('category_translation_successfully_added'));
                
                return $this->redirect($this->generateUrl('categories'));
            }
        }

        return array(
            'form'               => $form->createView(),
            'category'           => $category,
            'metas'              => $metas, 
            'lang'               => $lang, 
            'referenceCategory'  => $reference, 
            'referenceCatObj'    => $categoryReference
        );
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

        $fields = $this->_getFieldsClassement();

        $form = $this->createForm(new CategoryType(), $category, array('lang_id' => $category->getLanguage()->getId(),'fields' => $fields));
        
        $metas = ExtraMetas::loadEditMetasCategory($category, $this);

        if ($request->isMethod('POST')) {
            $form->bind($request);
            if ($form->isValid()) {

                $em = $this->getDoctrine()->getManager();



                $em->persist($category);
                $em->flush();
                ExtraMetas::updateMetasCategory($this, $em, $request, $category);
                $session = $this->getRequest()->getSession();
                $session->getFlashBag()->add('success', $this->get('translator')->trans('category_successfully_updated'));
                return $this->redirect($this->generateUrl('categories'));
            }
        }

        return array(
            'form'     => $form->createView(),
            'category' => $category, 
            'metas'    => $metas
        );
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
        
        $session = $this->getRequest()->getSession();
        $session->getFlashBag()->add('success', $this->get('translator')->trans('category_successfully_removed'));
        
        return $this->redirect($this->generateUrl('categories'));
    }

    /**
     * Change le statut d'un contenu
     * 
     * @param Request $request Objet request
     * @param int     $id      id du contenu
     * 
     * @return [type]           [description]
     *
     * @Route("/contents/published/{id}", name="categories_published")
     * @Template()
     */
    public function publishedItemAction(Request $request, $id)
    {
        $category = $this->getDoctrine()->getRepository('CMSContentBundle:CMCategory')->find($id);

        if($category->getPublished())
            $category->setPublished(0);
        else
            $category->setPublished(1);

        $em = $this->getDoctrine()->getManager();

        $em->persist($category);
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
            //$url .= '.html';
            $category->setUrl($url);
        }

        if (!$category->getCanonical()) {
                $category->setCanonical($category->getUrl());
        }

        return $category;
    }
}
