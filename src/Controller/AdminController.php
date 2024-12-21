<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AdminController extends AbstractController
{
    #[Route('/admin', name: 'app_admin')]
    public function index(): Response
    {
        return $this->render('admin/dashboard.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }

    #[Route('/admin/disciplines', name: 'admin_disciplines')] #дисциплины
    public function disciplines(): Response
    {
        return $this->render('admin/disciplines.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }

    #[Route('/admin/accounts', name: 'admin_accounts')] #аккаунты
    public function accounts(): Response
    {
        return $this->render('admin/accounts.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }
}


