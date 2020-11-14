<?php

namespace App\Controller;

use App\Entity\Transactions;
use App\Entity\Users;
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
     * @return Response
     */
    public function dashboard(UserInterface $user) : Response {
        dump($user);


        return $this->render('main/dashboard.html.twig', [
            'user' => $user,
        ]);
    }

    public function showTransactions($userId) {
        $user = $this->getDoctrine()
            ->getRepository(Users::class)
            ->findTransactionsById($userId);

        $transactions = $user->getUsers();
    }

}
