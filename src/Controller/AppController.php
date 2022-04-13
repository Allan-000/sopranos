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
       return $this->render('userTemplates/home.html.twig');
    }

    /**
     * @Route("/categories")
     */
    public function showCategories(EntityManagerInterface $em){
        $categories = $em->getRepository(Category::class)->findAll();

        return $this->render('userTemplates/categories.html.twig',[
            'categories' => $categories
        ]);
    }

    /**
     * @Route("/category/{id}", methods={"GET","head"})
     */
    public function showProducts(int $id, EntityManagerInterface $em){
        $products=$em->getRepository(Product::class)->findBy(array('category' => $id));

        return $this->render('userTemplates/products.html.twig',[
            'products' => $products
        ]);
    }

    /**
     * @Route("/product/{id}"), methods={"GET","head"}
     */
    public function showProduct(int $id,EntityManagerInterface $em,Request $request){
        $product=$em->getRepository(Product::class)->find($id);
        
        //try the form out
        // $user=new User();

        // $form=$this->createFormBuilder($user)
        //     ->add('email',TextType::class,array('attr' => array ('class'=>'form-control')))
        //     ->add('submit',SubmitType::class,array('attr' => array('class' =>'btn btn-primary mt-3')))
        //     ->getForm();

        // $form->handleRequest($request);
        
        // if($form->isSubmitted() && $form->isValid()){
        //     return $this->redirectToRoute('homePage');
        // }

        $order=new Orders();

        $form=$this->createFormBuilder($order)
            ->add('amount',NumberType::class , ['attr' => ['class' => 'form-control my-3'],'label'=>'Hoeveel wilt u bestellen :'])
            ->add('submit',SubmitType::class, ['attr' => ['class' => 'btn btn-primary d-block m-auto'],'label'=>'toevoegen'])
            ->getForm();

        $form->handleRequest($request);    
        if($form->isSubmitted() && $form->isValid()){
            return $this->redirectToRoute('homePage');
        }


        return $this->render('userTemplates/product.html.twig',[
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

            return $this->redirectToRoute('app_app_homepage');
        }



        // $user=new User();
        // $form=$this->createFormBuilder($user)
        //     ->add('name' ,TextType::class, ['attr' => ['class' =>'form-control','label' => 'Volledig naam']])
        //     ->add('email',TextType::class, ['attr' => ['class'=>'','label'=>'email adress']])
        //     ->add('gender',TextType::class, ['attr' => ['class' => '' ,'label' =>'Geslacht']])
        //     ->add('password',TextType::class, ['attr' => ['class' => '' ,'label' =>'wachtword']])
        //     ->getForm();
        


        
        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
    
}