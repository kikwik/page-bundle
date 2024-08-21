<?php

namespace Kikwik\PageBundle\Controller;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Kikwik\PageBundle\Entity\Page;
use Kikwik\PageBundle\Repository\PageRepository;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Environment;

class AdminController
{
    public function __construct(
        private Environment            $twig,
        private UrlGeneratorInterface  $urlGenerator,
        private Registry               $doctrine,
        private PageRepository         $pageRepository,
        private array                  $enabledLocales,
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

    public function create(?int $parentId = null): Response
    {
        if($parentId)
        {
            $parent = $this->pageRepository->find($parentId);

            $page = new Page();
            $page->setName('page'.rand(100,999));
            $page->setParent($parent);

            $this->doctrine->getManager()->persist($page);
            $this->doctrine->getManager()->flush();
        }
        elseif(count($this->pageRepository->findAll()) == 0)
        {
            $page = new Page();
            $page->setName('homepage');
            foreach($this->enabledLocales as $locale)
            {
                $page->getTranslation($locale);
            }
            $this->doctrine->getManager()->persist($page);
            $this->doctrine->getManager()->flush();
        }


        return new RedirectResponse($this->urlGenerator->generate('kikwik_page_admin_list'));
    }

    public function edit(string $_locale, int $id): Response
    {
        $page = $this->pageRepository->find($id);
        if($page)
        {
            // TODO: create form
        }

        return new RedirectResponse($this->urlGenerator->generate('kikwik_page_admin_list'));
    }
}