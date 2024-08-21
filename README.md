Kikwik/PageBundle
==================================

Manage pages for symfony 6.4+

## Installation

1. require the bundle

```console
#!/bin/bash
composer require kikwik/page-bundle
```

### Page admin ###

To activate the page admin feature add routes in `config/routes/kikwik_pages.yaml`:

```yaml
kikwik_page_bundle_admin:
    resource: '@KikwikPageBundle/config/routes.xml'
    prefix: '/admin/page'
```

## Usage


