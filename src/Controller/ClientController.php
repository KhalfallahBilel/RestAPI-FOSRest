<?php

namespace App\Controller;

use App\Repository\InformationRepository;
use App\Repository\UserRepository;
use App\Repository\ClientRepository;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\View\View;
use App\Entity\Client;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations as Rest;


class ClientController extends AbstractFOSRestController
{

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    /**
     * @var ClientRepository
     */
    private $clientRepository;
    /**
     * @var UserRepository
     */
    private $userRepository;
    /**
     * @var InformationRepository
     */
    private $informationRepository;

    public function __construct(
        UserRepository $userRepository,
        ClientRepository $clientRepository,
        InformationRepository $informationRepository,
        EntityManagerInterface $entityManager
    )
    {
        $this->userRepository = $userRepository;
        $this->clientRepository = $clientRepository;
        $this->entityManager = $entityManager;
        $this->informationRepository = $informationRepository;
    }

    public function getClientsAction()
    {
        $clients = $this->clientRepository->findAll();
        if ($clients) {
            return $this->view($clients, Response::HTTP_OK);
        } else {
            return $this->view(null, Response::HTTP_NOT_FOUND);
        }
    }

    public function getClientAction(int $id)
    {
        $data = $this->clientRepository->find($id);
        if ($data) {
            return $this->view($data, Response::HTTP_OK);
        } else {
            return $this->view(null, Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * @Rest\View(statusCode=Response::HTTP_CREATED)
     * @Rest\Post("/clients")
     * @param Request $request
     * @return View
     */
    public function postClientsAction(Request $request)
    {
        $username = $request->get('manager');
        $user = $this
            ->userRepository
            ->findOneBySomeField($username);
        $informationNumber = $request->get('information');
        $information = $this
            ->informationRepository
            ->findOneBySomeField($informationNumber);
        if ($user) {
            if ($information) {
                $client = new Client();
                $client->setNotes($request->get('notes'));
                $client->setManager($user);
                $client->setInformation($information);
                $user->setClient($client);
                $information->setClient($client);
                $this->entityManager->persist($client);
                $this->entityManager->persist($user);
                $this->entityManager->persist($information);
                $this->entityManager->flush();
            } else {
                return $this->view($information, Response::HTTP_NOT_FOUND);
            }
        } else {
            return $this->view($user, Response::HTTP_NOT_FOUND);
        }
        return $this->view($client, Response::HTTP_CREATED);
        return $this->view(['name' => 'This cannot be null'], Response::HTTP_BAD_REQUEST);
    }

    /**
     * @Rest\View(statusCode=Response::HTTP_OK)
     * @Rest\Put("/clients/{id}")
     * @param Request $request
     * @param int $id
     * @return View
     * @throws \Exception
     */
    public function putClientAction(Request $request, int $id)
    {
        $username = $request->get('manager');
        $user = $this
            ->userRepository
            ->findOneBySomeField($username);
        $informationNumber = $request->get('information');
        $information = $this
            ->informationRepository
            ->findOneBySomeField($informationNumber);
        $client = $this
            ->clientRepository
            ->find($id);
        if ($user) {
            if ($information) {
                if ($client) {
                    $client->setNotes($request->get('notes'));
                    $client->setManager($user);
                    $client->setInformation($information);
                    $user->setClient($client);
                    $information->setClient($client);
                    $this->entityManager->persist($client);
                    $this->entityManager->persist($user);
                    $this->entityManager->persist($information);
                    $this->entityManager->flush();
                }
                return $this->view($client, Response::HTTP_OK);
            } else {
                return $this->view($information, Response::HTTP_NOT_FOUND);
            }
        } else {
            return $this->view($user, Response::HTTP_NOT_FOUND);
        }
        return $this->view(['name' => 'This cannot be null'], Response::HTTP_BAD_REQUEST);
    }

    public function deleteClientAction(int $id)
    {
        $client = $this->clientRepository->findOneBy(['id' => $id]);
        if ($client) {
            $this->entityManager->remove($client);
            $this->entityManager->flush();
            return $this->view(null, Response::HTTP_NO_CONTENT);
        } else {
            return $this->view(null, Response::HTTP_NOT_FOUND);
        }
    }

}
