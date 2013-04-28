<?php

namespace CMS\FrontBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class DefaultController extends Controller
{

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
        //$url = explode(".", $url);
        //$format = array_pop($url);

        $languages = $this->getDoctrine()
                          ->getRepository('CMSContentBundle:CMLanguage')
                          ->findAll(array('published'=>1));

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
                //var_dump($category->getId()); die;
            }
        } else {
            $contents = $category->getContents();

            if (!empty($contents)) {
                foreach ($category->getContents() as $content_cat) {
                    $currentContent = $content_cat;
                    break;
                }
                $template = $currentContent->getContenttype()->getTemplate().'/category';
                $metas = $this->getMetasCategory($category);
                $title = $category->getTitle();
            } else {
                $template = 'default/category';
            }
        }

        $default_url = $this->getDefaultUrl();
        if ($template == '') {
            $template = 'default/category';
        }

        return array(
            'template' => $template, 
            'format' => $_format, 
            'url_site' => $this->container->getParameter('site_url'), 
            'format_url' => $this->container->getParameter('format_url'), 
            'lang' => $lang,  
            'url' => $this->container->getParameter('site_url').$lang.'/'.$url.$this->container->getParameter('format_url'), 
            'contents' => $contents, 
            'content' => $content, 
            'category' => $category, 
            'metas' => $metas ,
            'title' => $title, 
            'default_url' => '/'.$lang.'/'.$default_url['url'], 
            'languages' => $languages
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
        $str .= '<title>'.$content->getMetaTitle().'</title>';
        $str .= '<meta name="description" content="'.$content->getMetadescription().'" />';
        $str .= '<link rel="canonical" href="'.$content->getCanonical().'" />';

        return $str;
    }

    private function getMetasCategory($category)
    {
        $str = '';
        $str .= '<title>'.$category->getMetaTitle().'</title>';
        $str .= '<meta name="description" content="'.$category->getMetadescription().'" />';
        $str .= '<link rel="canonical" href="'.$category->getCanonical().'" />';

        return $str;
    }
}
