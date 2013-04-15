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
 * @link     https://github.com/damienc38/CMS/blob/master/src/CMS/ContentBundle/Controller/ContentTypeController.php
 */
namespace CMS\ContentBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;

use CMS\ContentBundle\Entity\CMContentType;
use CMS\ContentBundle\Type\ContentTypeType;


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
 *
 * @Route("/admin")
 */
class ContentTypeController extends Controller
{
    /**
     * Retourne la liste des types de contenu
     * 
     * @return array
     *
     * @Route("/contenttypes/list", name="types")
     * @Template("CMSContentBundle:ContentManager:types-list.html.twig")
     */
    public function listAction()
    {
        $types = $this->getDoctrine()->getRepository('CMSContentBundle:CMContentType')->findAll();
        $total = count($types);

        return array('types'=>$types, 'total' => $total, 'nb' => 10);
    }

    /**
     * Insère un nouveau type de contenu
     * 
     * @param Request $request Objet request
     * 
     * @return array
     *
     * @Route("/contenttypes/new", name="types_new")
     * @Template("CMSContentBundle:ContentManager:types-item.html.twig")
     */
    public function newItemAction(Request $request)
    {
        $type = new CMContentType;
        $form = $this->createForm(new ContentTypeType(), $type);

        if ($request->isMethod('POST')) {
            $form->bind($request);
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();

                $em->persist($type);
                $em->flush();

                return $this->redirect($this->generateUrl('types'));
            }
        }

        return array('form' => $form->createView(),'type' => $type);
    }

    /**
     * Mis à jour d'un type de contenu
     * 
     * @param Request $request Objet Request
     * @param int     $id      id du contenu
     * 
     * @return array
     *
     * @Route("/contenttypes/edit/{id}", name="types_edit")
     * @Template("CMSContentBundle:ContentManager:types-item.html.twig")
     */
    public function editItemAction(Request $request, int $id)
    {
        $type = $this->getDoctrine()->getRepository('CMSContentBundle:CMContentType')->find($id);
        $form = $this->createForm(new ContentTypeType(), $type);

        if ($request->isMethod('POST')) {
            $form->bind($request);
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();

                $em->persist($type);
                $em->flush();

                return $this->redirect($this->generateUrl('types'));
            }
        }

        return array('form' => $form->createView(),'type' => $type);
    }

    /**
     * Retourne une copie d'un type de contenu
     * 
     * @param CMContentType $type Type à copier
     * 
     * @return CMContentType
     */
    private function _getCopyItem(CMContentType $type)
    {
        $copy = new CMContentType;
        $copy->setTitle($type->getTitle());

        return $copy;
    }

    /**
     * Insère une copie
     * 
     * @param Request $request Objet de la request
     * @param int     $id      Id du type de contenu à copier
     * 
     * @return null
     *
     * @Route("/contenttypes/copy/{id}", name="types_copy")
     * @Template("CMSContentBundle:ContentManager:list.html.twig")
     */
    public function copyItemAction(Request $request, int $id)
    {
        $type = $this->getDoctrine()->getRepository('CMSContentBundle:CMContentType')->find($id);
        $copy = $this->_getCopyItem($type);

        $em = $this->getDoctrine()->getManager();

        $em->persist($copy);
        $em->flush();

        return $this->redirect($this->generateUrl('types'));
    }

    /**
     * Supprime un contenu
     * 
     * @param Request $request Objet request
     * @param int     $id      id du contenu à supprimer
     * 
     * @return [type]           [description]
     *
     * @Route("/contenttypes/delete/{id}", name="types_delete")
     * @Template("CMSContentBundle:ContentManager:list.html.twig")
     */
    public function deleteAction(Request $request, int $id)
    {
        $type = $this->getDoctrine()->getRepository('CMSContentBundle:CMContentType')->find($id);
        $em = $this->getDoctrine()->getManager();

        $em->remove($type);
        $em->flush();

        return $this->redirect($this->generateUrl('types'));
    }
}
