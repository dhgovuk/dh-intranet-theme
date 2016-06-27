<?php

class PagesTest extends PHPUnit_Framework_TestCase
{
    use \Dxw\Assertions\HTML;

    public function setUp()
    {
        \WP_Mock::setUp();

        \WP_Mock::wpFunction('esc_html', [
            'return' => function ($a) { return htmlspecialchars($a); },
        ]);

        \WP_Mock::wpFunction('esc_attr', [
            'return' => function ($a) { return htmlspecialchars($a); },
        ]);
    }

    public function tearDown()
    {
        \WP_Mock::tearDown();
    }

    public function testGetTheBreadcrumbs1()
    {
        \WP_Mock::wpFunction('get_bloginfo', [
         'args' => ['url'],
         'return' => 'y',
        ]);

        \WP_Mock::wpFunction('get_post_ancestors', [
         'args' => [7],
         'return' => [
         ],
        ]);

        \WP_Mock::wpFunction('get_the_ID', [
         'args' => [],
         'return' => 7,
        ]);

        $output = \DHIntranet\Pages::get_the_breadcrumbs();

        $this->assertHTMLEquals('', $output, true);
    }

    public function testGetTheBreadcrumbs2()
    {
        \WP_Mock::wpFunction('get_bloginfo', [
             'args' => ['url'],
             'return' => 'z',
         ]);

        \WP_Mock::wpFunction('get_post_ancestors', [
            'args' => [7],
            'return' => [
                8,
                9,
            ],
        ]);

        \WP_Mock::wpFunction('get_the_ID', [
            'args' => [],
            'return' => 7,
        ]);

        \WP_Mock::wpFunction('get_the_title', [
            'args' => [8],
            'return' => 'Title1',
        ]);

        \WP_Mock::wpFunction('get_the_title', [
            'args' => [9],
            'return' => 'Title2',
        ]);

        \WP_Mock::wpFunction('get_permalink', [
            'args' => [8],
            'return' => 'link1',
        ]);

        \WP_Mock::wpFunction('get_permalink', [
            'args' => [9],
            'return' => 'link2',
        ]);

        $output = \DHIntranet\Pages::get_the_breadcrumbs();

        $this->assertHTMLEquals('
        <li><a href="link2">Title2</a></li>
        <li><a href="link1">Title1</a></li>
        ', $output, true);
    }
}
