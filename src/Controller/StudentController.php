<?php

namespace App\Controller;

use App\Entity\Classroom;
use App\Entity\Student;
use App\Form\StudentType;
use App\Repository\ClassroomRepository;
use App\Repository\StudentRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Util\PHP\WindowsPhpProcess;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class StudentController extends AbstractController
{
    #[Route('/student', name: 'app_student')]
    public function index(StudentRepository $repo): Response
    {
        $students = $repo->findAll();
        return $this->render('student/index.html.twig', [
            'students' => $students,
        ]);
    }
    // #[Route('/fetch', name: 'fetch')]
    // public function fetch(StudentRepository $repo):Response {
    //     $result=$repo->findAll();
    //     return $this->render('student/test.html.twig',[
    //         'response'=> $result
    //     ]);
    // }
    #[Route('/add', name: 'add')]
    public function add(ManagerRegistry $mr,ClassroomRepository $repo):Response {
        $s=new Student();
        $c=$repo->find(1);
        $s->setName('test');
        $s->setEmail('test@gmail.com');
        $s->setAge('15');
        $s->setClassroom($c);
        

        $em=$mr->getManager();
        $em->persist($s);
        $em->flush();
        return $this->redirectToRoute('app_student');
    }
    #[Route('/addF', name: 'addF',methods:['GET','POST'])]
    public function addF(ManagerRegistry $mr,StudentRepository $repo,Request $req):Response {
        $s=new Student(); // 1- instance
        $form=$this->createForm(StudentType::class,$s);                // 2- Creation form
        $form->handleRequest($req);
        if($form->isSubmitted() && $form->isValid())
        {

           
        $em=$mr->getManager();
        $em->persist($s);
        $em->flush();
         return $this->redirectToRoute('app_student');
    };
        
        return $this->render('student/add.html.twig',[
            'f'=>$form->createView()
        ]);
    }
    #[Route('/remove/{id}', name: 'remove')]
    public function remove(StudentRepository $repo,$id, EntityManagerInterface $em):Response{
        $s=$repo->find($id);
        $em->remove($s);
        $em->flush();
    
        return $this->redirectToRoute('app_student');
    }
    #[Route('/edit/{id}', name: 'edit', methods: ['GET', 'POST'])]
    public function edit(ManagerRegistry $mr, StudentRepository $repo, $id, Request $request): Response {
        // Find the student to edit
        $student = $repo->find($id);
        // Create a form to edit the student
        $form = $this->createForm(StudentType::class, $student);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $mr->getManager();
            $em->flush(); // Update the changes in the database
    
            return $this->redirectToRoute('app_student');
        }
    
        return $this->render('student/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    

}
