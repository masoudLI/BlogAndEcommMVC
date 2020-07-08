<?php

namespace Tests\Renderer;

use Framework\Renderer\PHPRenderer;
use PHPUnit\Framework\TestCase;

class RendererTest extends TestCase 
{
  protected function setUp(): void
  {
    parent::setUp();
    $this->renderer = new PHPRenderer();
    $this->renderer->addPath(__DIR__ . '/views');
  }


  public function testRenderTheRighPath() {
    $this->renderer->addPath('blog', __DIR__ . '/views');
    $content = $this->renderer->render('@blog/demo', []);
    $this->assertEquals('salut les gens', $content);
  }

  public function testRenderTheDefaultsPath()
  {
    $content = $this->renderer->render('demo', []);
    $this->assertEquals('salut les gens', $content);
  }

  public function testRenderWithParams()
  {
    $content = $this->renderer->render('demoparams', ['nom' => 'massoud']);
    $this->assertEquals('salut massoud', $content);
  }

  public function testRenderWithAddGlobal()
  {
    $this->renderer->addGlobal('nom', 'massoud');
    $content = $this->renderer->render('demoparams');
    $this->assertEquals('salut massoud', $content);
  }
  
}
