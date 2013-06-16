<?php

namespace CMS\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class DashboardController extends Controller
{

    /**
     * @Route("/", name="dashboard")
     * @Template()
     */
    public function indexAction()
    {

    	if (false === $this->get('security.context')->isGranted('ROLE_ADMIN')) {
        	throw new AccessDeniedException();
    	}
        return array();
    }
}
