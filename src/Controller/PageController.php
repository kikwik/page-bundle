<?php

namespace Kikwik\PageBundle\Controller;

use Kikwik\PageBundle\Entity\PageTranslation;
use Kikwik\PageBundle\Event\PageExtraSlugEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Twig\Environment;

class PageController
{
    public function __construct(
        private Environment $twig,
        private EventDispatcherInterface $eventDispatcher,
    )
    {
    }

    public function show(Request $request, PageTranslation $pageTranslation, string $extraSlug = ''): Response
    {
        $request->setLocale($pageTranslation->getLocale());

        if($extraSlug)
        {
            $event = new PageExtraSlugEvent($pageTranslation, $extraSlug);
            $this->eventDispatcher->dispatch($event, PageExtraSlugEvent::NAME);

            if ($event->getResponse() !== null) {
                return $event->getResponse();
            } else {
                throw new NotFoundHttpException('Page not found');
            }
        }

        return new Response($this->twig->render('@KikwikPage/page/show.html.twig', [
            'pageTranslation' => $pageTranslation,
        ]));
    }

}