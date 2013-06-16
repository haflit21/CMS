<?php

namespace CMS\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

use CMS\BlocBundle\Entity\Bloc;
use CMS\AdminBundle\Classes\BlocDisplay;
use CMS\AdminBundle\Classes\BlocMenuDisplay;
use CMS\AdminBundle\Classes\BlocUserDisplay;

use Symfony\Component\Routing\RequestContext;

/**
 * @Route("/admin")
 */
class ModuleController extends Controller
{
    /**
     * @Route("/blocs/generate", name="generatebloc")
     */
    public function generateBlocAction(Request $request, $position, $url_courante)
    {

        $context = new RequestContext($_SERVER['REQUEST_URI']);
        $repository = $this->getDoctrine()
                       ->getManager()
                       ->getRepository('CMSBlocBundle:Bloc');
        $bloc_base = $repository->getBlocBaseAdmin($position);
        $router = $this->get("router");
        if (empty($bloc_base)) {
            $bloc_base = $repository->getBlocBaseDefault($position, 1);
        }
        $html = '';
        foreach ($bloc_base as $key => $bloc_b) {
            $params = json_decode($bloc_b->getParams());
            
            $bloc = $this->_getBlocBaseType($params);
            $bloc_display = new BlocDisplay();
           

            $name_class = '\CMS\AdminBundle\Classes\\'.$params->bloc_type.'Display';
            $bloc_spec = new $name_class;
            $bloc_spec->setBloc($bloc);
            $options = array();
            switch($params->bloc_type) {
                case 'BlocMenu':
                    $bloc_spec->setRequest($request);
                    
                    $bloc_spec->setUrlIntern($context->getBaseUrl());
                    $bloc_spec->setSession($this->get('session'));
                    $options = array('dir' => $bloc->getDisplayType());
                    break;
                case 'BlocBreadcrumb':
                    $bloc_spec->setRepositoryMenu($this->getDoctrine()->getRepository('CMSMenuBundle:Menu'));
                    $bloc_spec->setUrlIntern($context->getBaseUrl());
                    $bloc_spec->getOptionsBreadcrumb();
                    break; 
                case 'BlocUser':
                    $options['site_url'] = $this->_getSiteUrl();
                    $user = $this->get('security.context')->getToken()->getUser();
                    $options['user'] = $user->getFirstname().' '.$user->getLastname();
                    break;         
            }
            $html .= $bloc_spec->displayBloc($options);

        }

        return $this->render('::module.html.twig', array('html' => $html));
    }

    private function _getBlocBaseType($params)
    {
        return $this->getDoctrine()
                     ->getManager()
                     ->getRepository('CMSBlocBundle:'.$params->bloc_type)
                     ->find($params->bloc_id);
    }

    private function _getSiteUrl() {
        $url_base = $this->container->getParameter("site_url");
        $default = $this->getDoctrine()->getRepository('CMSMenuBundle:Menu')->getDefaultUrl();
        $default = current($default);
        $ds = $this->container->getParameter("directory_separator");
        $language = $this->container->get('cmsontent_bundle.language_controller')->getDefault();
        return $url_base.$language->getCode().$ds.$default;
    }
}