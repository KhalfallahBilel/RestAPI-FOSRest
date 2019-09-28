<?php

namespace App\Controller;

use App\Entity\Country;
use App\Repository\CityRepository;
use App\Repository\CountryRepository;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations as Rest; // alias pour toutes les annotations
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

class CountryController extends AbstractFOSRestController
{
    // /**
    //  * @Route("/country", name="country")
    //  */
    // public function index()
    // {
    //     return $this->json([
    //         'message' => 'Welcome',
    //         'path' => 'src/Controller/CountryController.php',
    //     ]);
    // }

    // /**
    //  * @Rest\Post("/update", name="app.update")
    //  */
    // public function update()
    // {
    //     return ['message' => 'update'];
    // }

    // /**
    //  * @Rest\Delete("/delete")
    //  */
    // public function remove()
    // {

    // }


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

    public function getCountriesAction()
    {
        $countries =  $this->countryRepository->findAll();
        return $this->view($countries, Response::HTTP_OK);
    }

    public function getCountryAction(int $id)
    {
        $data = $this->countryRepository->find($id);
        return $this->view($data, Response::HTTP_OK);
    }


    /**
     * @Rest\View(statusCode=Response::HTTP_CREATED)
     * @Rest\Post("/countries")
     */
    public function postCountriesAction(Request $request)
    {
        
            $country = new Country();
            $country->setName($request->get('name'));
            $country->setIsoCode($request->get('iso_code'));
            $this->entityManager->persist($country);
            $this->entityManager->flush();
            return $this->view($country, Response::HTTP_CREATED);
        
        return $this->view(['name' => 'This cannot be null'], Response::HTTP_BAD_REQUEST);
    }

    

    public function putCountryAction(int $id)
    {

    }

    public function deleteCountryAction(int $id)
    {
        $country = $this->countryRepository->findOneBy(['id' => $id]);
        $this->entityManager->remove($country);
        $this->entityManager->flush();
        return $this->view(null, Response::HTTP_NO_CONTENT);
    }

}
