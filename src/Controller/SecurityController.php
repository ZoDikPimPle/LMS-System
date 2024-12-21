<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
    $user = $this->getUser();
    if ($user) {
        if (in_array('ROLE_ADMIN', $user->getRoles())) {
            return $this->redirectToRoute('admin_dashboard'); // Перенаправление для админа
        }
        if (in_array('ROLE_TEACHER', $user->getRoles())) {
            return $this->redirectToRoute('teacher_dashboard'); // Перенаправление для учителя 
        }
        if (in_array('ROLE_STUDENT', $user->getRoles())) {
            return $this->redirectToRoute('student_dashboard'); // Перенаправление для студента
        }
    }

    // Получение ошибки входа
    $error = $authenticationUtils->getLastAuthenticationError();
    $lastUsername = $authenticationUtils->getLastUsername();

    return $this->render('security/login.html.twig', [
        'last_username' => $lastUsername,
        'error' => $error,
    ]);
}


    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    #[Route(path: '/admin', name: 'admin_dashboard')] # - админ панель
    public function adminDashboard(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        return $this->render('admin/dashboard.html.twig');
    }

    #[Route(path: '/teacher', name: 'teacher_dashboard')] # teacher panel
    public function teacherDashboard(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_TEACHER');

        return $this->render('teacher/dashboard.html.twig');
    }

    #[Route(path: '/student', name: 'student_dashboard')] # - student panel
    public function srudentDashboard(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_STUDENT');

        return $this->render('student/dashboard.html.twig');
    }

}
