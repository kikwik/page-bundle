Kikwik/PageBundle
=================

Manage pages for symfony 6.4+

## Installation

1. require the bundle

```console
#!/bin/bash
composer require kikwik/page-bundle
```

2. Define the `enabled_locales` in `config/packages/translation.yaml`

```yaml
framework:
  default_locale: it
  enabled_locales: ['it','en','de','fr']
```


3. Optionally configure options in `config/packages/kikwik_page.yaml`

```yaml
kikwik_page:
  admin_role: 'ROLE_ADMIN_PAGE'   # set to empty string to disable permission checker
  default_locale: '%kernel.default_locale%'
  enabled_locales: '%kernel.enabled_locales%'
```

### Page admin ###

To activate the page admin feature add routes in `config/routes/kikwik_pages.yaml`:

```yaml
kikwik_page_bundle_admin:
    resource: '@KikwikPageBundle/config/routes.xml'
    prefix: '/admin/page'
```

and create a PageFormLive component in `src/Twig/Components/PageFormLive.php`:

```php
namespace App\Twig\Components;

use Kikwik\PageBundle\Entity\Page;
use Kikwik\PageBundle\Form\PageFormType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\LiveComponent\LiveCollectionTrait;

#[AsLiveComponent(template: '@KikwikPage/components/PageFormLive.html.twig')]
final class PageFormLive
{
    public function __construct(
        private FormFactoryInterface $formFactory,
    )
    {
    }

    use DefaultActionTrait;
    use LiveCollectionTrait;

    #[LiveProp]
    public ?Page $initialFormData = null;

    protected function instantiateForm(): FormInterface
    {
        return $this->formFactory->create(PageFormType::class, $this->initialFormData);
    }
}
```


## Block

A block renderer must be a TwigComponent that implements `Kikwik\PageBundle\Block\BlockComponentInterface`
You can extend `BaseBlockComponent` and use `$this->getBlock()` to retrieve the Block entity and `$this->get('paramName')`
to get the parameter value.
The `buildEditForm` will be used in admin to create the form that will edit parameters 

```php
namespace App\Twig\Components;

use Kikwik\PageBundle\Block\BaseBlockComponent;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormInterface;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
class Alert extends BaseBlockComponent
{
   
    public function buildEditForm(FormInterface $form): void
    {
        $form
            ->add('type', ChoiceType::class, [
                'choices' => ['success' => 'success', 'info' => 'info', 'warning' => 'warning', 'danger' => 'danger'],
            ])
            ->add('message',TextareaType::class, [])
        ;
    }
}
```

```twig
<div class="alert alert-{{ this.get('type') | default('success') }}">
    {{ this.get('message') | default('default message') }}
</div>
```

## Child pages

Page rendering will be handled by the internal controller `Kikwik\PageBundle\Controller\PageController`,
If a URL that partially matches a page contains extra slug parts, an event will be triggered, 
and you can set a Response to display a different template. 
If your listener does not set the response, a 404 error will be thrown.

```php
namespace App\EventListener;

use Kikwik\PageBundle\Event\PageExtraSlugEvent;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

#[AsEventListener(event: PageExtraSlugEvent::NAME, method: 'onPageExtraSlug')]
class PageExtraSlugListener
{

    public function __construct(
        private Environment $twig,
    )
    {
    }

    public function onPageExtraSlug(PageExtraSlugEvent $event)
    {
        $pageTranslation = $event->getPageTranslation();
        $extraSlug = $event->getExtraSlug();
        
        if($extraSlug == 'some string that matches')
        {
            $response = new Response($this->twig->render('template/page/childPage.html.twig', [
                    'parentPageTranslation' => $pageTranslation,
                ]));
            $event->setResponse($response);
        }
    }
}
```