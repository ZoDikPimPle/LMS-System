<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

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
    public function updateUser(
        Request $request,
        UserRepository $userRepository,
        EntityManagerInterface $em
    ): JsonResponse {
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

    #[Route('/admin/add-user', name: 'admin_add_user', methods: ['POST'])]
    public function addUser(
        Request $request,
        UserRepository $userRepository,
        UserPasswordHasherInterface $passwordHasher,
        LoggerInterface $logger
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['email'], $data['password'], $data['role'])) {
            return new JsonResponse(['error' => 'Недостаточно данных для создания пользователя'], 400);
        }

        try {
            // Проверяем, существует ли пользователь с таким email
            $existingUser = $userRepository->findOneBy(['email' => $data['email']]);
            if ($existingUser) {
                return new JsonResponse(['error' => 'Пользователь с таким email уже существует'], 400);
            }

            // Создаём нового пользователя
            $user = new User();
            $user->setEmail($data['email']);
            $user->setRoles([$data['role']]);

            // Хэшируем пароль
            $hashedPassword = $passwordHasher->hashPassword($user, $data['password']);
            $user->setPassword($hashedPassword);

            // Сохраняем пользователя
            $userRepository->save($user, true);

            return new JsonResponse(['message' => 'Пользователь успешно добавлен']);
        } catch (\Exception $e) {
            // Используем внедрённый логгер
            $logger->error('Ошибка при добавлении пользователя: ' . $e->getMessage());

            return new JsonResponse(['error' => 'Ошибка при добавлении пользователя: ' . $e->getMessage()], 500);
        }
    }
}
