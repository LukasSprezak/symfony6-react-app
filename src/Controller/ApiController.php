<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\{
    Bundle\FrameworkBundle\Controller\AbstractController,
    Component\HttpFoundation\Response,
    Component\Routing\Annotation\Route,
};

final class ApiController extends AbstractController
{
    #[Route(
        path: '/{reactRouting}',
        name: 'app_api_index',
        requirements: ['reactRouting' => '^(?!api).+'],
        defaults: ['reactRouting' => null]
    )]
    public function __invoke(): Response
    {
        return $this->render(view: 'api/index.html.twig');
    }
}
