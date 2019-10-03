<?php

namespace App\Controller;

use App\Entity\City;
use App\Repository\CityRepository;
use App\Repository\CountryRepository;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\View\View;
use Swagger\Annotations as SWG;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
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

    /**
     * @SWG\Response(
     *     response=200,
     *     description="Returns an array of cities",
     *      @SWG\Schema(
     *         type="array",
     *         @Model(type=City::class, groups={"non_sensitive_data"})
     *     )
     * )
     * @SWG\Response(
     *     response=401,
     *     description="JWT Not valid ! No permission",
     * )
     * @SWG\Tag(name="cities")
     * @Security(name="Bearer")
     */
    public function getCitiesAction()
    {
        $cities = $this->cityRepository->findAll();
        if ($cities) {
            return $this->view($cities, Response::HTTP_OK);
        } else {
            return $this->view($cities, Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * @SWG\Response(
     *     response=200,
     *     description="Returns a city by id",
     *     @Model(type=City::class, groups={"non_sensitive_data"})
     * )
     * @SWG\Response(
     *     response=401,
     *     description="JWT Not valid ! No permission",
     * )
     * @SWG\Tag(name="cities")
     * @Security(name="Bearer")
     *
     */
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
     * @SWG\Response(
     *     response=200,
     *     description="City Created with success",
     *     @Model(type=City::class, groups={"non_sensitive_data"})
     * )
     * @SWG\Response(
     *     response=401,
     *     description="JWT Not valid ! No permission",
     * )
     * @SWG\Response(
     *     response=400,
     *     description="Bad Request",
     * )
     * @Rest\View(statusCode=Response::HTTP_CREATED)
     * @Rest\Post("/cities")
     * @param Request $request
     * @return View
     * @SWG\Tag(name="cities")
     * @Security(name="Bearer")
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
     *  @SWG\Response(
     *     response=201,
     *     description="City updated with success",
     *     @Model(type=City::class, groups={"non_sensitive_data"})
     * )
     * @SWG\Response(
     *     response=401,
     *     description="JWT Not valid ! No permission",
     * )
     * @SWG\Response(
     *     response=400,
     *     description="Bad Request",
     * )
     * @Rest\View(statusCode=Response::HTTP_OK)
     * @Rest\Put("/cities/{id}")
     * @param Request $request
     * @param int $id
     * @return View
     * @throws \Exception
     * @SWG\Tag(name="cities")
     * @Security(name="Bearer")
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

    /**
     * @SWG\Response(
     *     response=201,
     *     description="No Content",
     * )
     * @SWG\Response(
     *     response=401,
     *     description="JWT Not valid ! No permission",
     * )
     * @SWG\Response(
     *     response=404,
     *     description="City Not Found",
     * )
     * @SWG\Tag(name="cities")
     * @Security(name="Bearer")
     */
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
