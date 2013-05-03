<?php

namespace CMS\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

use CMS\BlocBundle\Entity\Bloc;
use CMS\AdminBundle\Classes\BlocDisplay;
use CMS\AdminBundle\Classes\BlocMenuDisplay;
use CMS\AdminBundle\Classes\BlocUserDisplay;

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

        $repository = $this->getDoctrine()
                       ->getEntityManager()
                       ->getRepository('CMSBlocBundle:Bloc');
        $bloc_base = $repository->getBlocBaseAdmin($position);


        if (empty($bloc_base)) {
            $bloc_base = $repository->getBlocBaseDefault($position, 1);
        }
        $html = '';
        foreach ($bloc_base as $key => $bloc_b) {
            $params = json_decode($bloc_b->getParams());
            
            $bloc = $this->getBlocBaseType($params);
            $bloc_display = new BlocDisplay();
           

            $name_class = '\CMS\AdminBundle\Classes\\'.$params->bloc_type.'Display';
            /*if($position == 'admin_menu_top')
                var_dump($params->bloc_type);*/
            $bloc_spec = new $name_class;
            $bloc_spec->setBloc($bloc);
            $options = array();
            //var_dump($params->bloc_type); die;
            switch($params->bloc_type) {
                case 'BlocMenu':
                    $bloc_spec->setRequest($request);

                    $bloc_spec->setUrlIntern($this->getUrlInternNormalized($request));
                    $bloc_spec->setSession($this->get('session'));
                    $options = array('dir' => $bloc->getDisplayType());
                    break;
                case 'BlocBreadcrumb':
                    //$url_courante = $this->generateUrl($url_courante);
                    $bloc_spec->setRepositoryMenu($this->getDoctrine()->getRepository('CMSMenuBundle:Menu'));
                    $bloc_spec->setUrlIntern($request->getPathInfo());
                    $bloc_spec->getOptionsBreadcrumb();
                    break; 
                case 'BlocUser':
                    $options['site_url'] = 'http://chris-local.com/fr/accueil.html';
                    $options['user'] = $this->get('security.context')->getToken()->getUser()->getFirstname().' '.$this->get('security.context')->getToken()->getUser()->getLastname();
                    break;         
            }
            $html .= $bloc_spec->displayBloc($options);

        }

        return $this->render('::module.html.twig', array('html' => $html));
    }

    public function getBlocBaseType($params)
    {
        //var_dump($params->bloc_type); die;
        return $this->getDoctrine()
                     ->getEntityManager()
                     ->getRepository('CMSBlocBundle:'.$params->bloc_type)
                     ->find($params->bloc_id);
    }

    public function getUrlInternNormalized($request) {
        preg_match_all('$\/$', $request->getPathInfo(), $matches);
        $matches = current($matches);
        //var_dump($matches); die;
        if(count($matches) > 2) {
            $pos = strpos($request->getPathInfo(), '/',1);
            $pos++;
            $pos = strpos($request->getPathInfo(), '/', $pos);
            return substr($request->getPathInfo(),0,$pos);
        }
        return $request->getPathInfo();
    }
    
}