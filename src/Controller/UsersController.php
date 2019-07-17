<?php

namespace App\Controller;

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

    public function __construct(UserRepository $userRepository, SessionInterface $session)
    {
        $this->userRepository = $userRepository;

        $normalizers = [new UserNormalizer()];
        $serializer = new Serializer($normalizers);
        $this->objectNormalizer = $serializer;
        $this->session = $session;
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
        $user = $this->userRepository->find($id);

        $user->setPassword($passwordEncoder->encodePassword(
                         $user,
                         $request['new_password']
                     ));
        return $this->json([]);
    }
}
