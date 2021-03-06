<?php

namespace App\Controller;

use App\Entity\Companies;
use App\Entity\History;
use App\Entity\Transactions;
use App\Entity\Users;
use App\Entity\Wallet;
use App\Entity\AccountEdit;
use App\Entity\PasswordEdit;
use App\Repository\CompaniesRepository;
use App\Repository\HistoryRepository;
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
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\Cache\Adapter\PdoAdapter;

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
     * @Route("/stock_index", name="stock_index")
     * @param UserInterface $user
     * @param CompaniesRepository $companiesRepository
     * @return Response
     */
    public function stockIndex(UserInterface $user, CompaniesRepository $companiesRepository): Response {
        $company = $companiesRepository->findAll();

        return $this->render('main/stock_index.html.twig', [
            'user' => $user,
            'company' => $company,
            'alert' => " "
        ]);
    }
    
    // ========== Sprzedaż wszystkich akcji spółki i usunięcie jej z portfela ========== //

    /**
     * @Route("/wallet/deleteStock", name="deleteStock")
     * Method ({"POST"})
     * @param UserInterface $user
     * @param Request $request
     * @return Response
     */
    public function deleteStock(UserInterface $user, Request $request) : Response {
        $em = $this->getDoctrine()->getManager();
        $comp_id = $request->get('comp_id');
        $user_id = $request->get('user_id');

        $procedure = "CALL sellAllStocks(:userid, :companyid);";
        $params['userid'] = $user_id;
        $params['companyid'] = $comp_id;
        $stmt = $em->getConnection()->prepare($procedure);
        $stmt->execute($params);

        $userWallet = $user->getUserWallets();
        $company = $em->getRepository(Companies::class)->findAll();

        return $this->render('main/wallet.html.twig', [
            'user' => $user,
            'wallet' => $userWallet,
            'company' => $company,
        ]);
    }

    // ================================================================================== //

    /**
     * @Route("/modifyStocksAmount", name="modifyStocksAmount")
     * Method ({"POST"})
     * @param Request $request
     * @param UserInterface $user
     * @param CompaniesRepository $company
     * @return Response
     */
    public function modifyStocksAmount(Request $request, UserInterface $user, CompaniesRepository $company) : Response {
        $em = $this->getDoctrine()->getManager();

        $action = $request->get('action');
        $comp_id = $request->get('comp_id');
        $amount = $request->get('amount');
        $id = $request->get('user_id');

        if ($action == 'buy' && $amount != null) {
            $procedure = "CALL stockBuy(:userid, :company_id, :amount)";
            $params['userid'] = $id;
            $params['company_id'] = $comp_id;
            $params['amount'] = $amount;
        }
        else if ($action == 'sell' && $amount != null){
            $procedure = "CALL stockSell(:userid, :company_id, :amount)";
            $params['userid'] = $id;
            $params['company_id'] = $comp_id;
            $params['amount'] = $amount;
        }
        else {
            $company= $em->getRepository(Companies::class)->findAll();

            return $this->render('main/stock_index.html.twig', [
                'user' => $user,
                'company' => $company,
                'alert' => "Actions were not filled correctly!",
            ]);
        }

        $stmt = $em->getConnection()->prepare($procedure);
        $stmt->execute($params);

        $company = $em->getRepository(Companies::class)->findAll();

        return $this->render('main/stock_index.html.twig', [
            'user' => $user,
            'company' => $company,
            'alert' =>  " ",
        ]);
    }

    /**
     * @Route("/actions/{name?}", name="actions")
     * @param Request $request
     * @param UserInterface $user
     * @param CompaniesRepository $companiesRepository
     * @param HistoryRepository $historyRepository
     * @param $name
     * @return Response
     */
    public function actions(Request $request, UserInterface $user, CompaniesRepository $companiesRepository, HistoryRepository $historyRepository, $name): Response {
        $company = $companiesRepository->findOneBy(array('cpnName' => $name));
        $companyId = $company->getId();
        $em = $this->getDoctrine()->getManager();

        $simulationValues = $this->prepareRandomValues($companyId);
        $procedure = "CALL updateCompany(:company_id, :value, :volume, :action )";
        $params['company_id'] = $companyId;
        $params['value'] = $simulationValues['value'];
        $params['volume'] = $simulationValues['volume'];
        $params['action'] = $simulationValues['action'];

        $stmt = $em->getConnection()->prepare($procedure);
        $stmt->execute($params);

        return $this->render('main/actions.html.twig', [
            'user' => $user,
            'company' => $company,
        ]);
    }

    public function insertHistoryContext($company, $simulationValues) {
        if ($simulationValues["action"] == 0) {
            $this->insertHistory($company, $simulationValues, -1);
        } else {
            $this->insertHistory($company, $simulationValues, 1);
        }
    }

    public function insertHistory($company, $simulationValues, $action) {
        $history = new History();
        $history->setHisValue($simulationValues["lastValue"] + $simulationValues["value"] * $action);
        $history->setHisVolume($simulationValues["stocksVolume"] + $simulationValues["volume"]);
        $history->setHisDate(\DateTime::createFromFormat("Y-m-d H:i:s", date("Y-m-d H:i:s")));
        $history->setCompany($company);
        $em = $this->getDoctrine()->getManager();
        $em->persist($history);
        $em->flush();
    }


    public function prepareRandomValues($companyId) {
        $em = $this->getDoctrine()->getManager();

        $repository = $em->getRepository(History::class);
        $lastValue = $repository->findBy(array('company' => $companyId), array('id' => 'DESC'), 1, 0);
        $lastValue = $lastValue[0]->getHisValue();

        $stocksVolume = $repository->findBy(array('company' => $companyId), array('id' => 'ASC'), 1, 0);
        $stocksVolume = $stocksVolume[0]->getHisVolume();

        return $array = array(
            "action" => $this->randomAction(),
            "value" => $this->randomValue($lastValue),
            "volume" => $this->randomVolume($stocksVolume),
        );
    }

    public function randomValue($lastValue) {
        $maxChange = $lastValue / 100;
        return rand(0, $maxChange * 100) / 100;
    }

    public function randomVolume($stocksVolume) {
        $maxStocks = $stocksVolume / 5;
        return (int)(rand(1 * 100, $maxStocks * 100) / 100);
    }

    public function randomAction() {
        return rand(0, 1);
    }

    public function getStockValueArray($companyId) {
        $conn = $this->getDoctrine()
            ->getConnection();
        $sql = 'SELECT * FROM history WHERE company_id = ' . $companyId;
        $stmt = $conn->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * @Route("/wallet/{id}", name="wallet")
     * @param UserInterface $user
     * @param CompaniesRepository $company
     * @return Response
     */

    public function wallet(UserInterface $user, CompaniesRepository $company) {
        dump($user);

        $userWallet = $user->getUserWallets();

        $em = $this->getDoctrine()->getManager();
        $company = $em->getRepository(Companies::class)->findAll();

        return $this->render('main/wallet.html.twig', [
            'user' => $user,
            'wallet' => $userWallet,
            'company' => $company,
        ]);
    }

    /**
     * @Route("/paymentMethod/{id}", name="payment_method")
     * @param UserInterface $user
     */

    public function paymentMethod(UserInterface $user, CompaniesRepository $company) {
        dump($user);

        $userWallet = $user->getUserWallets();

        $em = $this->getDoctrine()->getManager();
        $company = $em->getRepository(Companies::class)->findAll();


        return $this->render('main/payment.html.twig', [
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

    public function profile(Request $request, UserInterface $user, UserPasswordEncoderInterface $passwordEncoder, $id) {
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
                'usePhone' => $data['usePhone']
            ]);
        }

        return $this->render('main/profile.html.twig', [
            'user' => $user,
            'formAccount' => $formAccount->CreateView()
        ]);
    }

    /**
     * @Route("/edit_account/{id}/{username}/{useFirstName}/{useLastName}/{useEmail}", name="edit_account")
     * Method ({"POST"})
     * @param $id
     * @param $username
     * @param $useFirstName
     * @param $useLastName
     * @param $useEmail
     * @param $usePhone
     * @return RedirectResponse
     */
    public function changeAccountSettings($id, $username, $useFirstName, $useLastName, $useEmail, $usePhone) {
        $entityManager = $this->getDoctrine()->getManager();

        $user = $entityManager->getRepository(AccountEdit::class)->find($id);

        $user->setUsername($username);
        $user->setUseFirstName($useFirstName);
        $user->setUseLastName($useLastName);
        $user->setUseEmail($useEmail);
        $user->setUsePhone($usePhone);

        $entityManager->flush();


        return $this->redirectToRoute('profile', array('id' => $id));
    }

    /**
     * @Route("/profilePassword/{id}", name="profile_password")
     * Method ({"GET"})
     * @param UserInterface $user
     * @param $id
     * @return Response
     */
    public function profilePassword(UserInterface $user, $id) {
        $formPassword = $this->createFormBuilder()
            ->add('username', HiddenType::class, [
                'required' => true
            ])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'required' => true,
                'first_options' => ['label' => 'Password'],
                'second_options' => ['label' => 'Confirm Password'],
            ])->getForm();

        return $this->render('main/password.html.twig', [
            'user' => $user,
            'formPassword' => $formPassword->CreateView()
        ]);
    }


    /**
     * @Route("/changePassword/{id}", name="change_password")
     * Method ({"POST"})
     * @param Request $request
     * @param UserInterface $user
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param $id
     * @param $formPassword
     * @param $password
     * @return RedirectResponse|Response
     */
    public function changePassword(Request $request, UserInterface $user, UserPasswordEncoderInterface $passwordEncoder, $id, $formPassword, $password) {
        $formPassword->handleRequest($request);
        if ($formPassword->isSubmitted()) {
            $data = $formPassword->getData();

            return $this->forward('App\Controller\MainController::changePassword', [
                'id' => $id,
                'password' => $passwordEncoder->encodePassword($user, $data['password'])
            ]);
        }

        $entityManager = $this->getDoctrine()->getManager();

        $user = $entityManager->getRepository(PasswordEdit::class)->find($id);

        $user->setPassword($password);

        $entityManager->flush();

        return $this->redirectToRoute('profile', array('id' => $id));
    }

    /**
     * @Route("/tos", name="tos")
     */
    public function tos() {
        return $this->render('main/tos.html.twig');
    }
}
