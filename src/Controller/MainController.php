<?php

namespace App\Controller;

use App\Entity\Transactions;
use App\Entity\Users;
use App\Repository\CompaniesRepository;
use App\Repository\UsersRepository;
use Container3199tEd\getUserMoneyRepositoryService;
use Container3199tEd\getUsersRepositoryService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

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
        return $this->render('main/dashboard.html.twig', [

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
     * @Route("/profile", name="profile")
     * @param UserInterface $user
     */
    public function profile(UserInterface $user)  {
        dump($user);

        return $this->render('main/profile.html.twig', [
            'user' => $user,
        ]);
    } 

    /**
     * @Route("/tos", name="tos")
     */
    public function tos() {
        return $this->render('main/tos.html.twig');
    }
}
