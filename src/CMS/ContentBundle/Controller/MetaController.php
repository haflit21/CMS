<?php
/**
 *
 * PHP version 5.4
 *
 * @category PHP
 * @package  ContentBundle
 * @author   Damien Corona <leoncorono@gmail.com>
 * @license  http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @version  SVN: 0.1: CMS Symfony
 * @link     https://github.com/damienc38/CMS/blob/master/src/CMS/ContentBundle/Controller/MetaController.php
 */
namespace CMS\ContentBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;

use CMS\ContentBundle\Entity\CMMeta;
use CMS\ContentBundle\Type\MetaType;


/**
 * Controller de meta : gère toutes les actions qui peuvent être faites sur les metas
 *
 * @category PHP
 * @package  ContentBundle
 * @author   Damien Corona <leoncorono@gmail.com>
 * @license  http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @version  SVN: 0.1: CMS Symfony
 * @link     https://github.com/damienc38/CMS/blob/master/src/CMS/ContentBundle/Controller/MetaController.php
 * @Route("/admin")
 */
class MetaController extends Controller
{

	/**
      * Retourne tous les types de meta
      * 
      * @param int $page page courante
      * 
      * @return array       tous les types de meta de la page
      * 
      * @Route("/metas/list/{page}", name="metas", defaults={"page": 1})
      * @Template("CMSContentBundle:ContentManager:metas-list.html.twig")
      */
    public function listAction($page)
    {

    	$request = $this->getRequest();
        $results = $this->_getElements($request, $page);

        $total = count($this->getDoctrine()
				            ->getRepository('CMSContentBundle:CMMeta')
				            ->findAll());

        return array(
            'pagination'      => $results['pagination'], 
            'nb'              => $results['nb'], 
            'total'           => $total, 
            'display'         => true, 
            );
    }

    /**
     * Récupère les éléments de la page courante
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

        $session->set('nb_elem', $nb_elem);

        $em = $this->getDoctrine()->getManager();
        $query = $em->getRepository('CMSContentBundle:CMMeta')->getMetaQuery();

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query,
            $this->get('request')->query->get('page', $page),
            $nb_elem
        );
        return array('pagination' => $pagination, 'nb' => $nb);
    }

    /**
     * Insère un nouveau type de meta
     * 
     * @param Request $request objet request
     * 
     * @return [type]           [description]
     *
     *  @Route("/metas/new", name="metas_new")
     * @Template("CMSContentBundle:ContentManager:metas-item.html.twig")
     */
    public function newItemAction(Request $request)
    {
        $meta = new CMMeta;
        $form = $this->createForm(new MetaType(), $meta);

        if ($request->isMethod('POST')) {
            $form->bind($request);
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();

                $em->persist($meta);
                $em->flush();

                $this->get('session')->setFlash('success', 'Le type de meta a bien été sauvegardé');
                return $this->redirect($this->generateUrl('metas'));
            }
        }

        return array(
            'form'        => $form->createView(),
            'meta'     => $meta
        );
    }

    /**
     * Mise à jour du type de meta
     * 
     * @param Request $request Objet request
     * @param int     $id      id de meta à mettre à jour
     * 
     * @return array 
     *
     * @Route("/metas/edit/{id}", name="metas_edit")
     * @Template("CMSContentBundle:ContentManager:metas-item.html.twig")
     */
    public function editItemAction(Request $request, $id)
    {
        $meta = $this->getDoctrine()->getRepository('CMSContentBundle:CMMeta')->find($id);

        $form = $this->createForm(new MetaType(), $meta);

        if ($request->isMethod('POST')) {
            $form->bind($request);
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();

                $em->persist($meta);
                $em->flush();

                $this->get('session')->setFlash('success', 'Le type de meta a bien été sauvegardé');
                
                return $this->redirect($this->generateUrl('metas'));
            }
        }

        return array('form' => $form->createView(),'meta' => $meta, 'id' => $id);
    }

    /**
     * Change le statut d'un type de meta
     * 
     * @param Request $request Objet request
     * @param int     $id      id du type de meta
     * 
     * @return [type]           [description]
     *
     * @Route("/metas/published/{id}", name="metas_published")
     * @Template()
     */
    public function publishedItemAction(Request $request, $id)
    {
		$meta = $this->getDoctrine()->getRepository('CMSContentBundle:CMMeta')->find($id);

		if($meta->getPublished())
		    $meta->setPublished(0);
		else
		    $meta->setPublished(1);

		$em = $this->getDoctrine()->getManager();

		$em->persist($meta);
		$em->flush();

		return $this->redirect($this->generateUrl('metas'));
    }

    /**
     * Supprime le type de meta
     * 
     * @param Request $request Objet request
     * @param int     $id      Type de meta à supprimer
     * 
     * @return null
     *
     * @Route("/metas/delete/{id}", name="metas_delete")
     * @Template()
     */
    public function deleteContentAction(Request $request, $id)
    {
        $meta= $this->getDoctrine()->getRepository('CMSContentBundle:CMMeta')->find($id);
        $em = $this->getDoctrine()->getManager();

        $em->remove($meta);
        $em->flush();

        return $this->redirect($this->generateUrl('metas'));
    }

}