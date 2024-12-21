<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
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

    #[Route('/admin/disciplines', name: 'admin_disciplines')]
    public function disciplines(): Response
    {
        return $this->render('admin/disciplines.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }
/*
    #[Route('/admin/accounts', name: 'admin_accounts')]
    public function accounts(): Response
    {
        return $this->render('admin/accounts.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }
*/
    #[Route('/admin/accountsTable', name: 'admin_accounts_table')]
    public function accountsTable(): Response
    {
        return $this->render('admin/accountsTable.html.twig');
    }

    #[Route('/admin/users/data', name: 'admin_users_data')]
    public function getUsersData(UserRepository $userRepository): JsonResponse
    {
        $users = $userRepository->findAll();

        $data = array_map(function ($user) {
            return [
                'id' => $user->getId(),
                'email' => $user->getEmail(),
                'roles' => implode(', ', $user->getRoles()), // Преобразуем массив ролей в строку
            ];
        }, $users);

        return new JsonResponse($data);
    }


    #[Route('/admin/users/update', name: 'admin_users_update', methods: ['POST'])]
    public function updateUser(Request $request, UserRepository $userRepository, EntityManagerInterface $em): JsonResponse
    {
    $data = json_decode($request->getContent(), true);
    $id = $data['id'] ?? null;
    $field = $data['field'] ?? null;
    $value = $data['value'] ?? null;

    if (!$id || !$field || !$value) {
        return new JsonResponse(['error' => 'Invalid input'], Response::HTTP_BAD_REQUEST);
    }

    $user = $userRepository->find($id);

    if (!$user) {
        return new JsonResponse(['error' => 'User not found'], Response::HTTP_NOT_FOUND);
    }

    // Обновляем поле
    switch ($field) {
        case 'email':
            $user->setEmail($value);
            break;
        case 'roles':
            $user->setRoles(array_map('trim', explode(',', $value)));
            break;
        default:
            return new JsonResponse(['error' => 'Invalid field'], Response::HTTP_BAD_REQUEST);
    }

    $em->flush();

    return new JsonResponse(['success' => true]);

    }

    
}
