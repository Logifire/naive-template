<?php
namespace NaiveTemplate\Test;

use NaiveTemplate\Exception\TemplateException;
use NaiveTemplate\Renderer;
use NaiveTemplate\Test\Model\Admin\Login;
use NaiveTemplate\Test\Model\MissingTemplate;
use NaiveTemplate\Test\Model\Welcome;
use PHPUnit\Framework\TestCase;

class RendererTest extends TestCase
{

    public function testRenderer()
    {
        $view_namespace = 'NaiveTemplate\Test\Model';
        $template_path = __DIR__ . '/templates';
        $renderer = new Renderer($view_namespace, $template_path);
        $welcome_view = new Welcome();
        $content_default = $renderer->capture($welcome_view);
        $this->assertSame('Welcome', $content_default);

        $content_dk = $renderer->capture($welcome_view, 'dk');
        $this->assertSame($content_dk, 'Velkommen');

        $login_view = new Login();
        $content_login = $renderer->capture($login_view);
        $this->assertSame($content_login, 'Login');
    }

    public function testInvalidRootPath()
    {
        $this->expectException(TemplateException::class);
        $this->expectExceptionCode(Renderer::INVALID_ROOT_PATH);
        new Renderer('NaiveTemplate', '/invalid/path');
    }

    public function testNamespaceMismatch()
    {
        $this->expectException(TemplateException::class);
        $this->expectExceptionCode(Renderer::NAMESPACE_MISMATCH);

        $view_namespace = 'NaiveTemplate\Test\Model';
        $template_path = __DIR__ . '/templates';
        $renderer = new Renderer($view_namespace, $template_path);

        $mismatch = new Class {
            
        };
        $renderer->capture($mismatch);
    }

    public function testMissingTemplate()
    {
        $this->expectException(TemplateException::class);
        $this->expectExceptionCode(Renderer::MISSING_TEMPLATE);

        $view_namespace = 'NaiveTemplate\Test\Model';
        $template_path = __DIR__ . '/templates';
        $renderer = new Renderer($view_namespace, $template_path);
        $missing_tpl_view = new MissingTemplate();
        $renderer->capture($missing_tpl_view);
    }
}
