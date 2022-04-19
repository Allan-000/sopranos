<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Orders;
use App\Entity\Product;
use App\Entity\User;
use App\Form\RegistrationFormType;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppController extends AbstractController

{
    /**
     * @Route("/" , name="homePage")
     */
    public function homePage()
    {
       return $this->render('home.html.twig');
    }

    /**
     * @Route("/categories")
     */
    public function showCategories(EntityManagerInterface $em){
        $categories = $em->getRepository(Category::class)->findAll();

        return $this->render('categories.html.twig',[
            'categories' => $categories
        ]);
    }

    /**
     * @Route("/category/{id}", methods={"GET","head"})
     */
    public function showProducts(int $id, EntityManagerInterface $em){
        $products=$em->getRepository(Product::class)->findBy(array('category' => $id));

        return $this->render('products.html.twig',[
            'products' => $products
        ]);
    }

    /**
     * @Route("/product/{id}"), methods={"GET","head"}
     */
    public function showProduct(int $id,EntityManagerInterface $em,Request $request){
        $product=$em->getRepository(Product::class)->find($id);
        
        $order=new Orders();

        $form=$this->createFormBuilder($order)
            ->add('amount',NumberType::class , ['attr' => ['class' => 'form-control my-3'],'label'=>'Hoeveel wilt u bestellen :'])
            ->add('submit',SubmitType::class, ['attr' => ['class' => 'btn btn-primary d-block m-auto'],'label'=>'toevoegen'])
            ->getForm();

        $form->handleRequest($request);    
        if($form->isSubmitted() && $form->isValid()){
            return $this->redirectToRoute('homePage');
        }


        return $this->render('product.html.twig',[
            'product' => $product,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/register")
     */
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
            $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $entityManager->persist($user);
            $entityManager->flush();
            // do anything else you need here, like send an email

            return $this->redirect('user');
        }
        
        return $this->render('register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("/user", name="userHome")
     */
    public function userMain(){
        $user=$this->getUser();

        return $this->render('userProfile.html.twig',[
            'user' => $user
        ]);
    }
    
}