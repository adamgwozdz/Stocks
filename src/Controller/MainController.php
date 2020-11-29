<?php

namespace App\Controller;

use App\Entity\Companies;
use App\Entity\History;
use App\Entity\Transactions;
use App\Entity\Users;
use App\Entity\AccountEdit;
use App\Entity\PasswordEdit;
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
        $companyId = $company->getId();
        dump($companyId);

        $this->prepareRandomValues($companyId);

        return $this->render('main/actions.html.twig', [
            'user' => $user,
            'company' => $company,
        ]);
    }

    public function prepareRandomValues($companyId) {
        $em = $this->getDoctrine()->getManager();

        $repository = $em->getRepository(History::class);
        $lastValue = $repository->findBy(array('company' => $companyId),array('id'=>'DESC'),1,0);
        $lastValue = $lastValue[0]->getHisValue();

        $stocksVolume = $repository->findBy(array('company' => $companyId),array('id'=>'ASC'),1,0);
        $stocksVolume = $stocksVolume[0]->getHisVolume();

        $this -> randomValue($lastValue);
        $this -> randomVolume($stocksVolume);
        $this -> randomAction();
    }

    public function randomValue($lastValue) {
        $maxChange = $lastValue / 1000;
        $randomValue = rand(0, $maxChange * 100) / 100;
    }

    public function randomVolume($stocksVolume) {
        $maxStocks = $stocksVolume / 100;
        $randomVolume = (int)(rand(1 * 100, $maxStocks * 100) / 100);
    }

    public function randomAction() {
        $actionFlag = rand(0, 1);
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
     * @Route("/profile/{id}", name="profile")
     * @param Request $request
     * @param UserInterface $user
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @return RedirectResponse|Response
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
