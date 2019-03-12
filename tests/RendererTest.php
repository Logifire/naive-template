<?php
namespace NanoTemplate\Test;

use NanoTemplate\Exception\TemplateException;
use NanoTemplate\Renderer;
use NanoTemplate\Test\Model\Admin\Login;
use NanoTemplate\Test\Model\MissingTemplate;
use NanoTemplate\Test\Model\TemplateError;
use NanoTemplate\Test\Model\Welcome;
use PHPUnit\Framework\TestCase;

class RendererTest extends TestCase
{

    /**
     * @var Renderer
     */
    private $renderer;

    /**
     * This method is called before each test.
     */
    protected function setUp(): void
    {
        $view_namespace = 'NanoTemplate\Test\Model';
        $template_path = __DIR__ . '/templates';
        $this->renderer = new Renderer($view_namespace, $template_path);
    }

    public function testRenderer()
    {
        $renderer = $this->renderer;
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
        new Renderer('NanoTemplate', '/invalid/path');
    }

    public function testNamespaceMismatch()
    {
        $this->expectException(TemplateException::class);
        $this->expectExceptionCode(Renderer::NAMESPACE_MISMATCH);

        $renderer = $this->renderer;

        $mismatch = new Class {
            
        };
        $renderer->capture($mismatch);
    }

    public function testMissingTemplate()
    {
        $this->expectException(TemplateException::class);
        $this->expectExceptionCode(Renderer::MISSING_TEMPLATE);

        $renderer = $this->renderer;
        $missing_tpl_view = new MissingTemplate();
        $renderer->capture($missing_tpl_view);
    }

    public function testTemplateError()
    {
        $this->expectException(TemplateException::class);
        $this->expectExceptionCode(Renderer::TEMPLATE_ERROR);

        $renderer = $this->renderer;
        $tpl_error_view = new TemplateError();
        $renderer->capture($tpl_error_view);
    }

    public function testRendererPrint()
    {
        $renderer = $this->renderer;
        $welcome_view = new Welcome();

        ob_start();

        $renderer->print($welcome_view);

        $content_default = ob_get_clean();

        $this->assertSame('Welcome', $content_default);
    }

    public function testPrintTemplateError()
    {
        $this->expectException(TemplateException::class);
        $this->expectExceptionCode(Renderer::TEMPLATE_ERROR);

        $renderer = $this->renderer;
        $tpl_error_view = new TemplateError();
        $renderer->print($tpl_error_view);
    }
}
