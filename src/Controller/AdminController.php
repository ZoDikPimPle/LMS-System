<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Subject;
use App\Repository\UserRepository;
use App\Repository\SubjectRepository;
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

   /* #[Route('/admin/disciplines', name: 'admin_disciplines')]
    public function disciplines(): Response
    {
        return $this->render('admin/disciplines.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }*/

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

    #[Route('/admin/users/delete', name: 'admin_users_delete', methods: ['POST'])]
    public function deleteUser(
    Request $request,
    UserRepository $userRepository,
    EntityManagerInterface $em
    ): JsonResponse 
    {
        $data = json_decode($request->getContent(), true);
        $id = $data['id'] ?? null;

        if (!$id) {
            return new JsonResponse(['error' => 'ID пользователя не указан'], Response::HTTP_BAD_REQUEST);
        }

        $user = $userRepository->find($id);

        if (!$user) {
            return new JsonResponse(['error' => 'Пользователь не найден'], Response::HTTP_NOT_FOUND);
        }

        $em->remove($user);
        $em->flush();

        return new JsonResponse(['success' => true]);
    }

    #[Route('/admin/disciplines', name: 'admin_disciplines')]
    public function disciplines(UserRepository $userRepository): Response
    {
        return $this->render('admin/disciplines.html.twig', [
            'teachers' => $userRepository->findByRole('ROLE_TEACHER'),
        ]);

        $teachers = $userRepository->findByRole('ROLE_TEACHER');

        return $this->render('admin/disciplines.html.twig', [
            'teachers' => $teachers, // Передаем список преподавателей в шаблон
        ]);
    }
    
    #[Route('/admin/disciplines/data', name: 'admin_disciplines_data')]
    public function getDisciplinesData(SubjectRepository $subjectRepository): JsonResponse
    {
        $data = array_map(function (Subject $subject) {
            return [
                'id' => $subject->getId(),
                'name' => $subject->getName(),
                'teacher_email' => $subject->getTeacher()->getEmail(),
            ];
        }, $subjectRepository->findAll());
    
        return new JsonResponse($data);
    }
/* Не логично оставлять возможность такого редактирования 
    #[Route('/admin/disciplines/update', name: 'admin_disciplines_update', methods: ['POST'])]
    public function updateDiscipline(
    Request $request,
    SubjectRepository $subjectRepository,
    EntityManagerInterface $em
    ): JsonResponse {
    $data = json_decode($request->getContent(), true);
    $id = $data['id'] ?? null;
    $field = $data['field'] ?? null;
    $value = $data['value'] ?? null;

    if (!$id || !$field || !$value) {
        return new JsonResponse(['error' => 'Invalid input'], Response::HTTP_BAD_REQUEST);
    }

    $discipline = $subjectRepository->find($id);

    if (!$discipline) {
        return new JsonResponse(['error' => 'Discipline not found'], Response::HTTP_NOT_FOUND);
    }

    // Обновляем указанное поле
    switch ($field) {
        case 'name':
            $discipline->setName($value);
            break;
        case 'teacher_email':
            if (filter_var($value, FILTER_VALIDATE_EMAIL)) {
                $discipline->getTeacher()->setEmail($value);
            } else {
                return new JsonResponse(['error' => 'Invalid email format'], Response::HTTP_BAD_REQUEST);
            }
            break;
        default:
            return new JsonResponse(['error' => 'Invalid field'], Response::HTTP_BAD_REQUEST);
    }

    $em->flush();

    return new JsonResponse(['success' => true]);
}*/ 
    
    #[Route('/admin/disciplines/add', name: 'admin_disciplines_add', methods: ['POST'])]
    public function addDiscipline(Request $request, UserRepository $userRepository, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
    
        $teacher = $userRepository->find($data['teacher']);
        if (!$teacher) {
            return new JsonResponse(['error' => 'Преподаватель не найден'], 404);
        }
    
        $subject = new Subject();
        $subject->setName($data['name']);
        $subject->setTeacher($teacher);
    
        $em->persist($subject);
        $em->flush();
    
        return new JsonResponse(['success' => true]);
    }
    
    
}
