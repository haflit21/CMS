<?php

namespace CMS\FrontBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class DefaultController extends Controller
{


    /**
     * Route to articles with this tag
     * @param  String $tag tag to view
     * @return Array       infos for displaying tag view
     * @Route("/tag/{tag}.{_format}", name="tag", requirements={"_format"="html|xml"}, defaults={"_format"="html", "lang"="fr"})
     * @Template("CMSFrontBundle:Default:index.html.twig")
     */
    public function tagAction($tag,$_format)
    {

        $content = null;
        $template = '';
        $contents = null;
        $currentContent = null;
        $metas = null;
        $title = null;


        $languages = $this->getDoctrine()
                          ->getRepository('CMSContentBundle:CMLanguage')
                          ->findAll(array('published'=>1));

        $lang = $this->getDoctrine()
                          ->getRepository('CMSContentBundle:CMLanguage')
                          ->findOneBy(array('default_lan' => 1));
        $lang = current(explode('_', $lang->getIso()));

        $tag = $this->getDoctrine()->getRepository('CMSContentBundle:CMTag')->findOneBy(array('slug' => $tag));
        $contents = $tag->getContents();
        $template = 'default/category';
        $default_url = $this->getDefaultUrl();

        return array(
            'template'    => $template, 
            'format'      => $_format, 
            'url_site'    => $this->container->getParameter('site_url'), 
            'format_url'  => $this->container->getParameter('format_url'), 
            'lang'        => null,  
            'url'         => $this->container->getParameter('site_url').'/tag/'.$tag.$this->container->getParameter('format_url'), 
            'contents'    => $contents, 
            'content'     => null, 
            'category'    => null, 
            'metas'       => null,
            'title'       => $tag->getTitle(), 
            'default_url' => '/'.$lang.'/'.$default_url['url'], 
            'languages'   => $languages
        );
    }

    /**
     * @Route("/{lang}/{url}.{_format}", name="front", requirements={"lang" = "fr|en|de", "url"="[^\.]+", "_format"="html|xml"}, defaults={"url"="accueil", "_format"="html", "lang"="fr"})
     * @Template()
     */
    public function indexAction($lang,$url,$_format)
    {

        $content = null;
        $template = '';
        $contents = null;
        $currentContent = null;
        $metas = null;
        $title = null;
        $category_id = '';
        $content_id= '';


        $languages = $this->getDoctrine()
                          ->getRepository('CMSContentBundle:CMLanguage')
                          ->findAll(array('published'=>1));

        $langObj = $this->getDoctrine()
                        ->getRepository('CMSContentBundle:CMLanguage')
                        ->findOneBy(array('iso' => $lang.'-'.strtoupper($lang)));
        

        $category = $this->getDoctrine()->getRepository('CMSContentBundle:CMCategory')->findBy(array('url' => $url));
        $category = current($category);

        if (!is_object($category)) {
            $content = $this->getDoctrine()->getRepository('CMSContentBundle:CMContent')->findBy(array('url' => $url));
            $content = current($content);
            if (is_object($content)) {
                $template = $content->getContenttype()->getTemplate().'/item';
                $metas = $this->getMetasContent($content);
                $title = $content->getTitle();
                $categories = $content->getCategories();
                $category = $categories[0];
                $content_id = $content->getId();
            }
        } else {
            $categories = $this->getDoctrine()->getRepository('CMSContentBundle:CMCategory')->findCategoriesByParent($category->getId());
            $category_id = $category->getId();

            $contents = $this->getDoctrine()->getRepository('CMSContentBundle:CMContent')->findByCategories($categories, $langObj->getId(), $category);

            if (!empty($contents)) {
                $currentContent = $contents[0];
                $template = $currentContent->getContenttype()->getTemplate().'/category';
                $title = $category->getTitle();
            } else {
                $template = 'default/category';
            }
            $metas = $this->getMetasCategory($category);
        }

        $default_url = $this->getDefaultUrl();
        if ($template == '') {
            $template = 'default/category';
        }



        return array(
            'template'    => $template, 
            'format'      => $_format, 
            'url_site'    => $this->container->getParameter('site_url'), 
            'format_url'  => $this->container->getParameter('format_url'), 
            'lang'        => $lang,  
            'url'         => $this->container->getParameter('site_url').$lang.'/'.$url.$this->container->getParameter('format_url'), 
            'contents'    => $contents, 
            'content'     => $content,
            'content_id'  => $content_id,
            'category'    => $category, 
            'category_id' => $category_id,
            'metas'       => $metas,
            'title'       => $title, 
            'default_url' => '/'.$lang.'/'.$default_url['url'], 
            'languages'   => $languages
        );
    }

    

    private function getDefaultUrl()
    {
        return $this->getDoctrine()
                    ->getRepository('CMSMenuBundle:Menu')
                    ->getDefaultUrl();

    }

    private function getMetasContent($content)
    {
        $str = '';
        $metas = $this->getDoctrine()->getRepository('CMSContentBundle:CMMeta')->findBy(array('published' => 1));
        foreach ($metas as $meta) {
            $additem = false;
            foreach ($content->getMetavalues() as $metavalue) {
                if ($metavalue->getMeta()->getId() == $meta->getId()) {
                    if(strpos($meta->getValue(), '%s') !== false && $metavalue->getValue() != '')
                        $str .= sprintf($meta->getValue(), $metavalue->getValue());
                }
            }
        }            
        return $str;
    }

    private function getMetasCategory($category)
    {
        $str = '';
        $metas = $this->getDoctrine()->getRepository('CMSContentBundle:CMMeta')->findBy(array('published' => 1));
        foreach ($metas as $meta) {
            $additem = false;
            foreach ($category->getMetavalues() as $metavalue) {
                if ($metavalue->getMeta()->getId() == $meta->getId()) {
                    if(strpos($meta->getValue(), '%s') !== false && $metavalue->getValue() != '')
                        $str .= sprintf($meta->getValue(), $metavalue->getValue());
                }
            }
        }            
        return $str;
    }
}
