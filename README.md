# Pimcore Vite Bundle

This bundle adds a Service to your [Pimcore](https://github.com/pimcore/pimcore) project, which can resolve vite assets.

## Installation

### Using composer

```
composer require carbdrox/pimcore-vite-bundle
```
### Enable the Bundle

```
bin/console pimcore:bundle:enable ViteBundle
```

## Usage

To resolve your vite assets inside twig templates you can use the function `vite('asset/path')` which accepts the asset path as parameter.  
```html
<link rel="stylesheet" href="{{ vite('assets/scss/app.scss') }}"/>

<script type="module" defer src="{{ vite('assets/js/app.js') }}"></script>
```

If you need to resolve your asset inside php code, you need to inject the Service `\ViteBundle\Services\ViteService::class`
then you can use the method `getAsset('asset/path')` from this service, which also accepts the asset path as parameter.

```php
class DefaultController extends FrontendController
{
    public function defaultAction(\ViteBundle\Services\ViteService $viteService)
    {
        $path = $viteService->getAsset('assets/js/app.js')
    }
}
```

If you want to use the hot reload functionality, you need to insert the following snippet at the bottom of your twig template.  
It will insert the required script which is required by vite to enable the hot reloading.  
The script tag will only be inserted if the APP_ENV is 'dev' and you the template is not opened in editmode.
```html
    {{ viteReload(editmode) | raw }}
    </body>
</html>
```

## Configuration

#### Automatic vite configuration
It is highly recommended to use the [pimcore-vite-plugin](http://npmjs.com/package/pimcore-vite-plugin) in combination 
with this bundle. It will configure your vite to work in harmony with this bundle. 

#### Manual vite configuration
If you prefer to write your vite config yourself, you need to provide some files for this bundle to work correctly.  

The bundle requires you to provide a `manifest.json` file, which contains all the asset paths keyed by their vite path.  
This manifest.json will be loaded from `public/build/manifest.json`.  

If you want to make use of the hot reload functionality, you need to provide a file named `vite-serve` in your public 
folder whenever your vite dev server is running.  
This file must contain the url of your vite dev server e.g. `http://localhost:5173`. The file must not contain any 
additional text or whitespaces.

## Contributing

Thank you for considering contributing! The contribution guide can be found in the [CONTRIBUTING.md](CONTRIBUTING.md).

## Code of Conduct

Please review and abide by the [Code of Conduct](CODE_OF_CONDUCT.md).

## License

The Pimcore Vite bundle is licensed under the [MIT license](LICENSE.md).
