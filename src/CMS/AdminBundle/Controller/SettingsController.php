<?php

namespace CMS\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\HttpFoundation\Request;


use CMS\AdminBundle\Entity\Settings;
use CMS\AdminBundle\Form\SettingsType;

class SettingsController extends Controller
{

    /**
     * @Route("/settings", name="settings")
     * @Template()
     */
    public function indexAction(Request $request)
    {

    	$settings = $this->getDoctrine()
    					 ->getRepository('CMSAdminBundle:Settings')
    					 ->findAll();

    	$form = $this->createForm(new SettingsType(), $settings, array('options_fields' => $settings));
    	if ($request->getMethod() == 'POST') {
            $form->bindRequest($request);
            if ($form->isValid()) {
            	$data = $form->getData();
            	
            	$em = $this->getDoctrine()
            			   ->getEntityManager();

            	foreach($settings as $option) {
            		$option->setOptionValue($data[$option->getOptionName()]);
            		$em->persist($option);
            	}
            	$em->flush();
            	
            	$this->get('session')->setFlash('success', 'New user saved!');

                return $this->redirect($this->generateUrl('settings'));
            }
        }

        return array('form' => $form->createView());
    }
}
