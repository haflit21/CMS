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
     * @Route("/list", name="users")
     * @Template()
     **/
    public function indexAction()
    {
        $users = $this->getDoctrine()
                ->getRepository('CMSAdminBundle:User')
                ->findAll();

        if (!$users) {
            return array('users' => null, 'active' => 'Utilisateurs');
        }

        return array('users' => $users, 'active' => 'Utilisateurs');
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
