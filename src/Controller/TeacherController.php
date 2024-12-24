<?php

namespace App\Controller;

use App\Entity\Grade;
use App\Entity\Subject;
use App\Repository\GradeRepository;
use App\Repository\SubjectRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TeacherController extends AbstractController
{
    #[Route('/teacher/dashboard', name: 'teacher_dashboard')]
    public function dashboard(SubjectRepository $subjectRepository): Response
    {
        $teacher = $this->getUser();
        if (!$teacher) {
            throw $this->createAccessDeniedException('Access denied.');
        }

        $subjects = $subjectRepository->findBy(['teacher' => $teacher]);

        return $this->render('teacher/dashboard.html.twig', [
            'subjects' => $subjects,
        ]);
    }

    #[Route('/teacher/grade_table', name: 'grade_table')]
    public function accountsTable(): Response
    {
        return $this->render('teacher/grade_table.html.twig');
    }

    #[Route('/teacher/subject/{id}/students', name: 'teacher_subject_students')]
    public function students(Subject $subject): Response
    {
        $teacher = $this->getUser();

        if ($subject->getTeacher() !== $teacher) {
            throw $this->createAccessDeniedException('This is not your subject.');
        }

        return $this->render('teacher/subject_students.html.twig', [
            'subject' => $subject,
            'students' => $subject->getStudents(),
        ]);
    }

    #[Route('/teacher/grade', name: 'teacher_grade_create', methods: ['POST'])]
    public function createGrade(
        Request $request,
        UserRepository $userRepository,
        SubjectRepository $subjectRepository,
        EntityManagerInterface $entityManager
    ): Response {
        $data = json_decode($request->getContent(), true);

        $studentId = $data['studentId'] ?? null;
        $subjectId = $data['subjectId'] ?? null;
        $gradeValue = $data['grade'] ?? null;

        if (!$studentId || !$subjectId || !$gradeValue) {
            return $this->json(['success' => false, 'error' => 'Missing data'], 400);
        }

        $student = $userRepository->find($studentId);
        $subject = $subjectRepository->find($subjectId);

        if (!$student || !$subject) {
            return $this->json(['success' => false, 'error' => 'Invalid student or subject'], 404);
        }

        if ($subject->getTeacher() !== $this->getUser()) {
            throw $this->createAccessDeniedException('You are not the teacher for this subject.');
        }

        $grade = new Grade();
        $grade->setStudent($student)
            ->setSubject($subject)
            ->setGrade((int)$gradeValue)
            ->setCreatedAt(new \DateTimeImmutable());

        $entityManager->persist($grade);
        $entityManager->flush();

        return $this->json(['success' => true, 'gradeId' => $grade->getId()]);
    }

    #[Route('/teacher/subject/{id}/students', name: 'teacher_subject_students')]
    public function students_grade(
        Subject $subject,
        GradeRepository $gradeRepository // Добавляем репозиторий оценок
    ): Response
    {
        $teacher = $this->getUser();
    
        if ($subject->getTeacher() !== $teacher) {
            throw $this->createAccessDeniedException('This is not your subject.');
        }
    
        // Получаем список студентов для данного предмета
        $students = $subject->getStudents();
    
        // Получаем все оценки студентов по данному предмету
        $grades = $gradeRepository->findBy(['subject' => $subject]);
    
        // Создаем массив оценок студентов, чтобы передать их в шаблон
        $studentGrades = [];
        foreach ($students as $student) {
            $studentGrades[$student->getId()] = null; // Инициализируем оценку как null
            foreach ($grades as $grade) {
                if ($grade->getStudent()->getId() === $student->getId()) {
                    $studentGrades[$student->getId()] = $grade->getGrade(); // Присваиваем оценку студенту
                }
            }
        }
    
        return $this->render('teacher/subject_students.html.twig', [
            'subject' => $subject,
            'students' => $students,
            'studentGrades' => $studentGrades, // Убедитесь, что передаете эту переменную
        ]);
    }

}
