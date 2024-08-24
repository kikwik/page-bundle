Kikwik/PageBundle
==================================

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

## Block

A block renderer must be a TwigComponent that implements `Kikwik\PageBundle\Block\BlockComponentInterface`
You can extends `BaseBlockComponent`

```php
namespace App\Twig\Components;

use Kikwik\PageBundle\Block\BaseBlockComponent;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
class Alert extends BaseBlockComponent
{
    public function getDefaultValues(): array
    {
        return [
            'type'=>'success',
            'message'=>'Hello!',
        ];
    }
}
```

```twig
<div class="alert alert-{{ this.get('type') | default('success') }}">
    {{ this.get('message') | default('default message') }}
</div>
```
