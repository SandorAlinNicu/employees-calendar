<?php


namespace App\Controller;


use App\Entity\User;
use App\Entity\Department;
use App\Form\ActivateUserType;
use App\Form\DeleteUserType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;


class AdminController extends BasicController
{
    /**
     * @Route("/users", name="users")
     * @IsGranted("ROLE_ADMIN")
     */
    public function users()
    {

        $users = $this->getDoctrine()->getRepository(User::class)->findAll();
        return $this->render('users.html.twig', [
            'user' => $users
        ]);
    }

    /**
     * @Route("/user/delete/{id}", name="user_delete")
     * @IsGranted("ROLE_ADMIN")
     */
    public function deleteAction($id, Request $request)
    {
        $form = $this->createForm(DeleteUserType::class, [
            'userId' => $id
        ]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $user = $em->getRepository(User::class)->findOneBy(['id' => $id]);
            $em->remove($user);
            $em->flush();
            $this->addFlash('success', 'User deleted!');
            return $this->redirectToRoute('users');
        }
        return $this->render('pages/form_page.html.twig', [
            'title' => "Are you sure you want to delete this user?",
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/user/activate/{id}", name="user_activate")
     * @IsGranted("ROLE_ADMIN")
     */
    public function activateAction($id, Request $request)
    {

        $form = $this->createForm(ActivateUserType::class, [
            'userId' => $id
        ]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $user = $em->getRepository(User::class)->findOneBy(['id' => $id]);
            $user->setActive(true);
            $em->flush();
            $this->addFlash('success', 'User activated!');
            return $this->redirectToRoute('users');
        }
        return $this->render('pages/form_page.html.twig', [
            'title' => "Are you sure you want to manually activate this user?",
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/departments", name="departments")
     * @IsGranted("ROLE_ADMIN")
     */
    public function departments()
    {

        $departments = $this->getDoctrine()->getRepository(Department::class)->findAll();
        return $this->render('departments.html.twig', [
            'departments' => $departments,
            'title' => 'Departments'
        ]);
    }
}