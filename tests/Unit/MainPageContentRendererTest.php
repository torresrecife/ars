<?php

namespace Tests\Unit;

use App\Services\MainPageContentRenderer;
use App\Support\View;
use App\ViewModels\MainPageContent;
use Mockery;
use Tests\TestCase;

class MainPageContentRendererTest extends TestCase
{
    public function test_render_uses_view_renderer_for_view_content()
    {
        $view = Mockery::mock(View::class);
        $view->shouldReceive('render')
            ->once()
            ->with('index/default', array('areas' => array()))
            ->andReturn('<div>ok</div>');

        $renderer = new MainPageContentRenderer($view);

        $result = $renderer->render(
            MainPageContent::forView('index/default', array('areas' => array())),
            array(),
            array()
        );

        $this->assertSame('<div>ok</div>', $result);
    }

    public function test_render_returns_empty_string_for_unknown_content_type()
    {
        $view = Mockery::mock(View::class);
        $renderer = new MainPageContentRenderer($view);

        $content = new \ReflectionClass(MainPageContent::class);
        $instance = $content->newInstanceWithoutConstructor();

        $typeProperty = $content->getProperty('type');
        $typeProperty->setAccessible(true);
        $typeProperty->setValue($instance, 'unknown');

        $viewProperty = $content->getProperty('view');
        $viewProperty->setAccessible(true);
        $viewProperty->setValue($instance, '');

        $dataProperty = $content->getProperty('data');
        $dataProperty->setAccessible(true);
        $dataProperty->setValue($instance, array());

        $controllerProperty = $content->getProperty('controller');
        $controllerProperty->setAccessible(true);
        $controllerProperty->setValue($instance, '');

        $spacerProperty = $content->getProperty('spacer');
        $spacerProperty->setAccessible(true);
        $spacerProperty->setValue($instance, false);

        $this->assertSame('', $renderer->render($instance, array(), array()));
    }
}
