# nano-template

This template library maps view models to PHP template files.
The default naming of the template files is *.tpl.php, but the suffix can be changed. Eg. Welcome.en.php and Welcome.de.php, if you want to have templates with different languages.

## Usage
**Basic**
```
    $view_namespace = 'NanoTemplate\Test\Model';
    $template_path = __DIR__ . '/templates';
    $renderer = new Renderer($view_namespace, $template_path);
    
    $view = new Welcome();
    
    $content = $renderer->capture($view);
    
    // $content = $renderer->capture($view, 'en'); If you want to use another suffix
    ... 
```
Add the content to the PSR-7 response model and emit the response.

If you are not using response models, you can also print the template directly: `$renderer->print($view)`
