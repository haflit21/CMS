<?php

namespace CMS\DashboardBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Symfony\Component\HttpFoundation\Request;

use CMS\DashboardBundle\Entity\Event;
use CMS\DashboardBundle\Form\EventType;

class AgendaController extends Controller
{

    /**
     * @Route("/agenda/new", name="new_event")
     * @Template()
     */
    public function newEventAction(Request $request)
    {
        $event = new Event();
        $form = $this->createForm(new EventType(), $event);
        if ($request->getMethod() == 'POST') {
            $form->bindRequest($request);

            if ($form->isValid()) {

                $em = $this->getDoctrine()->getEntityManager();
                $em->persist($event);
                $em->flush();

                return $this->render('CMSDashboardBundle:Default:index.html.twig',array('form' => $form->createView(), 'month' => $request->request->get('month'), 'year' => $request->request->get('year')));
            }
        }

        return $this->render('CMSDashboardBundle:Default:index.html.twig',array('form' => $form->createView(), 'month' => $request->request->get('month'), 'year' => $request->request->get('year')));
    }

    /**
     * @Route("/agenda/{month}/{year}", name="agenda", defaults={"month"="","year"=""})
     */
    public function indexAction($month="",$year="")
    {
        if ($month == '') {
            $month = date('m');
        }
        if($year == '')
            $year = date('Y');

        $start = $year.'-'.$month.'-01';
        $end = $year.'-'.$month.'-'.date('t',mktime(0,0,0,$month,1,$year));

        $events = $this->getDoctrine()
                       ->getRepository('CMSDashboardBundle:Event')
                       ->findAllByInterval($start,$end);

        $busyDays = array();
        foreach ($events as $event) {
            $start = strftime('%e',$event->getDateDebut()->getTimestamp());
            $end = strftime('%e',$event->getDateFin()->getTimestamp());
            $title = $event->getName();
            for ($i=$start;$i <= $end;$i++) {
                $busyDays['jour'][] = $i;
                $busyDays['title'][(int) $i] = $title;
            }
        }

        return $this->render('CMSDashboardBundle:Agenda:calendar.html.twig',array('month' => $month,'year'=>$year, 'busyDays' => $busyDays));
    }

}
