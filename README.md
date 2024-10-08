Kikwik/PageBundle
=================

Manage pages with translations for symfony 6.4+

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
  resolve_target_entities:
    page: App\Entity\Pages\Page
    page_translation: App\Entity\Pages\PageTranslation
    block: App\Entity\Pages\Block

```

4. Define your entity and make them extends base classes


```php
// src/Entity/Pages/Page.php
namespace App\Entity\Pages;

use Doctrine\ORM\Mapping as ORM;
use Kikwik\PageBundle\Entity\AbstractPage;
use Kikwik\PageBundle\Model\PageInterface;

#[ORM\Entity()]
#[ORM\Table(name: 'kw_page__page')]
class Page extends AbstractPage implements PageInterface
{
}
```

```php
// src/Entity/Pages/PageTranslation.php
namespace App\Entity\Pages;

use Doctrine\ORM\Mapping as ORM;
use Kikwik\PageBundle\Entity\AbstractPageTranslation;
use Kikwik\PageBundle\Model\PageTranslationInterface;

#[ORM\Entity()]
#[ORM\Table(name: 'kw_page__page_translation')]
class PageTranslation extends AbstractPageTranslation implements PageTranslationInterface
{
}
```

```php
// src/Entity/Pages/Block.php
namespace App\Entity\Pages;

use Doctrine\ORM\Mapping as ORM;
use Kikwik\PageBundle\Entity\AbstractBlock;
use Kikwik\PageBundle\Model\BlockInterface;

#[ORM\Entity()]
#[ORM\Table(name: 'kw_page__block')]
class Block extends AbstractBlock implements BlockInterface
{
}
```

5. Clear your cache and update databse

```shell
symfony console cache:clear
symfony console doctrine:schema:update --force
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

use App\Entity\Pages\Page;
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
        private MyCustomController $customController,
    )
    {
    }

    public function onPageExtraSlug(PageExtraSlugEvent $event)
    {
        $pageTranslation = $event->getPageTranslation();
        $extraSlug = $event->getExtraSlug();
        
        if($extraSlug == 'some string that matches')
        {
            $response = $this->customController->customAction($pageTranslation);
            $event->setResponse($response);
        }
    }
}
```