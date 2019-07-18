<?php


namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class IndexController extends AbstractController
{
public function index(){



    return new Response(<<<'HTML'
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
            i18n: { welcome : 'Terminal #'+1 }
        });
</script>
<script src="/assets/js/commands.js"></script>
</html>
HTML
);
}
}
