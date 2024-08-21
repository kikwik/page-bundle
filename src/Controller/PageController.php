<?php

namespace Kikwik\PageBundle\Controller;

use Kikwik\PageBundle\Entity\PageTranslation;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

class PageController
{
    public function __construct(
        private Environment $twig,
    )
    {
    }

    public function show(Request $request, PageTranslation $pageTranslation): Response
    {
        // Render your template or return a response based on the PageTranslation entity
        return $this->render('@KikwikPage/page/show.html.twig', [
            'page' => $pageTranslation,
        ]);
    }


    protected function render(string $view, array $parameters = [], Response $response = null): Response
    {
        $content = $this->twig->render($view, $parameters);

        if (null === $response) {
            $response = new Response();
        }

        $response->setContent($content);

        return $response;
    }
}