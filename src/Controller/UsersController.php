<?php

namespace App\Controller;

use App\Repository\GroupRepository;
use App\Repository\UserRepository;
use App\Serializer\Normalizer\UserNormalizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Serializer\Serializer;

class UsersController extends AbstractController
{

    /**
     * @var UserRepository
     */
    private $userRepository;
    /**
     * @var UserNormalizer
     */
    private $objectNormalizer;
    /**
     * @var SessionInterface
     */
    private $session;
    /**
     * @var GroupRepository
     */
    private $groupRepository;

    public function __construct(UserRepository $userRepository, GroupRepository $groupRepository, SessionInterface $session)
    {
        $this->userRepository = $userRepository;

        $normalizers = [new UserNormalizer()];
        $serializer = new Serializer($normalizers);
        $this->objectNormalizer = $serializer;
        $this->session = $session;
        $this->groupRepository = $groupRepository;
    }

    public function addGroup($id, Request $request)
    {
        $user = $this->userRepository->findOrFail($id);

        $group = $this->groupRepository->findOrFail($request->request->get('group'));

        $user->addGroup($group);

        return $this->json(null);

    }

    public function delete($id)
    {

    }

    public function create()
    {
    }

    /**
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @throws \Symfony\Component\Serializer\Exception\ExceptionInterface
     */
    public function list()
    {

        return $this->json(
            $this->objectNormalizer->normalize(
                $this->userRepository->findAll(),
                UserNormalizer::AS_OBJECT
            )
        );
    }

    public function removeGroup($groupId, $id)
    {
    }

    /**
     * @param int $id
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @throws \Symfony\Component\Serializer\Exception\ExceptionInterface
     */
    public function show(int $id)
    {
        return $this->json(
            $this->objectNormalizer->normalize(
                $this->userRepository->findOrFail($id),
                UserNormalizer::AS_OBJECT
            )
        );
    }

    public function update($id, UserPasswordEncoderInterface $passwordEncoder, Request $request)
    {
        $user = $this->userRepository->findOrFail($id);

        $user->setPassword($passwordEncoder->encodePassword(
                         $user,
                         $request->query->get('new_password')
                     ));
        return $this->json([]);
    }
}
