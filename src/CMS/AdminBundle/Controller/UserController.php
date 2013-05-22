<?php

namespace CMS\AdminBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use CMS\AdminBundle\Entity\User;
use CMS\AdminBundle\Entity\Role;
use CMS\AdminBundle\Form\UserType;
use CMS\AdminBundle\Form\RoleType;

/**
 * @Route("/users")
 */
class UserController extends Controller
{

    /**
     * @Route("/list/{page}", name="users", defaults={"page": 1})
     * @Template()
     **/
    public function indexAction(Request $request, $page)
    {
        $users = $this->getDoctrine()
                ->getRepository('CMSAdminBundle:User')
                ->findAll();

        $results = $this->_getElements($request, $page);      

        if (!$users) {
            return array('users' => null, 'active' => 'Utilisateurs');
        }

        return array(
            'pagination' => $results['pagination'], 
            'nb'         => $results['nb'], 
            'users'      => $users, 
            'active'     => 'Utilisateurs'
        );
    }

    /**
     * Récupère la liste des catégories de la langue par défaut
     *
     * @param Request    $request         : récupère l'ensemble des paramètres de la requête
     * @param int        $page            : indique le numéro de page courante 
     * @param CMLanguage $defaultLanguage : Langue par défaut
     *
     * @return Liste des catégories et nombre total de catégories
     */

    private function _getElements(Request $request, $page)
    {
        $session = $this->get('session');
        $nb_elem = $session->get('nb_elem', 5);

        $filters = $request->request->get('filter');
        $nb_elem = isset($filters['display']) ? $filters['display'] : $nb_elem;
        $nb = $nb_elem;
        if($nb_elem == 'all')
            $nb_elem = 10000;

        $session->set('nb_elem', $nb_elem);

        $em = $this->getDoctrine()->getManager();
        $query = $em->getRepository('CMSAdminBundle:User')->getAllUsersQuery();

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query,
            $this->get('request')->query->get('page', $page),
            $nb_elem
        );

        return array('pagination' => $pagination, 'nb' => $nb);
    }


    /**
     * @Route("/new", name="new_user")
     * @Template()
     **/
    public function newUserAction(Request $request)
    {
        $user = new User();
        $form = $this->createForm(new UserType(), $user);
        if ($request->getMethod() == 'POST') {
            $form->bindRequest($request);
            if ($form->isValid()) {
                $data = $form->getData();
                $factory = $this->get('security.encoder_factory');
                $em = $this->getDoctrine()->getEntityManager();
                $encoder = $factory->getEncoder($user);
                $password = $encoder->encodePassword($data->getPassword(), $user->getSalt());
                $user->setPassword($password);
                $em->persist($user);
                $em->flush();

                $this->get('session')->setFlash('success', 'New user saved!');

                return $this->redirect($this->generateUrl('users'));
            }
        }

        return array('form' => $form->createView(),  'active' => 'Utilisateurs');
    }

    /**
     * @Route("/edit/{id}", name="edit_user")
     * @Template("CMSAdminBundle:User:newUser.html.twig")
     */
    public function editAction(Request $request, $id)
    {
        $user = $this->getDoctrine()
                ->getRepository('CMSAdminBundle:User')
                ->find($id);
        $form = $this->createForm(new UserType(), $user);
        if ($request->getMethod() == 'POST') {
            $form->bindRequest($request);
            if ($form->isValid()) {
                $factory = $this->get('security.encoder_factory');
                 $em = $this->getDoctrine()->getEntityManager();
                
                if (!$user) {
                    throw $this->createNotFoundException('No user found for id '.$id);
                }

                $em->persist($user);

                $em->flush();
                $this->get('session')->setFlash('success', 'New user edited!');

                return $this->redirect($this->generateUrl('users'));
            }
        }

        return array('form' => $form->createView(), 'id' => $id, 'active' => 'Utilisateurs');
    }

    /**
     * @Route("/roles", name="roles")
     * @Template()
     **/
    public function indexRoleAction()
    {
        $roles = $this->getDoctrine()
                ->getRepository('CMSAdminBundle:Role')
                ->findAll();

        if (!$roles) {
            return array('roles' => null);
        }

        return array('roles' => $roles, 'active' => 'Utilisateurs');
    }

    /**
     * @Route("/role/new", name="new_role")
     * @Template()
     */
    public function newRoleAction(Request $request)
    {
        $role = new Role();
        $form = $this->createForm(new RoleType(), $role);
        if ($request->getMethod() == 'POST') {
            $form->bindRequest($request);
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getEntityManager();
                $em->persist($role);

                $em->flush();
                $this->get('session')->setFlash('success', 'A new role has been added');

                return $this->redirect($this->generateUrl('roles'));
            }
        }

        return array('form' => $form->createView(), 'active' => 'Utilisateurs');
    }

    /**
     * @Route("/role/edit/{id}", name="edit_role")
     * @Template("CMSAdminBundle:User:newRole.html.twig")
     */
    public function editRoleFunction($id)
    {
        $role = new Role();
        $form = $this->createForm(new RoleType(), $role);
        if ($request->getMethod() == 'POST') {
            $form->bindRequest($request);
            if ($form->isValid()) {

                $em->persist($role);

                $em->flush();
                $this->get('session')->setFlash('success', 'A new role has been added');

                return $this->redirect($this->generateUrl('roles'));
            }
        }

        return array('form' => $form->createView(), 'active' => 'Utilisateurs');
    }

    /**
     * @Route("/new/init", name="new_user_init")
     * @Template()
     **/
    public function newUserInitAction(Request $request)
    {
        $user = new User();
        $user->setFirstname('Damien');
        $user->setLastname('Corona');
        $user->setEmail('damien.corona@aliceadsl.fr');
        $user->setPath('1111111.jpg');
        $factory = $this->get('security.encoder_factory');
        $em = $this->getDoctrine()->getEntityManager();
        $encoder = $factory->getEncoder($user);
        $password = $encoder->encodePassword('admin', $user->getSalt());
        $user->setPassword($password);

        $em->persist($user);
        $em->flush();

        $this->get('session')->setFlash('success', 'New user saved!');

        return $this->redirect($this->generateUrl('users'));
    }

}
