<?php
/**
 * Controle un champ de contenu
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

use CMS\ContentBundle\Entity\CMField;
use CMS\ContentBundle\Type\FieldType;

/**
 * Controle un champ de contenu
 *
 * PHP version 5.4
 *
 * @category PHP
 * @package  ContentBundle
 * @author   Damien Corona <leoncorono@gmail.com>
 * @license  http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @version  SVN: 0.1: CMS Symfony
 * @link     https://github.com/damienc38/CMS/blob/master/src/CMS/ContentBundle/Controller/CategoryController.php
 *
 * @Route("/admin")
 */
class FieldController extends Controller
{
    /**
     * Retourne la liste des champs
     * 
     * @return array
     *
     * @Route("/fields/list", name="fields")
     * @Template("CMSContentBundle:ContentManager:fields-list.html.twig")
     */
    public function listAction()
    {
        $fieldslist=$this->_generateListTypeField();
        $fields = $this->getDoctrine()->getRepository('CMSContentBundle:CMField')->findAll();
        $total = count($fields);
        $session = $this->get('session');
        $session->set('active', 'Contenus');

        return array('fields' => $fields, 'total' => $total, 'nb' => 10, 'fieldstype' => $fieldslist);
    }

    /**
     * Retourne la liste des types des champs
     * 
     * @return array
     */
    private function _getFieldsType()
    {
        $path = $this->_getEntityPath();
        $fieldsDirectory = $path.DIRECTORY_SEPARATOR.'Entity'.DIRECTORY_SEPARATOR.'Fields';

        //open my directory
        $myDirectory = opendir($fieldsDirectory);

        // get each entry
        while ($field = readdir($myDirectory)) {
            if ($field != '.' && $field != '..') {
                $field = substr($field, 0, strrpos($field, '.'));
                $fields[] = $field;
            }
        }

        // close directory
        closedir($myDirectory);
        sort($fields);

        return $fields;
    }

    /**
     * Retourne la liste déroulante des types de champ disponible
     * 
     * @return String
     */
    private function _generateListTypeField()
    {
        $fieldsListType=$this->_getFieldsType();
        $fieldsDirectory = $this->_getFieldPath();
        $path = $this->_getEntityPath();

        $html = '<select name="fieldtype" id="fieldtype">';
        foreach ($fieldsListType as $key => $fieldtype) {
            $fieldclass = $fieldsDirectory.$fieldtype;
            $field = new $fieldclass;
            $html .= '<option value="'.$fieldtype.'">'.$field->getName().'</option>';
        }
        $html .= '</select>';

        return $html;
    }

    /**
     * Retourne le chemin vers les entités
     * 
     * @return String
     */
    private function _getEntityPath()
    {
        $dirbase = substr(__DIR__, 0, strrpos(__DIR__, DIRECTORY_SEPARATOR));

        return $dirbase;
    }

    /**
     * Retourne le chemin vers les champs
     * 
     * @return String
     */
    private function _getFieldPath()
    {
        $dirbase = '\CMS\ContentBundle\Entity\Fields\\';

        return $dirbase;
    }

    /**
     * Insère un nouveau champ
     * 
     * @param Request $request Objet Request
     * 
     * @return array
     *
     * @Route("/fields/new", name="fields_new")
     * @Template("CMSContentBundle:ContentManager:fields-item.html.twig")
     */
    public function newItemAction(Request $request)
    {
        $field = new CMField;
        $fieldsOptions = null;
        $fieldtype = $request->query->get('fieldtype');
        if ($fieldtype) {
            $fieldPath = $this->getFieldPath();
            $fieldclass = $fieldPath.$fieldtype;
            $fieldsOptions = new $fieldclass;
            $fieldsOptions = $fieldsOptions->getOptions();
        }
           $form = $this->createForm(new FieldType(), $field);

        if ($request->isMethod('POST')) {
            $form->bind($request);
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $fieldtype = $request->request->get('fieldtype');

                $fieldPath = $this->getFieldPath();
                $fieldclass = $fieldPath.$fieldtype;
                $fieldValue = new $fieldclass;

                $fieldsOptions = $request->request->get('options');

                $fieldValue->setParams($fieldsOptions);

                $created = $this->getDateTimeObject($field->getCreated());
                $field->setCreated($created);
                $field->setField($fieldValue);
                $field->setType($fieldtype);

                foreach ($field->getContentType() as $key => $type) {
                    $type->addField($field);
                    $em->persist($type);
                }

                $em->persist($field);
                $em->flush();

                return $this->redirect($this->generateUrl('fields'));
            }
        }

        return array('form' => $form->createView(), 'fieldsOptions' => $fieldsOptions, 'field' => $field, 'fieldtype' => $fieldtype);
    }

    /**
     * @Route("/fields/edit/{id}", name="fields_edit")
     * @Template("CMSContentBundle:ContentManager:fields-item.html.twig")
     */
    public function editItemAction(Request $request, $id)
    {
        $field = $this->getDoctrine()->getRepository('CMSContentBundle:CMField')->find($id);
        $field = $this->getStringDate($field);
        $fieldsOptions = null;
        if (is_object($field->getField())) {
            $fieldsOptions = $field->getField()->getOptions();
        }
           $form = $this->createForm(new FieldType(), $field);
           $fieldtype = $field->getType();

        if ($request->isMethod('POST')) {
            $old_field = $field;
            $form->bind($request);
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();

                $options = $request->request->get('options');
                $created = $this->getDateTimeObject($field->getCreated());
                $field->setCreated($created);
                $fieldClass = $field->getField();
                if (is_object($fieldClass)) {
                    $fieldClass->setParams($options);
                }

                $field->setField(null);
                foreach ($field->getContentType() as $key => $type) {
                    $em->persist($type);
                }
                $em->persist($field);
                $em->flush();

                $field->setField($fieldClass);
                $em->persist($field);
                $em->flush();

                return $this->redirect($this->generateUrl('fields'));
            }
        }

        return array('form' => $form->createView(), 'fieldsOptions' => $fieldsOptions, 'field' => $field, 'fieldtype' => $fieldtype);
    }

    private function getDateTimeObject($date)
    {
        //input format : M/d/Y
        $date = explode('-', $date);
        if (is_array($date) && count($date)==3) {
            $year = $date[0];
            $month = $date[1];
            $day = $date[2];

            $date = new \DateTime();
            $date->setDate($year,$month,$day);
        } else {
            $date = new \DateTime();
        }

        return $date;
    }

    private function getStringDate($content)
    {
        $datetime = $content->getCreated();
        $datetime = $datetime->format('m/d/Y');

        $content->setCreated($datetime);

        return $content;
    }

    private function getCopyItem($field)
    {
        $copy = new CMField;
        $copy->setTitle($field->getTitle());
        $copy->setName($field->getName());
        $copy->setField($field->getField());
        $copy->setType($field->getType());

        return $copy;
    }

    /**
     * @Route("/fields/copy/{id}", name="fields_copy")
     * @Template("CMSContentBundle:ContentManager:list.html.twig")
     */
    public function copyItemAction(Request $request, $id)
    {
        $field = $this->getDoctrine()->getRepository('CMSContentBundle:CMField')->find($id);
        $copy = $this->getCopyItem($field);

        $em = $this->getDoctrine()->getManager();

           $em->persist($copy);
           $em->flush();

        return $this->redirect($this->generateUrl('fields'));
    }

    /**
     * @Route("/fields/published/{id}", name="fields_published")
     * @Template("CMSContentBundle:ContentManager:list.html.twig")
     */
    public function publishedItemAction(Request $request, $id)
    {
        $field = $this->getDoctrine()->getRepository('CMSContentBundle:CMField')->find($id);

        if($field->getPublished())
            $field->setPublished(0);
        else
            $field->setPublished(1);

        $em = $this->getDoctrine()->getManager();

           $em->persist($field);
           $em->flush();

        return $this->redirect($this->generateUrl('fields'));
    }

    /**
     * @Route("/fields/delete/{id}", name="fields_delete")
     * @Template("CMSContentBundle:ContentManager:list.html.twig")
     */
    public function deleteAction(Request $request, $id)
    {
        $field = $this->getDoctrine()->getRepository('CMSContentBundle:CMField')->find($id);
           $em = $this->getDoctrine()->getManager();

           $em->remove($field);
           $em->flush();

           return $this->redirect($this->generateUrl('fields'));
    }
}
