<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    #[Route(
        '/{reactRoute}',
        name: 'app_default',
        defaults: ['reactRoute' => null],
        requirements: ['reactRoute' => '.+'],
        priority: -1
    )]
    public function index(?string $reactRoute, Request $request): Response
    {
        if (str_starts_with($reactRoute, 'api/') || $reactRoute === 'api') {
            $request->setRequestFormat('json');
            throw $this->createNotFoundException();
        }

        return $this->render('default/index.html.twig');
    }
}
