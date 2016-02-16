<?php

class SearchTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        \WP_Mock::setUp();

        $this->isAdmin = false;
        $this->isSearch = true;
        $this->isMainQuery = true;

        $this->s = new \DHIntranet\Search([]);

        $this->q = $this->getMockBuilder('WP_Query')
        ->setMethods(['is_search', 'set', 'is_main_query'])
        ->getMock();

        $this->q
        ->method('is_search')
        ->will($this->returnCallback(function () {
            return $this->isSearch;
        }));

        $this->q
        ->method('is_main_query')
        ->will($this->returnCallback(function () {
            return $this->isMainQuery;
        }));

        \WP_Mock::wpFunction('is_admin', [
            'return' => function () {
                return $this->isAdmin;
            },
        ]);
    }

    public function tearDown()
    {
        \WP_Mock::tearDown();
    }

    public function testRegister()
    {
        \WP_Mock::expectActionAdded('parse_query', [$this->s, 'parseQuery']);

        $this->s->register();
    }

    public function testParseQuery()
    {
        $this->q
        ->expects($this->exactly(1))
        ->method('set')
        ->with('post_type', ['page', 'event', 'todo-item', 'local-news', 'post']);

        $this->s->parseQuery($this->q);
    }

    public function testParseQueryNotAdmin()
    {
        $this->q
        ->expects($this->exactly(0))
        ->method('set');

        $this->isAdmin = true;

        $this->s->parseQuery($this->q);
    }

    public function testParseQueryOnlySearch()
    {
        $this->q
        ->expects($this->exactly(0))
        ->method('set');

        $this->isSearch = false;

        $this->s->parseQuery($this->q);
    }

    public function testParseQueryOnlyMainQuery()
    {
        $this->q
        ->expects($this->exactly(0))
        ->method('set');

        $this->isMainQuery = false;

        $this->s->parseQuery($this->q);
    }

    public function testParseQueryExcludeNews()
    {
        $this->s = new \DHIntranet\Search([
            'exclude-news' => 'yes',
        ]);

        $this->q
        ->expects($this->exactly(1))
        ->method('set')
        ->with('post_type', ['page', 'event', 'todo-item', 'local-news']);

        $this->s->parseQuery($this->q);
    }
}
