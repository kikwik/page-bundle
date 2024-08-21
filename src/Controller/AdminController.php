<?php

namespace Kikwik\PageBundle\Controller;

use Kikwik\PageBundle\Repository\PageRepository;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

class AdminController
{
    public function __construct(
        private Environment $twig,
        private PageRepository $pageRepository,
        private array $enabledLocales,
    )
    {
    }

    public function list(string $_locale): Response
    {
        $pages = $this->pageRepository
            ->createQueryBuilder('p')
            ->orderBy('p.lft', 'ASC')
            ->getQuery()
            ->getResult();

        return new Response($this->twig->render('@KikwikPage/admin/list.html.twig', [
            'pages' => $pages,
            'selectedLocale' => $_locale,
            'enabledLocales' => $this->enabledLocales,
        ]));
    }
}