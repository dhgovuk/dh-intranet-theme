<?php

class Event_Categories
{
    public static function get_categories_and_events()
    {
        return [
            9 => ['a', 'b', 'c'],
        ];
    }
}

class EventListingsTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        \WP_Mock::setUp();

        \WP_Mock::wpFunction('add_shortcode', []);

        \WP_Mock::wpFunction('esc_attr', [
            'return' => function ($a) { return htmlspecialchars($a); },
        ]);

        \WP_Mock::wpFunction('esc_html', [
            'return' => function ($a) { return htmlspecialchars($a); },
        ]);

        \WP_Mock::wpFunction('get_option', [
            'return' => 'Europe/London',
        ]);

        \WP_Mock::wpFunction('get_the_title', [
            'return' => 'Meow',
        ]);

        \WP_Mock::wpFunction('get_permalink', [
            'return' => 'http://localhost/event/meow/',
        ]);
    }

    public function tearDown()
    {
        \WP_Mock::tearDown();
    }

    public $matrix = [
        [
            'input' => [
                'link' => 'https://in.dh.gov.uk/?post_type=event&p=171534',
                'title' => 'Group Operations Experience Week – meet Tamara Finkelstein and the Senior Team – Burnley',
                'start' => 1444644000, // 12 October 2015 11:00 (Europe/London)
                'end' => 1444649400, // 12 October 2015 12:30 (Europe/London)
                'address' => '',
                'description' => 'During Group Operations Experience Week there will be various opportunities for staff to see how the directorate enables DH to deliver on its priorities as well as highlighting some of its products and services. This session ‘Meet the Senior Team’ will enable Tamara Finkelstein (Director General and Chief Operating Officer) and her directors to set out their key priorities and will be an opportunity for all DH staff to meet the directorate’s senior team and ask questions. Gerry Murphy, Chair of the DH Audit Committee, will also talk about his role, responsibilities and experience as both DH Audit Committee Chair',
            ],
            'expected' => '
            <div class="row event_item">
                <div class="border-bottom"><a href="https://in.dh.gov.uk/?post_type=event&#038;p=171534" title="Group Operations Experience Week – meet Tamara Finkelstein and the Senior Team – Burnley">Group Operations Experience Week – meet Tamara Finkelstein and the Senior Team – Burnley</a></div>
                <div class="col-md-6">
                    <p class="border-bottom"><strong>Start time:</strong> 12 October 2015 11:00</p>
                    <p class="border-bottom"><strong>End time:</strong> 12 October 2015 12:30</p>
                    <p class="border-bottom"><strong>Address:</strong> </p>
                </div>
                <div class="col-md-6">
                    <p>During Group Operations Experience Week there will be various opportunities for staff to see how the directorate enables DH to deliver on its priorities as well as highlighting some of its products and services. This session ‘Meet the Senior Team’ will enable Tamara Finkelstein (Director General and Chief Operating Officer) and her directors to set out their key priorities and will be an opportunity for all DH staff to meet the directorate’s senior team and ask questions. Gerry Murphy, Chair of the DH Audit Committee, will also talk about his role, responsibilities and experience as both DH Audit Committee Chair&hellip;<a href="https://in.dh.gov.uk/?post_type=event&#038;p=171534" title="See more about Group Operations Experience Week – meet Tamara Finkelstein and the Senior Team – Burnley"> more </a></p>
                </div>
                <a href="https://in.dh.gov.uk/?post_type=event&#038;p=171534" title="Group Operations Experience Week – meet Tamara Finkelstein and the Senior Team – Burnley" class="btn btn-primary" style="float:right">See more</a>
            </div>
            ',
        ],
        [
            'input' => [
                'link' => 'https://in.dh.gov.uk/event/meow/',
                'title' => 'Group meowing',
                'start' => 1450016100, // 2015-12-13 14:15 (Europe/London)
                'end' => 1450017000, // 2015-12-13 14:30 (Europe/London)
                'address' => '1 Cat Lane',
                'description' => 'Just <b>cat</b> things',
            ],
            'expected' => '
            <div class="row event_item">
                <div class="border-bottom"><a href="https://in.dh.gov.uk/event/meow/" title="Group meowing">Group meowing</a></div>
                <div class="col-md-6">
                    <p class="border-bottom"><strong>Start time:</strong> 13 December 2015 14:15</p>
                    <p class="border-bottom"><strong>End time:</strong> 13 December 2015 14:30</p>
                    <p class="border-bottom"><strong>Address:</strong> 1 Cat Lane</p>
                </div>
                <div class="col-md-6">
                    <p>Just <b>cat</b> things&hellip;<a href="https://in.dh.gov.uk/event/meow/" title="See more about Group meowing"> more </a></p>
                </div>
                <a href="https://in.dh.gov.uk/event/meow/" title="Group meowing" class="btn btn-primary" style="float:right">See more</a>
            </div>
            ',
        ],
    ];

    public function testRenderEvent()
    {
        foreach ($this->matrix as $test) {
            $e = new \DHIntranet\EventListings(true);

            $_expected = new DOMDocument();
            $_expected->loadHTML($test['expected']);
            $_actual = new DOMDocument();
            $_actual->loadHTML($e->renderEvent($test['input']));

            $this->assertEquals($this->_norm($_expected->saveHTML()), $this->_norm($_actual->saveHTML()));
        }
    }

    public function testRenderEvents()
    {
        $input = array_map(function ($a) { return $a['input']; }, $this->matrix);
        $expected = $this->matrix[0]['expected'].'<hr>'.$this->matrix[1]['expected'];

        $e = new \DHIntranet\EventListings(true);

        $_expected = new DOMDocument();
        $_expected->loadHTML($expected);
        $_actual = new DOMDocument();
        $_actual->loadHTML($e->renderEvents($input));

        $this->assertEquals($this->_norm($_expected->saveHTML()), $this->_norm($_actual->saveHTML()));
    }

    public function _norm($h)
    {
        return preg_replace('/^\s+/m', '', $h);
    }

    public function testGetEventIDs()
    {
        $e = new \DHIntranet\EventListings(true);
        $out = $e->getEventIDs([]);
        $this->assertEquals([], $out);

        $out = $e->getEventIDs([
            'cat' => '9',
        ]);
        $this->assertEquals(['a', 'b', 'c'], $out);
    }

    public function testGetEventFromID()
    {
        \WP_Mock::wpFunction('get_post', [
            'return' => (object) [
                'post_content' => 'Cat things',
                'post_status' => 'publish',
            ],
        ]);

        \WP_Mock::wpFunction('get_post_meta', [
            'args' => [123, 'start_date', true],
            'return' => '2015-10-06 10:23:00',
        ]);

        \WP_Mock::wpFunction('get_post_meta', [
            'args' => [123, 'end_date', true],
            'return' => '2015-10-06 11:00:00',
        ]);

        \WP_Mock::wpFunction('get_post_meta', [
            'args' => [123, 'venue', true],
            'return' => 'Cat Kingdom',
        ]);

        \WP_Mock::wpFunction('get_post_meta', [
            'args' => [123, 'adress', true],
            'return' => 'NW1',
        ]);

        \WP_Mock::wpFunction('get_post_meta', [
            'args' => [123, 'timezone', true],
            'return' => 'Europe/London',
        ]);

        $e = new \DHIntranet\EventListings(true);
        $out = $e->getEventFromID(123);
        $this->assertEquals('Meow', $out['title']);
        $this->assertEquals('http://localhost/event/meow/', $out['link']);
        $this->assertEquals('Cat things', $out['description']);
        $this->assertEquals('Cat Kingdom, NW1', $out['address']);
        $this->assertEquals('publish', $out['post_status']);
        // Check that the local dates get converted to UTC
        $this->assertEquals('2015-10-06 09:23:00 UTC', $out['start']);
        $this->assertEquals('2015-10-06 10:00:00 UTC', $out['end']);
    }

    public function testGetCurrentEvents()
    {
        $e = new \DHIntranet\EventListings(true);
        $allEvents = [
            ['id' => 0, 'start' => '2015-10-06 09:23:00', 'post_status' => 'publish'],
            ['id' => 1, 'start' => '2015-10-08 09:23:00', 'post_status' => 'publish'],
            ['id' => 2, 'start' => '2015-10-07 09:23:00', 'post_status' => 'publish'],
            ['id' => 3, 'start' => '2015-10-07 10:23:00', 'post_status' => 'publish'],
            ['id' => 4, 'start' => '2015-10-07 10:23:00', 'post_status' => 'draft'],
        ];

        $currentEvents = $e->getCurrentEvents('2015-10-07 09:30:00', $allEvents);
        $this->assertInternalType('array', $currentEvents);
        $this->assertCount(2, $currentEvents);

        $eventIds = array_map(function ($a) {return $a['id'];}, $currentEvents);
        $this->assertContains(1, $eventIds);
        $this->assertContains(3, $eventIds);

        // Check the output is sorted
        $this->assertEquals([3, 1], $eventIds);
    }
}
