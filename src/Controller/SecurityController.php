<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Security\TokenAuthenticator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class SecurityController extends AbstractController
{

    public function login(
        Request $request,
        UserRepository $userRepository,
        TokenAuthenticator $tokenAuthenticator,
        UserPasswordEncoderInterface $passwordEncoder
    ) {
        /** @var User $user */
        $user = $this->getUser();

        if (!$user) {
            $valid = false;
            try {
                $user = $userRepository->findOrFail($request->request->get('username'));
                if ($user->checkPassword($request->request->get('password'), $passwordEncoder)) {
                    $valid = true;
                    $user->setApiToken(
                        //replace sha1 token with sensible option
                        sha1($tokenAuthenticator->createAuthenticatedToken($user, 'jsonlogin')->serialize())
                    );
                    $this->getDoctrine()->getManager()->flush();
                }
            } catch (NotFoundHttpException $ex) {

            }

            if (!$valid) {
                return $this->json(["message" => "Authentication failed."], Response::HTTP_UNAUTHORIZED);
            }

        }

        //if($auth->checkCredentials())
        return $this->json(["user"=>$user->getName(), 'isAdmin' => $user->hasRole('ROLE_ADMIN'), "token" => $user->getApiToken()]);
    }

    public function firstUse(
        Request $request,
        UserRepository $userRepository,
        UserPasswordEncoderInterface $passwordEncoder
    ) {
        if ($userRepository->findAll()) {
            //if there's any user, simply fail
            return $this->json(["message" => "Error"], Response::HTTP_BAD_REQUEST);
        }

        $entityManager = $this->getDoctrine()->getManager();

        $user = new User();
        $user->setName($request->request->get('username'));
        $user->setIsAdmin(true);
        $user->setPassword($request->request->get('password'), $passwordEncoder);
        $user->setApiToken('');
        $entityManager->persist($user);
        $entityManager->flush();

        return $this->json(null, Response::HTTP_CREATED);
    }
}
