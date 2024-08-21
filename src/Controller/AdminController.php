<?php

namespace Kikwik\PageBundle\Controller;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Kikwik\PageBundle\Entity\Page;
use Kikwik\PageBundle\Form\PageFormType;
use Kikwik\PageBundle\Repository\PageRepository;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Environment;

class AdminController
{
    public function __construct(
        private Environment           $twig,
        private UrlGeneratorInterface $urlGenerator,
        private Registry              $doctrine,
        private PageRepository        $pageRepository,
        private FormFactory           $formFactory,
        private RequestStack          $requestStack,
        private array                 $enabledLocales,
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

    public function create(Request $request, ?int $parentId = null): Response
    {
        $page = new Page();
        $parent = null;

        if($parentId)
        {
            // child page
            $parent = $this->pageRepository->find($parentId);
            if(!$parent)
            {
                $this->requestStack->getSession()->getFlashBag()->add('danger', 'Pagina padre non trovata.');
                return new RedirectResponse($this->urlGenerator->generate('kikwik_page_admin_list'));
            }
            $page->setParent($parent);
        }
        else
        {
            // home page
            if($this->pageRepository->count([]))
            {
                $this->requestStack->getSession()->getFlashBag()->add('danger', 'La homepage esiste già.');
                return new RedirectResponse($this->urlGenerator->generate('kikwik_page_admin_list'));
            }
            $page->setParent(null);
            $page->setName('Homepage');
        }

        foreach($this->enabledLocales as $locale)
        {
            $page->getTranslation($locale);
        }

        $form = $this->formFactory->create(PageFormType::class, $page);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $page = $form->getData();
            $this->doctrine->getManager()->persist($page);
            $this->doctrine->getManager()->flush();
            $this->requestStack->getSession()->getFlashBag()->add('success', 'La pagina è stata creata.');
            return new RedirectResponse($this->urlGenerator->generate('kikwik_page_admin_list'));
        }

        return new Response($this->twig->render('@KikwikPage/admin/create.html.twig', [
            'form' => $form->createView(),
            'parent'=>$parent,
        ]));
    }

    public function edit(Request $request, int $id): Response
    {
        $page = $this->pageRepository->find($id);
        if($page)
        {
            $form = $this->formFactory->create(PageFormType::class, $page);
            $form->handleRequest($request);
            if($form->isSubmitted() && $form->isValid())
            {
                $page = $form->getData();
                $this->doctrine->getManager()->persist($page);
                $this->doctrine->getManager()->flush();
                $this->requestStack->getSession()->getFlashBag()->add('success', 'La pagina è stata modificata.');
                return new RedirectResponse($this->urlGenerator->generate('kikwik_page_admin_list'));
            }

            return new Response($this->twig->render('@KikwikPage/admin/edit.html.twig', [
                'form' => $form->createView(),
            ]));
        }

        return new RedirectResponse($this->urlGenerator->generate('kikwik_page_admin_list'));
    }
}