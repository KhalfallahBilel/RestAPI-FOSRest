<?php

namespace App\Controller;

use App\Entity\Information;
use App\Repository\CityRepository;
use App\Repository\InformationRepository;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations as Rest;


class InformationController extends AbstractFOSRestController
{

    /**
     * @var InformationRepository
     */
    private $informationRepository;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    /**
     * @var CityRepository
     */
    private $cityRepository;

    public function __construct(
        InformationRepository $informationRepository,
        CityRepository $cityRepository,
        EntityManagerInterface $entityManager
    )
    {
        $this->informationRepository = $informationRepository;
        $this->cityRepository = $cityRepository;
        $this->entityManager = $entityManager;
    }

    public function getInformationsAction()
    {
        $informations = $this->informationRepository->findAll();
        if ($informations) {
            return $this->view($informations, Response::HTTP_OK);
        } else {
            return $this->view($informations, Response::HTTP_NOT_FOUND);
        }
    }

    public function getInformationAction(int $id)
    {
        $data = $this->informationRepository->find($id);
        if ($data) {
            return $this->view($data, Response::HTTP_OK);
        } else {
            return $this->view($data, Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * @Rest\View(statusCode=Response::HTTP_CREATED)
     * @Rest\Post("/informations")
     * @param Request $request
     * @return View
     */
    public function postInformationAction(Request $request)
    {
        $cityName = $request->get('city');
        $city = $this
            ->cityRepository
            ->findOneBySomeField($cityName);
        if ($city) {
            $information = new Information();
            $information->setNumber($request->get('number'));
            $information->setStreet($request->get('street'));
            $information->setAddressComplement($request->get('address_complement'));
            $information->setPhone($request->get('phone'));
            $information->setMobile($request->get('mob'));
            $information->setNotes($request->get('notes'));
            $information->setCity($city);
            $this->entityManager->persist($city);
            $this->entityManager->persist($information);
            $this->entityManager->flush();
        } else {
            return $this->view($city, Response::HTTP_NOT_FOUND);
        }
        return $this->view($information, Response::HTTP_CREATED);
        return $this->view(['name' => 'This cannot be null'], Response::HTTP_BAD_REQUEST);
    }

    /**
     * @Rest\View(statusCode=Response::HTTP_OK)
     * @Rest\Put("/informations/{id}")
     * @param Request $request
     * @param int $id
     * @return View
     * @throws \Exception
     */
    public function putInformationAction(Request $request, int $id)
    {
        $cityName = $request->get('city');
        $city = $this->cityRepository->findOneBySomeField($cityName);
        if ($cityName) {
            echo $city;
            echo "the city is : " . $city;
            $information = $this->informationRepository->find($id);
            echo $information;
            if ($information) {
                echo $information;
                $information->setNumber($request->get('number'));
                $information->setStreet($request->get('street'));
                $information->setAddressComplement($request->get('address_complement'));
                $information->setPhone($request->get('phone'));
                $information->setMobile($request->get('mob'));
                $information->setNotes($request->get('notes'));
                $information->setUpdateAt(new \DateTime());
                $information->setCity($city);
                $this->entityManager->persist($city);
                $this->entityManager->persist($information);
                $this->entityManager->flush();
            }
            return $this->view($information, Response::HTTP_OK);
        } else {
            return $this->view($city, Response::HTTP_NOT_FOUND);
        }
        return $this->view(['name' => 'This cannot be null'], Response::HTTP_BAD_REQUEST);
    }

    public function deleteInformationAction(int $id)
    {
        $information = $this->informationRepository->findOneBy(['id' => $id]);
        if ($information) {
            $this->entityManager->remove($information);
            $this->entityManager->flush();
            return $this->view(null, Response::HTTP_NO_CONTENT);
        } else {
            return $this->view(null, Response::HTTP_NOT_FOUND);
        }
    }
}
