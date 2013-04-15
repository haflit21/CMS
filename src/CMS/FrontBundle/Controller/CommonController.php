<?php

namespace CMS\FrontBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * @Route("/common")
 */
class CommonController extends Controller
{

    /**
     * @Route("/header")
     * @Template()
     */
    public function headerAction($cat_id,$item_id)
    {
        return array('cat_id' => $cat_id, 'item_id' => $item_id);
    }

    /**
     * @Route("/right")
     * @Template()
     */
    public function rightAction($cat_id,$item_id)
    {
        return array('cat_id' => $cat_id, 'item_id' => $item_id);
    }

    /**
     * @Route("/banner")
     * @Template()
     */
    public function bannerAction($cat_id,$item_id)
    {
        return array('cat_id' => $cat_id, 'item_id' => $item_id);
    }

     /**
     * @Route("/bottom")
     * @Template()
     */
    public function bottomAction($cat_id,$item_id)
    {
        return array('cat_id' => $cat_id, 'item_id' => $item_id);
    }
}
