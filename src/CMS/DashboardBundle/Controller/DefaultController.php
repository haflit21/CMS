<?php

namespace CMS\DashboardBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use CMS\DashboardBundle\Entity\Event;
use CMS\DashboardBundle\Form\EventType;

class DefaultController extends Controller
{
    /**
     * @Route("/dashboard/{month}/{year}",name="dashboard",defaults={"month"="","year"=""})
     * @Template()
     */
    public function indexAction($month = '', $year = '')
    {
        $event = new Event();
        $form = $this->createForm(new EventType(), $event);

        return array('month' => $month, 'year' => $year, 'form' => $form->createView(),'active' => 'Dashboard');
    }
}
