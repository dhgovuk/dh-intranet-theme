<?php

class ShortcodeTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        \WP_Mock::setUp();

        \WP_Mock::wpFunction('esc_attr', [
            'return' => function ($a) { return htmlspecialchars($a); },
        ]);

        \WP_Mock::wpFunction('esc_html', [
            'return' => function ($a) { return htmlspecialchars($a); },
        ]);
    }

    public function tearDown()
    {
        \WP_Mock::tearDown();
    }

    public function testRenderInternal()
    {

        $expected = '
        <div class="nav">
        <ul>
            <li><a href="http://showa-internal.doh.gov.uk:8012/OA_HTML/AppsLocalLogin.jsp">BMS</a></li>
            <li><a href="http://dhinfo.doh.gov.uk/ContactWeb">Contact</a></li>
            <li><a href="https://www.civilservicejobs.service.gov.uk/csr/index.cgi">CS Jobs</a></li>
            <li><a href="https://civilservicelearning.civilservice.gov.uk/">CS Learning</a></li>
            <li><a href="http://dhsrvmds453.doh.gov.uk/icdb/newsld.nsf/SLDWeb?OpenView">Directory</a></li>
            <li><a href="https://www.gov.uk/government/organisations/department-of-health">GOV.UK</a></li>
            <li><a href="http://iws.ims.gov.uk/">IWS</a></li>
            <li><a href="https://www.trips.uk.com/js/SABS/Corporate.html">tRIPS</a></li>
            <li><a href="https://www.yammer.com/dh.gsi.gov.uk/">Yammer</a></li>
        </ul>
        </div>
        ';

        $s = new \DHIntranetPlugin\Shortcode();

        $actual = $s->render(true);

        $this->assertHTMLEquals($expected, $actual);
    }

    public function testRenderExternal()
    {

        $expected = '
        <div class="nav">
        <ul>
            <li><a href="https://www.civilservicejobs.service.gov.uk/csr/index.cgi">CS Jobs</a></li>
            <li><a href="https://civilservicelearning.civilservice.gov.uk/">CS Learning</a></li>
            <li><a href="https://www.gov.uk/government/organisations/department-of-health">GOV.UK</a></li>
            <li><a href="https://www.yammer.com/dh.gsi.gov.uk/">Yammer</a></li>
        </ul>
        </div>
        ';

        $s = new \DHIntranetPlugin\Shortcode();

        $actual = $s->render(false);

        $this->assertHTMLEquals($expected, $actual);
    }

    public function testInternalDetection()
    {
        $matrix = [
            [['10.0.2.2'], '10.0.2.2', true],
            [['10.0.2.0/24'], '10.0.2.2', true],
            [['10.0.0.0/24'], '11.0.2.2', false],
        ];

        $s = new \DHIntranetPlugin\Shortcode();

        foreach ($matrix as $row) {
            list($patterns, $ip, $expected) = $row;
            $actual = $s->ipIsInternal($patterns, $ip);
            $this->assertEquals($expected, $actual);
        }
    }

    public function testAddShortcode()
    {
        \WP_Mock::wpFunction('add_shortcode', [
            'times' => 1,
        ]);

        \DHIntranetPlugin\Shortcode::register();
    }

    protected function assertHTMLEquals($expected, $actual)
    {
        $expected = preg_replace('/^\s+/m', '', $expected);
        $actual = preg_replace('/^\s+/m', '', $actual);
        $this->assertEquals($expected, $actual);
    }
}
