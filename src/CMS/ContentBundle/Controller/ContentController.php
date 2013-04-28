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
 * @link     https://github.com/damienc38/CMS/blob/master/src/CMS/ContentBundle/Controller/ContentController.php
 */
namespace CMS\ContentBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;

use CMS\ContentBundle\Entity\CMContent;
use CMS\ContentBundle\Entity\CMLanguage;
use CMS\ContentBundle\Entity\CMContentTaxonomy;
use CMS\ContentBundle\Entity\CMFieldValue;
use CMS\ContentBundle\Type\ContentType;

use CMS\ContentBundle\Classes\ExtraFields;

/**
 * Controller de contenu : gère toutes les actions qui peuvent être faites sur les contenus
 *
 * @category PHP
 * @package  ContentBundle
 * @author   Damien Corona <leoncorono@gmail.com>
 * @license  http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @version  SVN: 0.1: CMS Symfony
 * @link     https://github.com/damienc38/CMS/blob/master/src/CMS/ContentBundle/Controller/ContentController.php
 * @Route("/admin")
 */
class ContentController extends Controller
{
     /**
      * Retourne tous les contenus
      * 
      * @param int $page page courante
      * 
      * @return array       tous les contenus de la page
      * 
      * @Route("/contents/list/{page}", name="contents", defaults={"page": 1})
      * @Template("CMSContentBundle:ContentManager:contents-list.html.twig")
      */
    public function listAction($page)
    {
        $defaultLanguage = $this->_getLanguageDefault();

        if (empty($defaultLanguage)) {
            $this->get('session')->getFlashBag()->add('error', 'No default language exist. Please create one.');

            return array('display'=>false);
        }

        $languages = $this->_getLanguages();
        $contentType = $this->_generateListTypeField();

        $request = $this->getRequest();
        $locale = $request->getLocale();

        $results = $this->_getElements($request, $page, $defaultLanguage);

        $total = $this->getDoctrine()
            ->getRepository('CMSContentBundle:CMContent')
            ->getTotalElements($defaultLanguage->getId());

        return array(
            'pagination'      => $results['pagination'], 
            'nb'              => $results['nb'], 
            'total'           => $total, 
            'defaultLanguage' => $defaultLanguage, 
            'languages'       => $languages, 
            'display'         => true, 
            'contentType'     => $contentType
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
    private function _getElements(Request $request, $page, $defaultLanguage)
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
        $query = $em->getRepository('CMSContentBundle:CMContent')->getContentByLangIdQuery($defaultLanguage->getId());

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query,
            $this->get('request')->query->get('page', $page),
            $nb_elem
        );
        return array('pagination' => $pagination, 'nb' => $nb);
    }

    /**
     * récupère la langue par défaut
     * 
     * @return CMLanguage
     */
    private function _getLanguageDefault()
    {
        $language = $this->getDoctrine()->getRepository('CMSContentBundle:CMLanguage')->findBy(array('default_lan'=>'1'));
        $language = current($language);

        return $language;
    }

    /**
     * Récupère toutes les langues
     * 
     * @return array of CMLanguage
     */
    private function _getLanguages()
    {
        $languages = $this->getDoctrine()->getRepository('CMSContentBundle:CMLanguage')->findBy(array('default_lan'=>'0', 'published'=>'1'));

        return $languages;
    }

    /**
     * Affiche tous les types de contenus
     * 
     * @return array of ContentType
     */
    private function _generateListTypeField()
    {
        $contentTypes = $this->getDoctrine()->getRepository('CMSContentBundle:CMContentType')->findAll();

        $html = '<select name="contentType" id="contentType" width="220px">';
        foreach ($contentTypes as $key => $type) {
            $html .= '<option value="'.$type->getId().'">'.$type->getTitle().'</option>';
        }
        $html .= '</select>';

        return $html;
    }

    /**
     * Insère un nouveau contenu
     * 
     * @param Request $request objet request
     * @param int     $lang    id de la langue de l'article
     * 
     * @return [type]           [description]
     *
     *  @Route("/contents/new/{lang}", name="contents_new", defaults={"lang": 1})
     * @Template("CMSContentBundle:ContentManager:contents-item.html.twig")
     */
    public function newItemAction(Request $request, $lang)
    {
        $content = new CMContent;
        $language = $this->getDoctrine()->getRepository('CMSContentBundle:CMLanguage')->find($lang);
        $content->setLanguage($language);
        $form = $this->createForm(new ContentType(), $content, array('lang_id' => $lang));
        $contenttype = $request->query->get('contentType');
        $html = ExtraFields::loadFields($this, $contenttype);
        if ($request->isMethod('POST')) {
            $form->bind($request);
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();

                $created = $this->getDateTimeObject($content->getCreated());
                $content->setCreated($created);

                $content = $this->getMetas($content);

                $contentTaxonomy = new CMContentTaxonomy;
                $contentTaxonomy->addContent($content);
                $em->persist($contentTaxonomy);
                $em->flush();

                $content->setTaxonomy($contentTaxonomy);
                $em->persist($content);
                $em->flush();

                $contenttype = $request->get('contenttype');
                ExtraFields::saveFields($this, $em, $request, $content, $contenttype);
                $em->persist($content);
                $em->flush();
                $this->get('session')->setFlash('success', 'Le contenu a bien été sauvegardé');
                return $this->redirect($this->generateUrl('contents'));
            }
        }

        return array(
            'form'        => $form->createView(),
            'content'     => $content, 
            'lang'        => $lang, 
            'html'        => $html, 
            'contenttype' => $contenttype
        );
    }

    /**
     * [newItemTranslationAction description]
     * 
     * @param Request $request     Objet request
     * @param int     $reference   Contenu de référence
     * @param int     $lang        id de la langue de la traduction
     * @param String  $contenttype Type de l'article
     * 
     * @return array  
     *
     * @Route("/contents/translation/{reference}/{lang}/{contenttype}", name="contents_translation")
     * @Template("CMSContentBundle:ContentManager:contents-item.html.twig")
     */
    public function newItemTranslationAction(Request $request, $reference, $lang, $contenttype)
    {
        $content = new CMContent;
        $language = $this->getDoctrine()->getRepository('CMSContentBundle:CMLanguage')->find($lang);

        $referenceArticle = $this->getDoctrine()->getRepository('CMSContentBundle:CMContent')->find($reference);

        $content->setLanguage($language);
        $form = $this->createForm(new ContentType(), $content, array('lang_id' => $lang));
        $html = ExtraFields::loadFields($this, $contenttype);

        if ($request->isMethod('POST')) {
            $form->bind($request);
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();

                

                $taxonomy = $this->getDoctrine()->getRepository('CMSContentBundle:CMContentTaxonomy')->find($reference);
                $taxonomy->addContent($content);

                $content->setTaxonomy($taxonomy);

                $created = $this->getDateTimeObject($content->getCreated());
                $content->setCreated($created);

                $content = $this->getMetas($content);
                $content->setReferenceContent($referenceArticle);
                $em->persist($content);
                $em->flush();

                $em->persist($taxonomy);
                $contenttype = $request->request->get('contenttype');
                ExtraFields::saveFields($this, $em, $request, $content, $contenttype);
                $em->persist($content);
                $em->flush();

                $this->get('session')->setFlash('success', 'La traduction a bien été sauvegardée');

                return $this->redirect($this->generateUrl('contents'));
            }
        }

        return array(
            'form'             => $form->createView(),
            'content'          => $content, 
            'lang'             => $lang, 
            'referenceContent' => $reference, 
            'referenceArticle' => $referenceArticle, 
            'html'             => $html, 
            'contenttype'      => $contenttype
        );
    }

    /**
     * Mise à jour de l'article
     * 
     * @param Request $request Objet request
     * @param int     $id      id de l'article à mettre à jour
     * 
     * @return array 
     *
     * @Route("/contents/edit/{id}", name="contents_edit")
     * @Template("CMSContentBundle:ContentManager:contents-item.html.twig")
     */
    public function editItemAction(Request $request, $id)
    {
        $content = $this->getDoctrine()->getRepository('CMSContentBundle:CMContent')->find($id);
        $content = $this->_getStringDate($content);
        $html = '';

        foreach ($content->getContentType()->getFields() as $key1 => $field) {
            $display_elem=null;
            foreach ($content->getFieldValues() as $key2 => $value) {
                if ($field->getPublished()) {
                    if ($field->getId() == $value->getField()->getId() && $content->getId() == $value->getContent()->getId()) {
                        $html .= $field->getField()->displayfield($field, $value->getValue());
                        $display_elem=1;
                    }
                }
            }
            if (!$display_elem) {
                if ($field->getPublished()) {
                    $html .= $field->getField()->displayfield($field);
                }
            }

        }

        $form = $this->createForm(new ContentType(), $content, array('lang_id' => $content->getLanguage()->getId()));

        if ($request->isMethod('POST')) {
            $form->bind($request);
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();

                $created = $this->_getDateTimeObject($content->getCreated());
                $content->setCreated($created);
                $content = $this->_getMetas($content);

                $type = $content->getContentType();
                foreach ($type->getFields() as $key => $field) {
                    $additem=null;
                    foreach ($content->getFieldValues() as $key => $fieldvalue) {
                        if ($fieldvalue->getField()->getId() == $field->getId()) {
                            $value = $request->request->get($field->getName());
                            $fieldvalue->setValue($value);
                            $em->persist($fieldvalue);
                            $additem=1;
                        }
                    }
                    if (!$additem) {
                        $value = $request->request->get($field->getName());
                        $fieldvalue = new CMFieldValue;
                        $fieldvalue->setValue($value);
                        $fieldvalue->setContent($content);
                        $fieldvalue->setField($field);
                        $content->addFieldValue($fieldvalue);
                        $field->addFieldValue($fieldvalue);
                        $em->persist($fieldvalue);
                    }
                }

                $em->persist($content);
                $em->flush();

                $this->get('session')->setFlash('success', 'Le contenu a bien été sauvegardé');
                
                return $this->redirect($this->generateUrl('contents'));
            }
        }

        return array('form' => $form->createView(),'content' => $content, 'html'=>$html);
    }

    /**
     * Transforme une date en datetime
     * 
     * @param String $date date à convertir
     * 
     * @return DateTime date une fois convertie
     */
    private function _getDateTimeObject($date)
    {
        //input format : M/d/Y
        $date = explode('-', $date);
        if (is_array($date) && count($date)==3) {
            $year = $date[0];
            $month = $date[1];
            $day = $date[2];

            $date = new \DateTime();
            $date->setDate($year, $month, $day);
        } else {
            $date = new \DateTime();
        }

        return $date;
    }

    /**
     * Met à jour la date de création d'un contenu
     * 
     * @param CMContent $content Contenu à mettre à jour
     * 
     * @return CMContent 
     */
    private function _getStringDate($content)
    {
        $datetime = $content->getCreated();
        $datetime = $datetime->format('m/d/Y');

        $content->setCreated($datetime);

        return $content;
    }

    /**
     * Initialise les metas
     * 
     * @param CMContent $content Contenu à mettre à jour
     * 
     * @return CMContent
     */
    private function _getMetas($content)
    {
        if (!$content->getMetatitle()) {
            $title = $content->getTitle();
            $content->setMetatitle($title);
        }
        if (!$content->getMetadescription()) {
            $content->getMetadescription(" ");
        }
        if (!$content->getUrl()) {
            $url = str_replace(' ,\'\\', '-', $content->getTitle());
            $url = strtolower($url);
            $url = \Gedmo\Sluggable\Util\Urlizer::urlize($url);
            //$url .= '.html';
            $content->setUrl($url);
        }

        if (!$content->getCanonical()) {
            $content->setCanonical($content->getUrl());
        }

        return $content;
    }

    /**
     * Renvoie une copie d'un contenu
     * 
     * @param CMContent $content Contenu à copier
     * 
     * @return CMContent
     */
    private function _getCopyItem($content)
    {
        $copy = new CMContent;
        $copy->setTitle($content->getTitle());
        $copy->setDescription($content->getDescription());

        return $copy;
    }

    /**
     * Insère la copie
     * 
     * @param Request $request Objet request
     * @param int     $id      id de l'item à copier
     * 
     * @return null
     */
    public function copyItemAction(Request $request, $id)
    {
        $content = $this->getDoctrine()->getRepository('CMSContentBundle:CMContent')->find($id);
        $copy = $this->getCopyItem($content);

        $em = $this->getDoctrine()->getManager();

        $created = $this->getDateTimeObject($copy->getCreated());
        $copy->setCreated($created);

        $copy = $this->getMetas($copy);

        $em->persist($copy);
        $em->flush();

        return $this->redirect($this->generateUrl('contents'));
    }

    /**
     * Change le statut d'un contenu
     * 
     * @param Request $request Objet request
     * @param int     $id      id du contenu
     * 
     * @return [type]           [description]
     *
     * @Route("/contents/published/{id}", name="contents_published")
     * @Template()
     */
    public function publishedItemAction(Request $request, $id)
    {
        $content = $this->getDoctrine()->getRepository('CMSContentBundle:CMContent')->find($id);

        if($content->getPublished())
            $content->setPublished(0);
        else
            $content->setPublished(1);

        $em = $this->getDoctrine()->getManager();

           $em->persist($content);
           $em->flush();

        return $this->redirect($this->generateUrl('contents'));
    }

    /**
     * Supprime le contenu
     * 
     * @param Request $request Objet request
     * @param int     $id      Contenu à supprimer
     * 
     * @return null
     *
     * @Route("/contents/delete/{id}", name="contents_delete")
     * @Template()
     */
    public function deleteContentAction(Request $request, $id)
    {
        $content= $this->getDoctrine()->getRepository('CMSContentBundle:CMContent')->find($id);
        $em = $this->getDoctrine()->getManager();

        $em->remove($content);
        $em->flush();

        return $this->redirect($this->generateUrl('contents'));
    }

}
