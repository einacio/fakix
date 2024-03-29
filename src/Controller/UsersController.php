<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\GroupRepository;
use App\Repository\UserRepository;
use App\Serializer\Normalizer\UserNormalizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

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
     * @var GroupRepository
     */
    private $groupRepository;

    public function __construct(
        UserRepository $userRepository,
        GroupRepository $groupRepository
    ) {
        $this->userRepository = $userRepository;

        $normalizers = [new UserNormalizer()];
        $serializer = new Serializer($normalizers);
        $this->objectNormalizer = $serializer;
        $this->groupRepository = $groupRepository;
    }

    public function addGroup($id, Request $request)
    {
        if (!$this->getUser()->hasRole('ROLE_ADMIN')) {
            return $this->json(null, Response::HTTP_FORBIDDEN);
        }

        $user = $this->userRepository->findOrFail($id);

        $group = $this->groupRepository->findOrFail($request->request->get('group'));

        $user->addGroup($group);

        $this->getDoctrine()->getManager()->flush();

        return $this->json(null);

    }

    public function delete($id)
    {
        if (!$this->getUser()->hasRole('ROLE_ADMIN')) {
            return $this->json(null, Response::HTTP_FORBIDDEN);
        }

        $entityManager = $this->getDoctrine()->getManager();

        $user = $this->userRepository->findOrFail($id);
        $entityManager->remove($user);
        $entityManager->flush();

        return $this->json(null, Response::HTTP_CREATED);
    }

    public function create(
        Request $request,
        UserPasswordEncoderInterface $passwordEncoder,
        ValidatorInterface $validator
    ) {
        if (!$this->getUser()->hasRole('ROLE_ADMIN')) {
            return $this->json(null, Response::HTTP_FORBIDDEN);
        }


        if ($this->userRepository->find($request->request->get('username'))) {
            return $this->json(['message' => 'User already exists'], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $entityManager = $this->getDoctrine()->getManager();

        $user = new User();
        $user->setName($request->request->get('username'));
        $user->setPassword($request->request->get('password'), $passwordEncoder);
        $user->setIsAdmin(strtolower($request->request->get('isAdmin')) === 'y');
        $user->setApiToken('');

        $errors = $validator->validate($user);
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

        $entityManager->persist($user);
        $entityManager->flush();

        return $this->json(null, Response::HTTP_CREATED);
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
        if (!$this->getUser()->hasRole('ROLE_ADMIN')) {
            return $this->json(null, Response::HTTP_FORBIDDEN);
        }

        $user = $this->userRepository->findOrFail($id);

        $group = $this->groupRepository->findOrFail($groupId);

        $user->removeGroup($group);

        $this->getDoctrine()->getManager()->flush();

        return $this->json(null);
    }

    /**
     * @param int|string $id
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @throws \Symfony\Component\Serializer\Exception\ExceptionInterface
     */
    public function show($id)
    {
        if ($id === '0') {
            $id = $this->getUser()->getId();
        }

        return $this->json(
            $this->objectNormalizer->normalize(
                $this->userRepository->findOrFail($id),
                UserNormalizer::AS_OBJECT
            )
        );
    }

    public function update($id, Request $request, UserPasswordEncoderInterface $passwordEncoder, ValidatorInterface $validator)
    {
        if (!$this->getUser()->hasRole('ROLE_ADMIN')) {
            return $this->json(null, Response::HTTP_FORBIDDEN);
        }

        $user = $this->userRepository->findOrFail($id);

        $user->setPassword($request->query->get('new_password'), $passwordEncoder);

        $errors = $validator->validate($user);
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

        $this->getDoctrine()->getManager()->flush();

        return $this->json(null, Response::HTTP_OK);
    }
}
