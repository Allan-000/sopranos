<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Orders;
use App\Entity\Product;
use App\Entity\Size;
use App\Entity\User;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
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
     * @Route("/user/categories")
     */
    public function showCategories(EntityManagerInterface $em){
        $categories = $em->getRepository(Category::class)->findAll();

        return $this->render('categories.html.twig',[
            'categories' => $categories
        ]);
    }

    /**
     * @Route("/category/{id}", methods={"GET","head"})
     * @Route("/user/category/{id}", methods={"GET","head"})
     */
    public function showProducts(int $id, EntityManagerInterface $em){
        $products=$em->getRepository(Product::class)->findBy(array('category' => $id));

        return $this->render('products.html.twig',[
            'products' => $products
        ]);
    }

    /**
     * @Route("/product/{id}"), methods={"GET","head"}
     * @Route("/user/product/{id}"), methods={"GET","head"}
     */
    public function showProduct(int $id,EntityManagerInterface $em,Request $request){

        //get the sizes of the pizza's
        $sizes=$em->getRepository(Size::class)->findAll();

        //get product to display in twig temp
        $product=$em->getRepository(Product::class)->find($id);

        //get data of order
        
        $productPrice=$product->getPrice();
        $user=$this->getUser();
        
        $order=new Orders();


        $form=$this->createFormBuilder($order)
            ->add('amount',NumberType::class , ['attr' => [
                'class'=>'form-control',
                'placeholder' => 'Voer amount in'
            ]])
            ->add('size', ChoiceType::class,
                ['choices' => [
                    'klein' => $sizes[0],
                    'gemiddeld' => $sizes[1],
                    'groot' => $sizes[2]
                ],
                'attr'=>[
                    'class'=>'form-control'
                ]
                ]
            )
            ->add('submit',SubmitType::class, ['attr' => ['class' => 'btn btn-primary d-block m-auto'],'label'=>'toevoegen'])
            ->getForm();

        $form->handleRequest($request);    

        if($form->isSubmitted() && $form->isValid()){
            if( $this->isGranted('ROLE_USER')){
                //here flush data of order into database
                $order->setUser($user);
                $order->setProduct($product);
                $order->setTotalPrice($productPrice);
                $em->persist($order);
                $em->flush();
                return $this->redirectToRoute('shopping_card');
            }
            else{
                $error="<script>alert('Je moet inloggen om bestelling te plaatsen')</script>";
                echo $error;
                return $this->redirectToRoute('register_page');
            }
        }
        
        return $this->render('product.html.twig',[
            'product' => $product,
            'sizes' => $sizes,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/user/shoppingCard", name="shopping_card")
     */
    public function viewShoppingCard(EntityManagerInterface $em){
        $userId=$this->getUser()->getId();
        
        $orders=$em->getRepository(Orders::class)->findBy(array('user' => $userId));
        return $this->render('shoppingCard.html.twig',[
            'orders' => $orders
        ]);
    }

    /**
     * @Route("/register" , name="register_page")
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

            return $this->redirect('login');
        }
        
        return $this->render('register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("login" , name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('login/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }
    /**
     * @Route("logout" , name="app_logout")
     */
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    /**
     * @Route("/user", name="userHome")
     */
    public function userMain(){
        $user=$this->getUser();
        // $userId=$user->getId();
        
        return $this->render('userHome.html.twig',[
            'user' => $user
        ]);
    }
    
}