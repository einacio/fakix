<?php


namespace App\Controller;


use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class IndexController extends AbstractController
{
    public function index(UserRepository $userRepository, UserPasswordEncoderInterface $passwordEncoder)
    {

        if (!$userRepository->findAll()) {
            $entityManager = $this->getDoctrine()->getManager();

            $user = new User();
            $user->setName('root');
            $user->setIsAdmin(true);
            $user->setPassword('root', $passwordEncoder);
            $user->setApiToken('');
            $entityManager->persist($user);
            $entityManager->flush();
        }


        return new Response(
            <<<'HTML'
<!DOCTYPE html>
<html lang="en">
<head>
<title>FAKIX</title>
<script src="/assets/js/jquery-3.4.1.min.js"></script>
<script src="/assets/js/ptty.jquery.min.js"></script>
<style>
html,body{margin:0;padding:0;height: 100%}
</style>
</head>
<div id="term"></div>
<script>
window.$ptty = $('#term').Ptty({
            i18n: { welcome : 'Welcome to the FAKIX system. Use "help" to display available commands.' }
        });
</script>
<script src="/assets/js/commands.js"></script>
</html>
HTML
        );
    }
}
