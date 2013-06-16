<?php

namespace CMS\ContentBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;

use CMS\ContentBundle\Entity\CMLanguage;
use CMS\ContentBundle\Type\LanguageType;

/**
 * @Route("/admin", service="cmsontent_bundle.language_controller")
 */
class LanguageController extends Controller
{
    /**
     * @Route("/languages/list", name="languages")
     * @Template("CMSContentBundle:ContentManager:languages-list.html.twig")
     */
    public function listAction()
    {
        $languages = $this->getDoctrine()->getRepository('CMSContentBundle:CMLanguage')->findall();
        $session = $this->get('session');
        $session->set('active', 'Paramètres');

        return array('languages' => $languages, 'active' => 'Parametres');
    }

    private function isDefault($language)
    {
        $default = $this->getDefault();
        if (!empty($default)) {
            if ($language->getId() == $default->getId()) {
                return true;
            } else {
                return false;
            }
        }

        return false;
    }

    private function haveDefault()
    {
        $language = $this->getDoctrine()->getRepository('CMSContentBundle:CMLanguage')->findBy(array('default_lan'=>1));
        if (!empty($language)) {
            return true;
        } else {
            return false;
        }
    }

    public function getDefault()
    {
        $language = $this->getDoctrine()->getRepository('CMSContentBundle:CMLanguage')->findBy(array('default_lan'=>1));

        return current($language);
    }

    public function getAll()
    {
        return $this->getDoctrine()->getRepository('CMSContentBundle:CMLanguage')->findBy(array('default_lan'=>'0', 'published'=>'1'));
    }

   private function manageDefault($language)
   {
       if (!$this->isDefault($language)) {
           if ($default = $this->getDefault()) {
               $default->setDefaultLan(0);

               $em = $this->getDoctrine()->getManager();
               $em->persist($default);
               $em->flush();

               $language->setDefaultLan(1);
               $language->setPublished(1);
           } else {
               $language->setDefaultLan(1);
               $language->setPublished(1);
           }
       }

       return $language;
   }

    /**
     * @Route("/languages/new", name="languages_new")
     * @Template("CMSContentBundle:ContentManager:languages-item.html.twig")
     */
    public function newItemAction(Request $request)
    {
        $language = new CMLanguage;
        $form = $this->createForm(new LanguageType(), $language);

        if ($request->isMethod('POST')) {
            $form->bind($request);
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                if ($language->getDefaultLan()) {
                    $language = $this->manageDefault($language);
                } else {
                    if (!$this->haveDefault()) {
                        $this->get('session')->getFlashBag()->add('info', 'No default language. Please select this one as default');

                        return array('form' => $form->createView(),'language' => $language);
                    }
                }

                $em->persist($language);
                $em->flush();

                return $this->redirect($this->generateUrl('languages'));
            }
        }

        return array('form' => $form->createView(),'language' => $language, 'active' => 'Parametres');
    }

    /**
     * @Route("/languages/edit/{id}", name="languages_edit")
     * @Template("CMSContentBundle:ContentManager:languages-item.html.twig")
     */
    public function editItemAction(Request $request, $id)
    {
        $language = $this->getDoctrine()->getRepository('CMSContentBundle:CMLanguage')->find($id);
        $form = $this->createForm(new LanguageType(), $language);

        if ($request->isMethod('POST')) {
            $form->bind($request);

            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                if ($language->getDefaultLan()) {
                    $language = $this->manageDefault($language);
                } else {
                    if (!$this->haveDefault()) {
                        $this->get('session')->getFlashBag()->add('info', 'No default language. Please select this one as default');

                        return array('form' => $form->createView(),'language' => $language);
                    }
                }

                $em->persist($language);
                $em->flush();

                return $this->redirect($this->generateUrl('languages'));
            }
        }

        return array('form'   => $form->createView(),'language' => $language, 'active' => 'Parametres');
    }

    private function getCopyItem($language)
    {
        $copy = new CMLanguage;
        $copy->setTitle($language->getTitle());
        $copy->setIso($language->getIso());

        return $copy;
    }

    /**
     * @Route("/languages/copy/{id}", name="languages_copy")
     * @Template()
     */
    public function copyItemAction(Request $request, $id)
    {
        $language = $this->getDoctrine()->getRepository('CMSContentBundle:CMLanguage')->find($id);
        $copy = $this->getCopyItem($language);

        $em = $this->getDoctrine()->getManager();

           $em->persist($copy);
           $em->flush();

        return $this->redirect($this->generateUrl('languages'));
    }

    /**
     * @Route("/languages/default/{id}", name="languages_default")
     * @Template()
     */
    public function defaultItemAction(Request $request, $id)
    {
        $language = $this->getDoctrine()->getRepository('CMSContentBundle:CMLanguage')->find($id);
        $default = $this->getDefault();

        if (!$language->getDefaultLan()) {
            if (!empty($default)) {
                $default->setDefaultLan(0);
            }
            $language->setDefaultLan(1);
            $language->setPublished(1);
        } else {
            $this->get('session')->getFlashBag()->add('error', 'You can\'t change state of this element' );

            return $this->redirect($this->generateUrl('languages'));
        }

        $em = $this->getDoctrine()->getManager();

           $em->persist($language);
        if (!empty($default)) {
               $em->persist($default);
        }
           $em->flush();

        return $this->redirect($this->generateUrl('languages'));
    }

    /**
     * @Route("/languages/published/{id}", name="languages_published")
     * @Template()
     */
    public function publishedItemAction(Request $request, $id)
    {
        $language = $this->getDoctrine()->getRepository('CMSContentBundle:CMLanguage')->find($id);

        if (!$language->getDefaultLan()) {
            if($language->getPublished())
                $language->setPublished(0);
            else
                $language->setPublished(1);
        } else {
            $this->get('session')->getFlashBag()->add('error', 'You can\'t change unpublished this element' );

            return $this->redirect($this->generateUrl('languages'));
        }

        $em = $this->getDoctrine()->getManager();

           $em->persist($language);
           $em->flush();

        return $this->redirect($this->generateUrl('languages'));
    }

    /**
     * @Route("/languages/delete/{id}", name="languages_delete")
     * @Template()
     */
    public function deleteAction(Request $request, $id)
    {
        $language = $this->getDoctrine()->getRepository('CMSContentBundle:CMLanguage')->find($id);
           $em = $this->getDoctrine()->getManager();

           if ($this->isDefault($language)) {
            $this->get('session')->getFlashBag()->add('error', 'You can delete the default language');

            return $this->redirect($this->generateUrl('languages'));
        }

           $em->remove($language);
           $em->flush();

           return $this->redirect($this->generateUrl('languages'));
    }

}
