<?php

class AuthorPage_Test extends PHPUnit_Framework_TestCase
{
    protected $authorPage;

    public function setUp()
    {
        \WP_Mock::setUp();
        $this->authorPage = new \DHIntranet\AuthorPage();
    }

    public function tearDown()
    {
        \WP_Mock::tearDown();
    }

    public function testRegistrable()
    {
        $this->assertInstanceOf(\Dxw\Iguana\Registerable::class, $this->authorPage);
    }

    public function testRegister()
    {
        \WP_Mock::expectActionAdded('template_redirect', [$this->authorPage, 'disable']);
        \WP_Mock::expectFilterAdded('author_link', [$this->authorPage, 'removeAuthorLink']);

        $this->authorPage->register();
    }

    public function testDisableOnAuthorPage()
    {
        WP_Mock::wpFunction('is_author', [
            'times' => 1,
            'return' => true
        ]);

        global $wp_query;
        $wp_query = $this->getMockBuilder('WP_Query')->setMethods(['set_404'])->getMock();
        $wp_query->expects($this->exactly(1))
               ->method('set_404');

        WP_Mock::wpFunction('status_header', [
            'times' => 1,
            'args' => [404],
            'return' => true
        ]);

        $this->authorPage->disable();
    }

    public function testDoesNothingIfNotAuthorPage()
    {
        WP_Mock::wpFunction('is_author', [
            'times' => 1,
            'return' => false
        ]);

        global $wp_query;
        $wp_query = $this->getMockBuilder('WP_Query')->setMethods(['set_404'])->getMock();
        $wp_query->expects($this->exactly(0))
            ->method('set_404');

        $this->authorPage->disable();
    }

    public function testAuthorLinkRemoved()
    {
        $this->assertEquals('', $this->authorPage->removeAuthorLink('http://this.is/author/link'));
    }
}
