<?php

namespace App\Controller;

use App\Entity\Companies;
use App\Entity\Transactions;
use App\Entity\Users;
use App\Entity\AccountEdit;
use App\Repository\CompaniesRepository;
use App\Repository\UsersRepository;
use Container3199tEd\getUserMoneyRepositoryService;
use Container3199tEd\getUsersRepositoryService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;


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

    //User data
    //$userId = $user->getId();
    //
    //$this->getDoctrine()
    //->getRepository(Users::class)
    //->findTransactionsById($userId);
    //
    //$this->getDoctrine()
    //->getRepository(Users::class)
    //->findWalletItemsById($userId);
    //
    //$userTransactions = $user->getUserTransactions();
    //$userWallet = $user->getUserWallets();
    //Company data
    //$company = $company->findCompanyHistoryById(1);
    //$history = $company->getCompanyHistory();

    /**
     * @Route("/stock_index", name="stock_index")
     * @param UserInterface $user
     * @param CompaniesRepository $company
     * @return Response
     */
    public function stockIndex(UserInterface $user, CompaniesRepository $company) : Response {

        $em = $this->getDoctrine()->getManager();
        $company= $em->getRepository(Companies::class)->findAll();

        dump($company);
        return $this->render('main/stock_index.html.twig', [
            'user' => $user,
            'company' => $company,
        ]);
    }

    /**
     * @Route("/actions/{name?}", name="actions")
     * @param UserInterface $user
     * @param CompaniesRepository $companiesRepository
     * @param $name
     * @return Response
     */
    public function actions(UserInterface $user, CompaniesRepository $companiesRepository, $name) : Response {
        $company = $companiesRepository->findOneBy(array('cpnName' => $name));

        return $this->render('main/actions.html.twig', [
            'user' => $user,
            'company' => $company,
        ]);
    }

    /**
     * @Route("/wallet", name="wallet")
     * @param UserInterface $user
     * @param CompaniesRepository $company
     * @return Response
     */
    public function wallet(UserInterface $user, CompaniesRepository $company)  {
        dump($user);

        $userWallet = $user->getUserWallets();

        $em = $this->getDoctrine()->getManager();
        $company= $em->getRepository(Companies::class)->findAll();

        return $this->render('main/wallet.html.twig', [
            'user' => $user,
            'wallet' => $userWallet,
            'company' => $company,
        ]);
    }

    /**
     * @Route("/profile", name="profile")
     * @param Request $request
     * @param UserInterface $user
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @return RedirectResponse|Response
     */
    public function profile(Request $request, UserInterface $user, UserPasswordEncoderInterface $passwordEncoder)  {
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

        $formPassword = $this->createFormBuilder()
        ->add('password', RepeatedType::class, [
            'type' => PasswordType::class,
            'required' => true,
            'first_options' => ['label' => 'Password'],
            'second_options' => ['label' => 'Confirm Password'],
        ])->getForm();

        $formAccount->handleRequest($request);
        if ($formAccount->isSubmitted()) {
            $data = $formAccount->getData();
            $user = new AccountEdit();

            $user->setUsername($data['username']);
            $user->setUseFirstName($data['useFirstName']);
            $user->setUseLastName($data['useLastName']);
            $user->setUseEmail($data['useEmail']);
            $user->setUsePhone($data['usePhone']);

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            return $this->redirect($this->generateUrl('change_account_settings'));
        }

        $formPassword->handleRequest($request);
        if ($formPassword->isSubmitted()) {
            $data = $formPassword->getData();
            $user = new Users();

            $user->setUsername($data['username']);
            $user->setPassword(
                $passwordEncoder->encodePassword($user, $data['password'])
            );

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            return $this->redirect($this->generateUrl('change_password'));
        }

        return $this->render('main/profile.html.twig', [
            'user' => $user,
            'formAccount' => $formAccount->CreateView(),
            'formPassword' => $formPassword->CreateView()
        ]);
    } 

    /**
     * @Route("/change_account_settings", name="change_account_settings")
     */
    public function changeAccountSettings() {
        return "dupA";
    }

    /**
     * @Route("/changePassword", name="change_password")
     */
    public function changePassword() {
        return "duPa";
    }

    /**
     * @Route("/tos", name="tos")
     */
    public function tos() {
        return $this->render('main/tos.html.twig');
    }
}
