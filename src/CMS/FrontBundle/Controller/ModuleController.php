<?php

namespace CMS\FrontBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

use CMS\BlocBundle\Entity\Bloc;
use CMS\MenuBundle\Entity\Menu;

class ModuleController extends Controller
{
    /**
     * @Route("/blocs/generate", name="generatebloc")
     */
    public function generateBlocAction(Request $request, $position, $item_id, $cat_id)
    {

        $repository = $this->getDoctrine()
                       ->getManager()
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
                     ->getManager()
                     ->getRepository('CMSBlocBundle:'.$params->bloc_type)
                      ->find($params->bloc_id);
    }

    public function getParametersBlocBreadcrumb($item_id,$cat_id)
    {
        $repo = $this->getDoctrine()
                      ->getRepository('CMSMenuBundle:Menu');

        $entry_item = null;              
        if ($item_id != '') {
            $item = $this->getDoctrine()
                         ->getRepository('CMSContentBundle:CMContent')
                         ->find($item_id);              
            $entry_item = new Menu();
            $entry_item->setTitle($item->getTitle());
            $entry_item->setContent($item);             
        }                 

        $entry = $repo->getEntryMenu($item_id,$cat_id);
        
        $parent = '';
        if (is_array($entry)) {
            $entry = current($entry);
            if (is_object($entry)) {
                $parent = $entry->getParent();
                while ($parent->getLevel() > 2) {
                    $parent = $parent->getParent();

                }
            }
        }
        
        $path_real = array();
        
        if($parent != null) {
            $path  = $repo->getChildren($parent, true, null, 'desc');
            foreach ($path as $leaf) {
                if($leaf->getId() == $entry->getId())
                    $path_real[] = $leaf;
            }
            $path_real[] = $parent;
        }

        $path = array_reverse($path_real);
        if($entry_item != null && !in_array($entry_item, $path))
            $path[] = $entry_item;

        $options['entries'] = $path;
        $options['default_url'] = $repo->getDefaultUrl();
        if($entry != null)
            $options['url'] = $entry->getUrl();
        else
            $options['url'] = 'accueil';

        return $options;
    }
}
