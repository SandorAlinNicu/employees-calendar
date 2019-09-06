<?php


namespace App\Controller;


use App\Entity\Holiday;
use App\Entity\Interval;
use App\Entity\User;
use App\Entity\Department;
use App\Form\ActivateUserType;
use App\Form\DeleteUserType;
use App\Form\EditUserType;
use App\Form\EditDepartmentType;

use Knp\Snappy\Pdf;
use App\Form\Type\HolidayStatusChangeType;
use App\Form\Type\DenyHolidayType;
use phpDocumentor\Reflection\Type;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Knp\Bundle\SnappyBundle\Snappy\Response\PdfResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;


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
     * @Route("/user/edit/{id}", name="user_edit")
     * @IsGranted("ROLE_ADMIN")
     */
    public function editAction($id, Request $request)
    {
        $user = $this->getDoctrine()
            ->getRepository(User::class)
            ->find($id);
        $form = $this->createForm(EditUserType::class, [
            'userId' => $id,
            'fullName' => $user->getFullName(),
            'department' => $user->getDepartment(),
            'position' => $user->getPosition(),
            'roles' => $user->getRoles(),
        ]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $user = $em->getRepository(User::class)->findOneBy(['id' => $id]);
            $user->setFullName($form->get('fullName')->getData());
            $user->setDepartment($form->get('department')->getData());
            $user->setPosition($form->get('position')->getData());
            $user->setRoles($form->get('roles')->getData());
            $this->addFlash('success', 'User updated!');
            $em->flush();
            return $this->redirectToRoute('users');
        }

        return $this->render('pages/form_page.html.twig', [
            'form' => $form->createView(),
            'title' => 'Edit user',
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

    /**
     * @Route("/department/edit/{id}", name="department_edit")
     * @IsGranted("ROLE_ADMIN")
     */
    public function editDepartmentAction($id, Request $request)
    {
        $form = $this->createForm(EditDepartmentType::class, [
            'departmentId' => $id
        ]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $department = $em->getRepository(Department::class)->findOneBy(['id' => $id]);
            $department->updateManagers($form->get('managers')->getData()->toArray());
            $department->setName($form->get('name')->getData());
            $em->persist($department);
            $em->flush();
            $this->addFlash('success', 'Department updated!');
            return $this->redirectToRoute('departments');
        }
        return $this->render('pages/form_page.html.twig', [
            'title' => "Edit departments",
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/all-requests", name="requests")
     * @IsGranted({"ROLE_ADMIN", "ROLE_MANAGER"})
     */
    public function requests()
    {

        $requests = $this->getDoctrine()->getRepository(Holiday::class)->findAll();
        $user = $this->getUser();
        $departments = $user->getDepartments()->toArray();
        if ($this->isGranted('ROLE_ADMIN')) {
            return $this->render('requests.html.twig', [
                'title' => 'Available Holiday Requests',
                'requests' => $requests
            ]);
        } else if ($this->isGranted('ROLE_MANAGER')) {
            $filteredRequests = [];
            foreach ($requests as $request) {
                foreach ($departments as $department) {
                    if ($request->getDepartment() == $department) {
                        $filteredRequests[] = $request;
                    }
                }
            };
            return $this->render('requests.html.twig', [
                'title' => 'Available Holiday Requests',
                'requests' => $filteredRequests
            ]);
        }

    }

    /**
     * @Route("/request/{action}/{id}", name="request_action")
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_MANAGER')")
     */
    public function holidayAction($action, $id, Request $request, \Swift_Mailer $mailer)
    {
        $form = $this->createForm(HolidayStatusChangeType::class, [
            'holidayId' => $id
        ]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $holiday = $em->getRepository(Holiday::class)->findOneBy(['id' => $id]);
            if ($action === "approve") {
                $holiday->approve();
            } else {
                $holiday->deny();
            }
            $employeeEmail = $holiday->getEmail();
            $employee = $em->getRepository(User::class)->findOneBy(['email' => $employeeEmail]);
            $employeeDepartment = $employee->getDepartment();
            $managers = $employeeDepartment->getManagers()->toArray();
            //set email to employee and managers
            $email_sender = $_ENV['EMAIL_SENDING_ADDRESS'];
            $emailRecipients = [
                $employee->getEmail(),
            ];
            foreach ($managers as $manager) {
                $emailRecipients[] = $manager->getEmail();
            }
            $subject = "Holiday request #" . $id . " for " . $employee->getFullName();
            $message = (new \Swift_Message($subject))
                ->setFrom($email_sender)
                ->setTo($emailRecipients)
                ->setBody(
                    $this->render('email/holiday_request_approve.html.twig', [
                        'managerName' => $this->getUser()->getFullName(),
                        'action' => $action === 'approve' ? 'approved' : 'denied',
                        'id' => $id,
                        'fullName' => $employee->getFullName(),
                    ]),
                    'text/html'
                );
            $mailer->send($message);

            $em->persist($holiday);
            $em->flush();
            $this->addFlash('success', 'Holiday ' . ($action === 'approve' ? 'approved' : 'denied') . '!');
            return $this->redirectToRoute('requests');

        }
        return $this->render('pages/form_page.html.twig', [
            'title' => "Are you sure you want to " . $action . " this holiday request?",
            'form' => $form->createView(),
        ]);
    }
}