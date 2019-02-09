<?php
namespace NaiveTemplate;

use NaiveTemplate\Exception\TemplateException;
use Throwable;

/**
 * This renderer maps a view model to a PHP template with the 
 * extension ".tpl.php".
 * The search path for templates is set in the constructor argument.
 */
class Renderer
{

    public const INVALID_ROOT_PATH = 1;
    public const NAMESPACE_MISMATCH = 2;
    public const MISSING_TEMPLATE = 3;
    public const TEMPLATE_ERROR = 4;

    /**
     * @var string
     */
    private $view_namespace;

    /**
     * @var string
     */
    private $template_root_path;

    /**
     * @param string $view_namespace View namespace
     * @param string $template_root_path  Path to the templates
     */
    public function __construct(string $view_namespace, string $template_root_path)
    {

        if (!is_dir($template_root_path)) {
            throw new TemplateException("Invalid template root path: {$template_root_path}", self::INVALID_ROOT_PATH);
        }

        $this->view_namespace = $view_namespace;
        $this->template_root_path = $template_root_path;
    }

    private function namespaceToPath(string $class_name): string
    {
        $view_model_namespace = substr($class_name, strlen($this->view_namespace));

        //Namespaces should follow the path structure, converts namespaces to path
        $template_path = str_replace('\\', DIRECTORY_SEPARATOR, $view_model_namespace);

        return $template_path;
    }

    private function getTemplate($view, string $suffix): string
    {
        $class_name = get_class($view);

        if (strpos($class_name, $this->view_namespace) === false) {
            $msg = "This Renderer is configured to render templates from the following namespace: \"{$this->view_namespace}\". ";
            $msg .= "View model received: \"{$class_name}\".";
            throw new TemplateException($msg, self::NAMESPACE_MISMATCH);
        }

        $template_path = $this->namespaceToPath($class_name);

        $template_full_path = $this->template_root_path . $template_path . ".{$suffix}.php";

        if (!file_exists($template_full_path)) {
            throw new TemplateException(
                'Couldn\'t find a template for view model: "' . get_class($view) .
                '" Search path: "' . $template_full_path . '"'
                , self::MISSING_TEMPLATE);
        }

        return $template_full_path;
    }

    public function capture($view, string $suffix = 'tpl'): string
    {
        $template = $this->getTemplate($view, $suffix);

        ob_start();

        try {
            require $template;
        } catch (Throwable $e) {
            throw new TemplateException("Error in template: {$template}", self::TEMPLATE_ERROR, $e);
        } finally {
            $content = ob_get_clean();
        }

        return $content;
    }

    public function print($view, string $suffix = 'tpl'): void
    {
        $template = $this->getTemplate($view, $suffix);

        try {
            require $template;
        } catch (Throwable $e) {
            throw new TemplateException("Error in template: {$template}", self::TEMPLATE_ERROR, $e);
        }
    }
}
