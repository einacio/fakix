<?php

namespace App\Controller;

use App\Repository\GroupRepository;
use App\Serializer\Normalizer\GroupNormalizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\Serializer;

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

    public function __construct(GroupRepository $groupRepository){

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

    public function create()
    {
    }

    public function delete($id)
    {
    }}
