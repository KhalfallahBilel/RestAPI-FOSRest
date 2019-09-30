<?php

namespace App\Controller;

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
        if ($users) {
            return $this->view($users, Response::HTTP_OK);
        } else {
            return $this->view(null, Response::HTTP_NOT_FOUND);
        }
    }

    public function getUserAction(int $id)
    {
        $data = $this->userRepository->find($id);
        if ($data) {
            return $this->view($data, Response::HTTP_OK);
        } else {
            return $this->view(null, Response::HTTP_NOT_FOUND);
        }
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
            return $this->view($information, Response::HTTP_NOT_FOUND);
        }
        return $this->view($user, Response::HTTP_OK);
        return $this->view(['name' => 'This cannot be null'], Response::HTTP_BAD_REQUEST);
    }

    /**
     * @Rest\View(statusCode=Response::HTTP_OK)
     * @Rest\Put("/users/{id}")
     * @param Request $request
     * @param int $id
     * @return View
     * @throws \Exception
     */
    public function putUserAction(Request $request, int $id)
    {
        $informationNumber = $request->get('information');
        $information = $this->informationRepository->findOneBySomeField($informationNumber);
        if ($information) {
            echo $information;
            echo "the information is : " . $information;
            $user = $this->userRepository->find($id);
            echo $user;
            if ($user) {
                echo $user;
                $user->setUsername($request->get('username'));
                $user->setFirstname($request->get('firstname'));
                $user->setLastname($request->get('lastname'));
                $user->setEmail($request->get('email'));
                $user->setPassword($request->get('password'));
                $user->setUpdateAt(new \DateTime());
                $user->setInformation($information);
                $information->setUser($user);
                $this->entityManager->persist($user);
                $this->entityManager->persist($information);
                $this->entityManager->flush();
            }
            return $this->view($user, Response::HTTP_OK);
        } else {
            return $this->view($information, Response::HTTP_NOT_FOUND);
        }
        return $this->view(['name' => 'This cannot be null'], Response::HTTP_BAD_REQUEST);
    }

    public function deleteUserAction(int $id)
    {
        $user = $this->userRepository->findOneBy(['id' => $id]);
        if ($user) {
            $this->entityManager->remove($user);
            $this->entityManager->flush();
            return $this->view(null, Response::HTTP_NO_CONTENT);
        } else {
            return $this->view(null, Response::HTTP_NOT_FOUND);
        }
    }

}
