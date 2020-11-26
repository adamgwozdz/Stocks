<?php

namespace App\Controller;

use App\Entity\Transactions;
use App\Entity\Users;
use App\Entity\AccountEdit;
use App\Entity\PasswordEdit;
use App\Repository\CompaniesRepository;
use App\Repository\UsersRepository;
use Container3199tEd\getUserMoneyRepositoryService;
use Container3199tEd\getUsersRepositoryService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;


class MainController extends AbstractController {
    /**
     * @Route("/", name="check")
     */
    public function index(): Response {
        //Check if user is logged in. If not redirect to login page
        $securityContext = $this->container->get('security.authorization_checker');
        if ($securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->render('main/index.html.twig', [
                'controller_name' => 'MainController',
            ]);

        } else {
            return $this->render('main/index.html.twig');
        }
    }

    /**
     * @Route("/dashboard", name="dashboard")
     * @param UserInterface $user
     * @param CompaniesRepository $company
     * @return Response
     */
    public function dashboard(UserInterface $user, CompaniesRepository $company) : Response {
        // User data
        $userId = $user->getId();

        $this->getDoctrine()
            ->getRepository(Users::class)
            ->findTransactionsById($userId);

        $this->getDoctrine()
            ->getRepository(Users::class)
            ->findWalletItemsById($userId);

        $userTransactions = $user->getUserTransactions();
        $userWallet = $user->getUserWallets();

        //Company data
        $company->findCompanyHistoryById(1);


        dump($company);
        return $this->render('main/stock_index.html.twig', [

            'user' => $user,
            'wallet' => $userWallet,
            'transactions' => $userTransactions,
            'company' => $company,
        ]);
    }

    /**
     * @Route("/actions", name="actions")
     * @param UserInterface $user
     */
    public function actions(UserInterface $user) : Response {
        dump($user);

        return $this->render('main/actions.html.twig', [
            'user' => $user,
        ]);
    } 

    /**
     * @Route("/stock_index", name="stock_index")
     * @param UserInterface $user
     */
    public function indeks(UserInterface $user)  {
        dump($user);

        return $this->render('main/stock_index.html.twig', [
            'user' => $user,
        ]);
    } 

    /**
     * @Route("/wallet", name="wallet")
     * @param UserInterface $user
     */
    public function wallet(UserInterface $user)  {
        dump($user);

        return $this->render('main/wallet.html.twig', [
            'user' => $user,
        ]);
    } 

    /**
     * @Route("/profile/{id}", name="profile")
     * @param UserInterface $user
     */
    public function profile(Request $request, UserInterface $user, UserPasswordEncoderInterface $passwordEncoder, $id)  {
        dump($user);

        $formAccount = $this->createFormBuilder()
            ->add('username', HiddenType::class, [
                'required' => true
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

        $formAccount->handleRequest($request);
        if ($formAccount->isSubmitted()) {
            $data = $formAccount->getData();

            return $this->forward('App\Controller\MainController::changeAccountSettings', [
                'id' => $id, 
                'username' => $data['username'],
                'useFirstName' => $data['useFirstName'],
                'useLastName' => $data['useLastName'],
                'useEmail' => $data['useEmail'],
                //'usePhone' => $data['usePhone']
            ]);
        }

        $formPassword = $this->createFormBuilder()
        ->add('password', RepeatedType::class, [
            'type' => PasswordType::class,
            'required' => true,
            'first_options' => ['label' => 'Password'],
            'second_options' => ['label' => 'Confirm Password'],
        ])->getForm();

        $formPassword->handleRequest($request);
        if ($formPassword->isSubmitted()) {
            $data = $formPassword->getData();

            return $this->forward('App\Controller\MainController::changePassword', [
                'id' => $id, 
                'password' => encodePassword($data['password'])
            ]);
        }

        return $this->render('main/profile.html.twig', [
            'user' => $user,
            'formAccount' => $formAccount->CreateView(),
            'formPassword' => $formPassword->CreateView()
        ]);
    } 

    /**
     * @Route("/edit_account/{id}/{username}/{useFirstName}/{useLastName}/{useEmail}", name="edit_account")
     * Method ({"POST"})
     */
    public function changeAccountSettings($id, $username, $useFirstName, $useLastName, $useEmail) {
            $entityManager = $this->getDoctrine()->getManager();
        
            $user = $entityManager->getRepository(AccountEdit::class)->find($id);

            $user->setUsername($username);
            $user->setUseFirstName($useFirstName);
            $user->setUseLastName($useLastName);
            $user->setUseEmail($useEmail);
           // $user->setUsePhone($usePhone);

            $entityManager->flush();


        return $this->redirectToRoute('profile', array('id' => $id));
    }

    /**
     * @Route("/changePassword/{id}/{password}", name="change_password")
     * * Method ({"POST"})
     */
    public function changePassword($id, $password) {
        $entityManager = $this->getDoctrine()->getManager();

        $user = $entityManager->getRepository(PasswordEdit::class)->find($id);

        $user->setPassword($password);

        $entityManager->flush();


        return $this->redirectToRoute('stock_index');
    }

    /**
     * @Route("/tos", name="tos")
     */
    public function tos() {
        return $this->render('main/tos.html.twig');
    }
}
