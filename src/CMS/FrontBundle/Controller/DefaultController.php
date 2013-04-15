<?php

namespace CMS\FrontBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class DefaultController extends Controller
{

    /**
     * @Route("/{lang}/{url}.{_format}", name="front", requirements={"lang" = "fr|en|de", "url"=".+", "_format"="html"}, defaults={"url"="accueil", "_format"="html"})
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
            }

        } else {
            $contents = $category->getContents();

            if (!empty($contents)) {
                foreach ($category->getContents() as $content) {
                    $currentContent = $content;
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
        //echo $template; die;
        return array('template' => $template,'contents' => $contents, 'content' => $content, 'category' => $category, 'metas' => $metas ,'title' => $title, 'default_url' => '/'.$lang.'/'.$default_url['url'], 'languages' => $languages);
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
