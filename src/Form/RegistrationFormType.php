<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, ['attr' =>['class' => 'form-control email', 'placeholder' => 'JanKlaassen@gmail.nl']])
            ->add('name' ,TextType::class, ['attr' => ['class' =>'form-control name','placeholder' => 'Voor en achter naam']])
            ->add('post_code', TextType::class, ['attr' =>['class' =>'form-control zip_code','placeholder' => 'Post code']])
            ->add('address' ,TextType::class, ['attr' => ['class' =>'form-control address','placeholder' => 'Julianalaan']])
            ->add('house_number' ,TextType::class, ['attr' => ['class' =>'form-control house_number','placeholder' => '112 a']])
            ->add('plainPassword', PasswordType::class, [
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'mapped' => false,
                'attr' => ['autocomplete' => 'new-password','class' =>'form-control password','placeholder' => 'minimaal 8 karakters en een hoge letter'],
                
                'constraints' => [
                    new NotBlank([
                        'message' => 'Voer een wachtwoord in AUB !',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Uw wachtwoord moet minimaal {{ limit }} karacters zijn',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
