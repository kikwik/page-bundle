<?php

namespace Kikwik\PageBundle\Controller;

use Kikwik\PageBundle\Entity\PageTranslation;
use Kikwik\PageBundle\Service\BlockLoader;
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
        return new Response($this->twig->render('@KikwikPage/page/show.html.twig', [
            'pageTranslation' => $pageTranslation,
        ]));
    }



}