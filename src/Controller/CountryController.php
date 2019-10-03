<?php

namespace App\Controller;

use App\Entity\Country;
use App\Repository\CityRepository;
use App\Repository\CountryRepository;
use Exception;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Annotation\Model;
use phpDocumentor\Reflection\DocBlock\Serializer;
use Swagger\Annotations as SWG;
use Nelmio\ApiDocBundle\Annotation\Security;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations as Rest; // alias pour toutes les annotations
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class CountryController extends AbstractFOSRestController
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

    public function __construct(
        CountryRepository $countryRepository,
        CityRepository $cityRepository,
        EntityManagerInterface $entityManager
    )
    {
        $this->countryRepository = $countryRepository;
        $this->cityRepository = $cityRepository;
        $this->entityManager = $entityManager;
    }

    /**
     * @SWG\Response(
     *     response=200,
     *     description="Returns an array of countries",
     *      @SWG\Schema(
     *         type="array",
     *         @Model(type=Country::class, groups={"non_sensitive_data"})
     *     )
     * )
     * @SWG\Response(
     *     response=401,
     *     description="JWT Not valid ! No permission",
     * )
     * @SWG\Tag(name="countries")
     * @Security(name="Bearer")
     */
    public function getCountriesAction()
    {
        $countries = $this->countryRepository->findAll();
        if ($countries) {
            return $this->view($countries, Response::HTTP_OK);
        } else {
            return $this->view(null, Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * @SWG\Response(
     *     response=200,
     *     description="Returns a country by id",
     *     @Model(type=Country::class, groups={"non_sensitive_data"})
     * )
     * @SWG\Response(
     *     response=401,
     *     description="JWT Not valid ! No permission",
     * )
     * @SWG\Tag(name="countries")
     * @Security(name="Bearer")
     *
     */
    public function getCountryAction(int $id)
    {
        $data = $this->countryRepository->find($id);
        if ($data) {
            return $this->view($data, Response::HTTP_OK);
        } else {
            return $this->view(null, Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * @SWG\Response(
     *     response=200,
     *     description="Country Created with success",
     *     @Model(type=Country::class, groups={"non_sensitive_data"})
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
     * @Rest\Post("/countries")
     * @param Request $request
     * @return View
     * @SWG\Tag(name="countries")
     * @Security(name="Bearer")
     */
    public function postCountriesAction(Request $request)
    {
        $country = new Country();
        $country->setName($request->get('name'));
        $country->setIsoCode($request->get('iso_code'));
        $encoders = [new XmlEncoder(), new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];

        $serializer = new Serializer($normalizers, $encoders);
        $jsonContent = $serializer->serialize($country, 'json');
        $this->entityManager->persist($country);
        $this->entityManager->flush();
        return $this->view($jsonContent, Response::HTTP_CREATED);
        return $this->view(['name' => 'This cannot be null'], Response::HTTP_BAD_REQUEST);
    }


    /**
     * @SWG\Response(
     *     response=201,
     *     description="Country updated with success",
     *
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
     * @Rest\Put("/countries/{id}")
     * @param Request $request
     * @param int $id
     * @return View
     * @throws Exception
     * @SWG\Tag(name="countries")
     * @Security(name="Bearer")
     */
    public function putCountryAction(Request $request, int $id)
    {
        $country = $this->countryRepository->find($id);
        if ($country) {
            echo $country;
            echo "the country is : " . $country;
            $country->setName($request->get('name'));
            $country->setIsoCode($request->get('iso_code'));
            $country->setIsoCode($request->get('iso_code'));
            $country->setUpdatedAt(new \DateTime());
            $this->entityManager->persist($country);
            $this->entityManager->flush();
            return $this->view($country, Response::HTTP_OK);
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
     *     description="Country Not Found",
     * )
     * @SWG\Tag(name="countries")
     * @Security(name="Bearer")
     */
    public function deleteCountryAction(int $id)
    {
        $country = $this->countryRepository->findOneBy(['id' => $id]);
        if ($country) {
            $this->entityManager->remove($country);
            $this->entityManager->flush();
            return $this->view(null, Response::HTTP_NO_CONTENT);
        } else {
            return $this->view(null, Response::HTTP_NOT_FOUND);
        }
    }

}
