<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Product;
use App\Entity\User;
use App\Form\RegistrationFormType;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppController extends AbstractController

{
    /**
     * @Route("/")
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
    public function showProduct(int $id,EntityManagerInterface $em){
        $product=$em->getRepository(Product::class)->find($id);

        return $this->render('userTemplates/product.html.twig',[
            'product' => $product
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

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
    
}