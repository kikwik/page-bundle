<?php

namespace Kikwik\PageBundle\Controller;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Kikwik\PageBundle\Builder\PageBuilderFactory;
use Kikwik\PageBundle\Form\PageFormType;
use Kikwik\PageBundle\Repository\PageRepository;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Twig\Environment;

class AdminController
{
    public function __construct(
        private Environment                   $twig,
        private UrlGeneratorInterface         $urlGenerator,
        private Registry                      $doctrine,
        private PageRepository                $pageRepository,
        private FormFactory                   $formFactory,
        private RequestStack                  $requestStack,
        private AuthorizationCheckerInterface $authorizationChecker,
        private PageBuilderFactory            $pageBuilderFactory,
        private array                         $enabledLocales,
        private string                        $adminRole,
    )
    {
    }

    public function pageList(): Response
    {
        $this->checkPermission();

        $pages = $this->pageRepository
            ->createQueryBuilder('p')
            ->orderBy('p.lft', 'ASC')
            ->getQuery()
            ->getResult();

        return $this->render('@KikwikPage/admin/page/list.html.twig', [
            'pages' => $pages,
            'enabledLocales' => $this->enabledLocales,
        ]);
    }

    public function pageCreate(Request $request, ?int $parentId = null): Response
    {
        $this->checkPermission();
        $parent = null;

        $pageBuilder = $this->pageBuilderFactory->createPageBuilder();

        if($parentId)
        {
            // child page
            $parent = $this->pageRepository->find($parentId);
            if(!$parent)
            {
                $this->addFlash('danger', 'Pagina padre non trovata.');
                return $this->redirectToRoute('kikwik_page_admin_page_list');
            }
            $pageBuilder->setParent($parent);
        }
        else
        {
            // home page
            if($this->pageRepository->count([]))
            {
                $this->addFlash('danger', 'La homepage esiste già.');
                return $this->redirectToRoute('kikwik_page_admin_page_list');
            }
            $pageBuilder->setParent(null);
            $pageBuilder->setName('Homepage');
        }

        $page = $pageBuilder->getPage();

        $form = $this->formFactory->create(PageFormType::class, $page);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $page = $form->getData();
            $this->doctrine->getManager()->persist($page);
            $this->doctrine->getManager()->flush();
            $this->addFlash('success', 'La pagina è stata creata.');
            return $this->redirectToRoute('kikwik_page_admin_page_update', ['id' => $page->getId()]);
        }

        return $this->render('@KikwikPage/admin/page/create.html.twig', [
            'form' => $form->createView(),
            'parent'=>$parent,
        ]);
    }

    public function pageUpdate(Request $request, int $id): Response
    {
        $this->checkPermission();

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
                $this->addFlash('success', 'La pagina è stata modificata.');
                return $this->redirectToRoute('kikwik_page_admin_page_update', ['id' => $page->getId()]);
            }

            return $this->render('@KikwikPage/admin/page/update.html.twig', [
                'form' => $form->createView(),
                'page'=>$page,
                'enabledLocales' => $this->enabledLocales,
            ]);
        }

        return $this->redirectToRoute('kikwik_page_admin_page_list');
    }

    public function pageDelete(Request $request, int $id): Response
    {
        $this->checkPermission();

        $page = $this->pageRepository->find($id);
        if($page)
        {
            if($request->getMethod() == 'POST')
            {
                $this->doctrine->getManager()->remove($page);
                $this->doctrine->getManager()->flush();
                $this->addFlash('success', 'La pagina è stata eliminata.');
            }
            else
            {
                $children = $this->pageRepository->getChildren($page);
                return $this->render('@KikwikPage/admin/page/delete.html.twig', [
                    'page' => $page,
                    'children'=>$children,
                ]);
            }
        }
        else
        {
            $this->addFlash('danger', 'Pagina non trovata.');
        }
        return $this->redirectToRoute('kikwik_page_admin_page_list');
    }



    /**********************************/
    /* HELPERS                        */
    /**********************************/

    public function addFlash(string $type, string $message): void
    {
        $this->requestStack->getSession()->getFlashBag()->add($type, $message);
    }

    private function checkPermission(): void
    {
        if($this->adminRole && !$this->authorizationChecker->isGranted($this->adminRole))
        {
            throw new AccessDeniedHttpException('Acces denied checking role '.$this->adminRole);
        }
    }

    private function render(string $template, array $parameters = []): Response
    {
        return new Response($this->twig->render($template, $parameters));
    }

    private function redirectToRoute(string $routeName, array $parameters = []): RedirectResponse
    {
        return new RedirectResponse($this->urlGenerator->generate($routeName, $parameters));
    }
}