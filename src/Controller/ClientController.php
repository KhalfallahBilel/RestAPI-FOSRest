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
        return $this->view($clients, Response::HTTP_OK);
    }

    public function getClientAction(int $id)
    {
        $data = $this->clientRepository->find($id);
        return $this->view($data, Response::HTTP_OK);
    }

    /**
     * @Rest\View(statusCode=Response::HTTP_CREATED)
     * @Rest\Post("/clients")
     * @param Request $request
     * @return View
     */
    public function postClientsAction(Request $request)
    {
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
                    echo " information not found";
                }
            } else {
                echo " user not found";
            }
        }
        return $this->view($client, Response::HTTP_OK);
    }

    public function putClientAction(int $id)
    {

    }

    public function deleteClientAction(int $id)
    {
        $client = $this->clientRepository->findOneBy(['id' => $id]);
        $this->entityManager->remove($client);
        $this->entityManager->flush();
        return $this->view(null, Response::HTTP_NO_CONTENT);
    }

}
