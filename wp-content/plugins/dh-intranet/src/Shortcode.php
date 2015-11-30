<?php

namespace DHIntranetPlugin;

class Shortcode
{
    public function __construct()
    {
        // Array of IP addresses or CIDR ranges (v4-only)
        $this->errorPageInternalIps = ['212.250.43.0/26', '212.250.23.64/26'];
    }

    public function render($internal)
    {
        // URL, link text, internal-only
        $links = [
            ['http://showa-internal.doh.gov.uk:8012/OA_HTML/AppsLocalLogin.jsp', 'BMS', true],
            ['http://dhinfo.doh.gov.uk/ContactWeb', 'Contact', true],
            ['https://www.civilservicejobs.service.gov.uk/csr/index.cgi', 'CS Jobs', false],
            ['https://civilservicelearning.civilservice.gov.uk/', 'CS Learning', false],
            ['http://dhsrvmds453.doh.gov.uk/icdb/newsld.nsf/SLDWeb?OpenView', 'Directory', true],
            ['https://www.gov.uk/government/organisations/department-of-health', 'GOV.UK', false],
            ['http://iws.ims.gov.uk/', 'IWS', true],
            ['https://www.trips.uk.com/js/SABS/Corporate.html', 'tRIPS', true],
            ['https://www.yammer.com/dh.gsi.gov.uk/', 'Yammer', false],
        ];

        $links = array_filter($links, function ($link) use ($internal) {
            return $internal || !$link[2];
        });

        $lis = array_map(function ($link) {
            return '<li><a href="'.esc_attr($link[0]).'">'.esc_html($link[1]).'</a></li>';
        }, $links);

        // use DIV instead of NAV for IE8 support
        return '
        <div class="nav">
            <ul>
                '.implode("\n", $lis).'
            </ul>
        </div>
        ';
    }

    public function ipIsInternal($patterns, $ip)
    {
        foreach ($patterns as $pattern) {
            list($match, $err) = \CIDR\IPv4::match($pattern, $ip);
            if ($err === null && $match) {
                return true;
            }
        }

        return false;
    }

    public static function register()
    {
        $th = new self();
        add_shortcode('dh-error-page-links', [$th, 'shortcode']);
    }

    public function shortcode()
    {
        $internal = $this->ipIsInternal($this->errorPageInternalIps, $_SERVER['REMOTE_ADDR']);

        return $this->render($internal);
    }
}
