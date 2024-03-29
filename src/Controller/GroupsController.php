<?php

namespace App\Controller;

use App\Entity\Group;
use App\Repository\GroupRepository;
use App\Serializer\Normalizer\GroupNormalizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class GroupsController extends AbstractController
{

    /**
     * @var GroupRepository
     */
    private $groupRepository;
    /**
     * @var GroupNormalizer
     */
    private $objectNormalizer;

    public function __construct(GroupRepository $groupRepository)
    {

        $this->groupRepository = $groupRepository;
        $normalizers = [new GroupNormalizer()];
        $serializer = new Serializer($normalizers);
        $this->objectNormalizer = $serializer;
    }

    /**
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @throws \Symfony\Component\Serializer\Exception\ExceptionInterface
     */
    public function list()
    {
        return $this->json(
            $this->objectNormalizer->normalize(
                $this->groupRepository->findAll(),
                GroupNormalizer::AS_OBJECT
            )
        );
    }

    /**
     * @param $id
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @throws \Symfony\Component\Serializer\Exception\ExceptionInterface
     */
    public function show($id)
    {
        return $this->json(
            $this->objectNormalizer->normalize(
                $this->groupRepository->findOrFail($id),
                GroupNormalizer::AS_OBJECT
            )
        );
    }

    public function create(Request $request, ValidatorInterface $validator)
    {
        if (!$this->getUser()->hasRole('ROLE_ADMIN')) {
            return $this->json(null, Response::HTTP_FORBIDDEN);
        }


        if ($this->groupRepository->find($request->request->get('name'))) {
            return $this->json(['message' => 'Group already exists'], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $entityManager = $this->getDoctrine()->getManager();

        $group = new Group();
        $group->setName($request->request->get('name'));
        $group->setIsAdmin(strtolower($request->request->get('isAdmin')) === 'y');

        $errors = $validator->validate($group);
        if (count($errors)) {
            $message = '';
            /** @var ConstraintViolation $error */
            foreach ($errors as $error) {
                $message .= $error->getMessage().', ';
            }
            $message = rtrim($message, ', ');

            return $this->json(
                ['message' => $message],
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        $entityManager->persist($group);
        $entityManager->flush();

        return $this->json(null, Response::HTTP_CREATED);
    }

    public function delete($id)
    {
        if (!$this->getUser()->hasRole('ROLE_ADMIN')) {
            return $this->json(null, Response::HTTP_FORBIDDEN);
        }

        $group = $this->groupRepository->findOrFail($id);

        if ($group->getUsers()->count()) {
            return $this->json(['message' => 'Can\'t delete group with users'], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $entityManager = $this->getDoctrine()->getManager();

        $entityManager->remove($group);
        $entityManager->flush();

        return $this->json(null, Response::HTTP_CREATED);
    }
}
