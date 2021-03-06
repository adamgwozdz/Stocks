<?php

namespace App\Controller;

use App\Entity\UserMoney;
use App\Entity\Users;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class RegistrationController extends AbstractController
{
    /**
     * @Route("/register", name="register")
     * @param Request $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @return Response
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $form = $this->createFormBuilder()
            ->add('username', TextType::class, [
                'required' => true
            ])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'required' => true,
                'first_options' => ['label' => 'Password'],
                'second_options' => ['label' => 'Confirm Password'],
            ])
            ->add('useFirstName', TextType::class, [
                'label' => 'User First Name',
                'required' => true
            ])
            ->add('useLastName', TextType::class, [
                'label' => 'User Last Name',
                'required' => true
            ])
            ->add('useEmail', TextType::class, [
                'label' => 'User e-mail address',
                'required' => true
            ])
            ->add('usePhone', NumberType::class, [
                'label' => 'User Phone Number',
                'required' => true
            ])
            ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            $data = $form->getData();
            $user = new Users();

            $user->setUsername($data['username']);
            $user->setPassword(
                $passwordEncoder->encodePassword($user, $data['password'])
            );
            $user->setUseFirstName($data['useFirstName']);
            $user->setUseLastName($data['useLastName']);
            $user->setUseEmail($data['useEmail']);
            $user->setUsePhone($data['usePhone']);

            $userMoney = new UserMoney();
            $userMoney->setUser($user);
            $userMoney->setUsmAmount(5000);

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->persist($userMoney);
            $em->flush();

            return $this->redirect($this->generateUrl('app_login'));
        }

        return $this->render('registration/registration.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
