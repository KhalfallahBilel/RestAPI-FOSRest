<?php

namespace App\Controller;

use App\Entity\Information;
use App\Entity\User;
use App\Repository\UserRepository;
use App\Repository\InformationRepository;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations as Rest;


class UserController extends AbstractFOSRestController
{

    /**
     * @var UserRepository
     */
    private $userRepository;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    /**
     * @var InformationRepository
     */
    private $informationRepository;

    public function __construct(
        UserRepository $userRepository,
        InformationRepository $informationRepository,
        EntityManagerInterface $entityManager
    )
    {
        $this->userRepository = $userRepository;
        $this->informationRepository = $informationRepository;
        $this->entityManager = $entityManager;
    }

    public function getUsersAction()
    {
        $users = $this->userRepository->findAll();
        return $this->view($users, Response::HTTP_OK);
    }

    public function getUserAction(int $id)
    {
        $data = $this->userRepository->find($id);
        return $this->view($data, Response::HTTP_OK);
    }

    /**
     * @Rest\View(statusCode=Response::HTTP_CREATED)
     * @Rest\Post("/users")
     * @param Request $request
     * @return View
     */
    public function postUsersAction(Request $request)
    {
        $informationNumber = $request->get('information');
        $information = $this
            ->informationRepository
            ->findOneBySomeField($informationNumber);
        if ($information) {
            $user = new User();
            $user->setUsername($request->get('username'));
            $user->setFirstname($request->get('firstname'));
            $user->setLastname($request->get('lastname'));
            $user->setEmail($request->get('email'));
            $user->setPassword($request->get('password'));
            $user->setInformation($information);
            $information->setUser($user);
            $this->entityManager->persist($user);
            $this->entityManager->persist($information);
            $this->entityManager->flush();
        } else {
            echo "not found";
        }
        return $this->view($user, Response::HTTP_OK);
    }

    public function putUserAction(int $id)
    {

    }

    public function deleteUserAction(int $id)
    {
        $user = $this->userRepository->findOneBy(['id' => $id]);
        $this->entityManager->remove($user);
        $this->entityManager->flush();
        return $this->view(null, Response::HTTP_NO_CONTENT);
    }

}
