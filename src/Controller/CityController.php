<?php

namespace App\Controller;

use App\Entity\City;
use App\Entity\Country;
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
        return $this->view($cities, Response::HTTP_OK);
    }

    public function getCityAction(int $id)
    {
        $data = $this->cityRepository->find($id);
        return $this->view($data, Response::HTTP_OK);
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
        return $this->view($city, Response::HTTP_OK);
    }

    public function putCityAction(int $id)
    {
    }

    public function deleteCityAction(int $id)
    {
        $city = $this->cityRepository->findOneBy(['id' => $id]);
        $this->entityManager->remove($city);
        $this->entityManager->flush();
        return $this->view(null, Response::HTTP_NO_CONTENT);
    }
}
