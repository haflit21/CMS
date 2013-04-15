<?php

namespace CMS\BlocBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;

use CMS\BlocBundle\Entity\Bloc;

/**
 * @Route("/admin")
 */
class DefaultController extends Controller
{
    /**
     * Retourne la liste des blocs à afficher dans la page courante
     * 
     * @param Request $request : récupère l'ensemble des paramètres de la requête
     * @param int     $page    : indique le numéro de page courante 
     *
     * @return array
     * 
     * @Route("/blocs/list/{page}", name="blocs_list", defaults={"page": 1})
     * @Template("CMSBlocBundle:Blocs:blocs-list.html.twig")
     */
    public function listAction(Request $request, $page)
    {
        $defaultLanguage = $this->_getLanguageDefault();

        $results = $this->_getElements($request, $page, $defaultLanguage);

        $blocType = $this->_generateListTypeField();

        $em = $this->getDoctrine()->getManager();
        $total = $em->getRepository('CMSBlocBundle:Bloc')->getTotalElements($defaultLanguage->getId());

        $languages = $this->_getLanguages();

        return array(
            'active' => 'Apparence', 
            'defaultLanguage' =>$defaultLanguage, 
            'languages'=>$languages, 
            'pagination' => $results['pagination'],
            'nb' => $results['nb'],
            'total' => $total,
            'blocType'=>$blocType
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
        $session->set('active', 'Apparence');

        $em = $this->getDoctrine()->getManager();
        $query = $em->getRepository('CMSBlocBundle:Bloc')->getBlocByLangIdQuery($defaultLanguage->getId());

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query,
            $this->get('request')->query->get('page', $page),
            $nb_elem
        );

        return array('pagination' => $pagination, 'nb' => $nb);
    }

    private function _generateListTypeField()
    {
        $blocTypes = $this->getDoctrine()->getRepository('CMSBlocBundle:BlocTaxonomy')->findAll();

        $html = '<select name="blocType" id="blocType">';
        foreach ($blocTypes as $key => $type) {
            $html .= '<option value="'.$type->getName().'">'.$type->getName().'</option>';
        }
        $html .= '</select>';

        return $html;
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
     * @Route("/blocs/new/{lang}", name="blocs_new")
     * @Template("CMSBlocBundle:Blocs:blocs-item.html.twig")
     */
    public function newItemAction(Request $request,$lang)
    {
        $session = $this->get('session');
        //if (($session->get('blocType','')=='')) {
            if ($request->isMethod('GET') && $session->get('blocType')!=$request->get('blocType')) {
                $session->set('blocType', $request->get('blocType'));
            }
        //}

        $bloc_popup = $session->get('blocType');

        //echo $bloc_popup; die;
        $blocentity = "CMS\BlocBundle\Entity\\".$bloc_popup;
        $bloctype = "CMS\BlocBundle\Type\\".$bloc_popup.'Type';
        $bloctypeObj = new $bloctype;
        $bloc = new $blocentity;
        $form = $this->createForm($bloctypeObj, $bloc, array('lang_id' => $lang));

        if ($request->isMethod('POST')) {
            $form->bindRequest($request);
            if ($form->isValid()) {
                $data = $form->getData();

                $bloc_base = $bloc->getBloc();
                $bloc_base->setType($bloc->getType());
                $bloc_base->addBloc($bloc);

                //$bloc_base = $this->getOrdre($bloc_base);

                //var_dump($bloc->getBlocs());die();
                $em = $this->getDoctrine()->getEntityManager();
                $language = $this->getDoctrine()->getRepository('CMSContentBundle:CMLanguage')->find($lang);
                $bloc_base->setLanguage($language);
                $em->persist($bloc_base);
                $em->persist($bloc);
                $em->flush();
                $session->set('bloc_type', '');

                $json['bloc_type'] = $bloc->getType();
                $json['bloc_id'] = $bloc->getId();
                $json = json_encode($json);
                $bloc_base->setParams($json);
                $em->persist($bloc_base);
                $em->flush();

                //var_dump($json);die();

                $this->get('session')->setFlash('success', 'New bloc were saved!');

                return $this->redirect($this->generateUrl('blocs_list'));
            }
        }

        return array(
            'form' => $form->createView(), 
            'bloc_type' => $bloc_popup['type'], 
            'lang' => $lang, 
            'active' => 'Apparence'
        );
    }

    /**
     * @Route("/blocs/edit/{id}", name="blocs_edit")
     * @Template("CMSBlocBundle:Blocs:blocs-item.html.twig")
     **/
    public function editAction(Request $request, $id)
    {
        $bloc_base =  $this->getDoctrine()
                            ->getRepository('CMSBlocBundle:Bloc')
                            ->find($id);

        $params = json_decode($bloc_base->getparams());
        $classname = $params->bloc_type;
        $bloctype="CMS\BlocBundle\Type\\".$classname.'Type';
        $bloc_id = $params->bloc_id;
        $bloc = $this->getDoctrine()
                        ->getRepository('CMSBlocBundle:'.$classname)
                        ->find($bloc_id);
        $language = $this->getDoctrine()->getRepository('CMSContentBundle:CMLanguage')->find($bloc_base->getLanguage()->getId());
        $form = $this->createForm(new $bloctype, $bloc, array('lang_id' => $language->getId()));
        if ($request->getMethod() == 'POST') {
            $form->bindRequest($request);
            if ($form->isValid()) {
                $data = $form->getData();
                $bloc_base = $bloc->getBloc();
                $bloc_base->setType($bloc->getType());
                $bloc_base->addBloc($bloc);
                //var_dump($bloc->getBlocs());die();
                $em = $this->getDoctrine()->getEntityManager();
                $em->persist($bloc_base);
                $em->persist($bloc);
                $em->flush();

                $json['bloc_type'] = $bloc->getType();
                $json['bloc_id'] = $bloc->getId();
                $json = json_encode($json);
                $bloc_base->setParams($json);
                $em->persist($bloc_base);
                $em->flush();

                //var_dump($json);die();

                $this->get('session')->setFlash('success', 'New bloc were saved!');

                return $this->redirect($this->generateUrl('blocs_list'));
            }
        }

        return array(
            'form' => $form->createView(), 
            'id' => $id, 
            'bloc_type' => $classname, 
            'lang' => $bloc_base->getLanguage()->getId(),
            'active' => 'Apparence'
        );
    }

    /**
     * @Route("/blocs/delete/{id}", name="blocs_delete")
     * @Template()
     */
    public function deleteAction($id)
    {
        $bloc = $this->getDoctrine()->getRepository('CMSBlocBundle:Bloc')->find($id);
        $em = $this->getDoctrine()->getManager();
        $em->remove($bloc);
        $em->flush();
    }

}
