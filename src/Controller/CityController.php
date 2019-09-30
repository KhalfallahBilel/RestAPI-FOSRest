<?php

namespace App\Controller;

use App\Entity\City;
use App\Repository\CityRepository;
use App\Repository\CountryRepository;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations as Rest; // alias pour toutes les annotations


class CityController extends AbstractFOSRestController
{

    /**
     * @var CountryRepository
     */
    private $countryRepository;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    /**
     * @var CityRepository
     */
    private $cityRepository;

    /**
     * CityController constructor.
     * @param CountryRepository $countryRepository
     * @param EntityManagerInterface $entityManager
     * @param CityRepository $cityRepository
     */
    public function __construct(CountryRepository $countryRepository, EntityManagerInterface $entityManager, CityRepository $cityRepository)
    {
        $this->countryRepository = $countryRepository;
        $this->entityManager = $entityManager;
        $this->cityRepository = $cityRepository;
    }


    public function getCitiesAction()
    {
        $cities = $this->cityRepository->findAll();
        if ($cities) {
            return $this->view($cities, Response::HTTP_OK);
        } else {
            return $this->view($cities, Response::HTTP_NOT_FOUND);
        }
    }

    public function getCityAction(int $id)
    {
        $data = $this->cityRepository->find($id);
        if ($data) {
            return $this->view($data, Response::HTTP_OK);
        } else {
            return $this->view($data, Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * @Rest\View(statusCode=Response::HTTP_CREATED)
     * @Rest\Post("/cities")
     * @param Request $request
     * @return View
     */
    public function postCityAction(Request $request)
    {
        $countryName = $request->get('country');
        //echo $countryName;
        $country = $this
            ->countryRepository
            ->findOneBySomeField($countryName);
        //echo $country;
        if ($country) {
            //echo $country;
            //echo $countryName;
            $city = new City();
            $city->setName($request->get('name'));
            $city->setZipCode($request->get('zip_code'));
            $city->setCountry($country);
            $this->entityManager->persist($city);
            $this->entityManager->persist($country);
            $this->entityManager->flush();
        } else {
            echo "not found";
        }
        return $this->view($city, Response::HTTP_CREATED);
        return $this->view(['name' => 'This cannot be null'], Response::HTTP_BAD_REQUEST);
    }

    /**
     * @Rest\View(statusCode=Response::HTTP_OK)
     * @Rest\Put("/cities/{id}")
     * @param Request $request
     * @param int $id
     * @return View
     * @throws \Exception
     */
    public function putCityAction(Request $request, int $id)
    {
        $countryName = $request->get('country');
        $country = $this->countryRepository->findOneBySomeField($countryName);
        if ($country) {
            echo $country;
            echo "the country is : " . $country;
            $cityName = $request->get('name');
            echo $cityName;
            $city = $this->cityRepository->find($id);
            echo $city;
            if ($city) {
                echo $city;
                $city->setName($request->get('name'));
                $city->setZipCode($request->get('zip_code'));
                $city->setCountry($country);
                $city->setUpdatedAt(new \DateTime());
                $this->entityManager->persist($city);
                $this->entityManager->persist($country);
                $this->entityManager->flush();
            }
            return $this->view($city, Response::HTTP_OK);
        } else {
            return $this->view($country, Response::HTTP_NOT_FOUND);
        }
        return $this->view(['name' => 'This cannot be null'], Response::HTTP_BAD_REQUEST);
    }

    public function deleteCityAction(int $id)
    {
        $city = $this->cityRepository->findOneBy(['id' => $id]);
        if ($city) {
            $this->entityManager->remove($city);
            $this->entityManager->flush();
            return $this->view(null, Response::HTTP_NO_CONTENT);
        } else {
            return $this->view(null, Response::HTTP_NOT_FOUND);
        }
    }
}
