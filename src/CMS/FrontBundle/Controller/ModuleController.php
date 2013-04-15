<?php

namespace CMS\FrontBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

use CMS\BlocBundle\Entity\Bloc;

class ModuleController extends Controller
{
    /**
     * @Route("/blocs/generate", name="generatebloc")
     */
    public function generateBlocAction(Request $request, $position, $item_id, $cat_id)
    {

        $repository = $this->getDoctrine()
                       ->getEntityManager()
                       ->getRepository('CMSBlocBundle:Bloc');
        if ($item_id != null) {
            $bloc_base = $repository->getBlocBaseItem($position, $cat_id, $item_id,1);
        } else {
            $bloc_base = $repository->getBlocBaseCategory($position, $cat_id,1);
        }
        if (empty($bloc_base)) {
            $bloc_base = $repository->getBlocBaseDefault($position, 1);
        }

        $html = '';
        foreach ($bloc_base as $key => $bloc_b) {
            $params = json_decode($bloc_b->getParams());
            $bloc = $this->getBlocBaseType($params);
            switch ($params->bloc_type) {
                case 'BlocBreadcrumb':
                    $options = $this->getParametersBlocBreadcrumb($item_id,$cat_id);
                    $html .= $bloc->displayBloc($options);
                    break;
                 default:
                    $html .= $bloc->displayBloc();
            }

        }

        return $this->render('CMSFrontBundle:Modules:module.html.twig', array('html' => $html));
    }

    public function getBlocBaseType($params)
    {
        //var_dump($params->bloc_type); die;
        return $this->getDoctrine()
                     ->getEntityManager()
                     ->getRepository('CMSBlocBundle:'.$params->bloc_type)
                      ->find($params->bloc_id);
    }

    public function getParametersBlocBreadcrumb($item_id,$cat_id)
    {
        $repo = $this->getDoctrine()
                      ->getRepository('CMSMenuBundle:Menu');

        $entry = $repo->getEntryMenu($item_id,$cat_id);

        $entry = current($entry);
        $parent = $entry->getParent();
        while ($parent->getLevel() > 2) {
            $parent = $parent->getParent();

        }
        $path  = $repo->getChildren($parent, true, null, 'desc');
        $path_real = array();
        foreach ($path as $leaf) {
            if($leaf->getId() == $entry->getId())
                $path_real[] = $leaf;
        }
        $path_real[] = $parent;

        $path = array_reverse($path_real);
        $options['entries'] = $path;
        $options['default_url'] = $repo->getDefaultUrl();
        $options['url'] = $entry->getUrl();

        return $options;
    }
}
