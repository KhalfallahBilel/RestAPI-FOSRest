<?php

namespace App\Controller;

use App\Entity\Project;
use App\Repository\UserRepository;
use App\Repository\ClientRepository;
use App\Repository\ProjectRepository;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;
use Nelmio\ApiDocBundle\Annotation\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations as Rest;


class ProjectController extends AbstractFOSRestController
{
    /**
     * @var ProjectRepository
     */
    private $projectRepository;
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

    public function __construct(
        UserRepository $userRepository,
        ClientRepository $clientRepository,
        ProjectRepository $projectRepository,
        EntityManagerInterface $entityManager
    )
    {
        $this->projectRepository = $projectRepository;
        $this->userRepository = $userRepository;
        $this->clientRepository = $clientRepository;
        $this->entityManager = $entityManager;
    }

    /**
     * @SWG\Response(
     *     response=200,
     *     description="Returns an array of projects",
     *      @SWG\Schema(
     *         type="array",
     *         @Model(type=Project::class, groups={"non_sensitive_data"})
     *     )
     * )
     * @SWG\Response(
     *     response=401,
     *     description="JWT Not valid ! No permission",
     * )
     * @SWG\Tag(name="projects")
     * @Security(name="Bearer")
     */
    public function getProjectsAction()
    {
        $projects = $this->projectRepository->findAll();
        if ($projects) {
            return $this->view($projects, Response::HTTP_OK);
        } else {
            return $this->view(null, Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * @SWG\Response(
     *     response=200,
     *     description="Returns a project by id",
     *     @Model(type=Project::class, groups={"non_sensitive_data"})
     * )
     * @SWG\Response(
     *     response=401,
     *     description="JWT Not valid ! No permission",
     * )
     * @SWG\Tag(name="projects")
     * @Security(name="Bearer")
     *
     */
    public function getProjectAction(int $id)
    {
        $data = $this->projectRepository->find($id);
        if ($data) {
            return $this->view($data, Response::HTTP_OK);
        } else {
            return $this->view(null, Response::HTTP_NOT_FOUND);
        }
    }

    /**
     *  @SWG\Response(
     *     response=200,
     *     description="Project Created with success",
     *     @Model(type=Project::class, groups={"non_sensitive_data"})
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
     * @Rest\Post("/projects")
     * @param Request $request
     * @return View
     * @SWG\Tag(name="projects")
     * @Security(name="Bearer")
     */
    public function postProjectsAction(Request $request)
    {
        $username = $request->get('manager');
        $user = $this
            ->userRepository
            ->findOneBySomeField($username);
        $clientId = $request->get('client');
        $client = $this
            ->clientRepository
            ->findOneBySomeField($clientId);
        if ($user) {
            if ($client) {
                $project = new Project();
                $project->setName($request->get('name'));
                $project->setStorage($request->get('storage'));
                $project->setStorageKey($request->get('storage_key'));
                $project->setDbname($request->get('dbname'));
                $project->setUrl($request->get('url'));
                $project->setDescription($request->get('description'));
                $project->setManager($user);
                $project->setClient($client);
                $client->setProjects($project);
                $this->entityManager->persist($project);
                $this->entityManager->persist($client);
                $this->entityManager->persist($user);
                $this->entityManager->flush();
            } else {
                return $this->view($client, Response::HTTP_NOT_FOUND);
            }
        } else {
            return $this->view($user, Response::HTTP_NOT_FOUND);
        }
        return $this->view($project, Response::HTTP_CREATED);
        return $this->view(['name' => 'This cannot be null'], Response::HTTP_BAD_REQUEST);
    }

    /**
     * @SWG\Response(
     *     response=201,
     *     description="Project updated with success",
     *     @Model(type=Project::class, groups={"non_sensitive_data"})
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
     * @Rest\Put("/projects/{id}")
     * @param Request $request
     * @param int $id
     * @return View
     * @throws \Exception
     * @SWG\Tag(name="projects")
     * @Security(name="Bearer")
     */
    public function putProjectAction(Request $request, int $id)
    {
        $username = $request->get('manager');
        $user = $this
            ->userRepository
            ->findOneBySomeField($username);
        $clientId = $request->get('client');
        $client = $this
            ->clientRepository
            ->findOneBySomeField($clientId);
        $project = $this
            ->projectRepository
            ->find($id);
        if ($user) {
            if ($client) {
                if ($project) {
                    $project->setName($request->get('name'));
                    $project->setStorage($request->get('storage'));
                    $project->setStorageKey($request->get('storage_key'));
                    $project->setDbname($request->get('dbname'));
                    $project->setUrl($request->get('url'));
                    $project->setDescription($request->get('description'));
                    $project->setUpdatedAt(new \DateTime());
                    $project->setManager($user);
                    $project->setClient($client);
                    $client->setProjects($project);
                    $this->entityManager->persist($project);
                    $this->entityManager->persist($client);
                    $this->entityManager->persist($user);
                    $this->entityManager->flush();
                }
                return $this->view($project, Response::HTTP_OK);
            } else {
                return $this->view($client, Response::HTTP_NOT_FOUND);
            }
        } else {
            return $this->view($user, Response::HTTP_NOT_FOUND);
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
     *     description="Project Not Found",
     * )
     * @SWG\Tag(name="projects")
     * @Security(name="Bearer")
     */
    public function deleteProjectAction(int $id)
    {
        $project = $this->projectRepository->findOneBy(['id' => $id]);
        if ($project) {
            $this->entityManager->remove($project);
            $this->entityManager->flush();
            return $this->view(null, Response::HTTP_NO_CONTENT);
        } else {
            return $this->view(null, Response::HTTP_NOT_FOUND);
            return $this->view(null, Response::HTTP_NOT_FOUND);
        }
    }

}
