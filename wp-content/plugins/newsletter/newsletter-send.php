<?php

if (php_sapi_name() !== 'cli') {
    exit(1);
}

ini_set('display_errors', 'On');
error_reporting(E_ALL | E_STRICT);

define('DH_CRON_JOB', true);

//NOTE: could define SHORTINIT to reduce server load.
//define('SHORTINIT', true); // would need to include other files or rewrite everything as wpdb calls.
if (count($argv) > 1) {
    $wpRoot = $argv[1];
} else {
    $wpRoot = realpath(dirname(__FILE__).'/../../..');
}
require $wpRoot.'/wp-load.php';

// Requires
require_once __DIR__.'/../advanced-custom-fields/acf.php';
require_once __DIR__.'/../wtg-like-post/wtg_like_post_site.php';

//turn off voting for all included posts - but keep security filters.
WtgRemoveVoteFromContent();
$allUsers = get_users();
$forceEverybody = true;     // Force all users to receive the newsletter

$newsletterSettings = get_option('newsletter_option_name');
if (isset($newsletterSettings['newsletter-only-admins'])) {
    $onlyAdmins = $newsletterSettings['newsletter-only-admins'];
} else {
    $onlyAdmins = 'no';
}

foreach ($allUsers as $user) {
    if ($forceEverybody || (get_the_author_meta('newsoptin', $user->ID) == 'optin')) {
        if ($user->user_email != '') {
            //debug
            //if ($user->user_email == 'paul.abbott@wtg.co.uk' )
            if (($onlyAdmins != 'yes') || (in_array('administrator', $user->roles))) {
                echo 'Sending newsletter for '.$user->display_name.'(id='.$user->ID.') to '.$user->user_email.' - ';
                SendUserNewsletter($user->ID);
            }
        }
    }
}

function send_email($to = '', $from = '', $reply_to, $subject = '', $html_content = '', $text_content = '', $headers = '')
{
    $f = function ($phpmailer) use ($text_content) {
        $phpmailer->AltBody = $text_content;
    };
    add_action('phpmailer_init', $f);

    # Finish off headers
    $headers [] = "From: $from";
    $headers [] = "Reply-to: $reply_to";

    # Mail it out
    $ret = wp_mail($to, $subject, $html_content, $headers);
    remove_action('phpmailer_init', $f);

    return $ret;
}

/**
* note - with html content need to insert line breaks to stop outlook inserting spaces once a line exceeds 998 chars.
*/
function SendUserNewsletter($userID)
{
    // Get all categories
    $args = array(
        'type' => 'post',
        'child_of' => 0,
        'parent' => '',
        'orderby' => 'name',
        'order' => 'ASC',
        'hide_empty' => 0,
        'hierarchical' => 1,
        'exclude' => '',
        'include' => '',
        'number' => '',
        'taxonomy' => 'category',
        'pad_counts' => false,
    );
    $allCats = get_categories_memo($args);

    // If this method exists, then the News Aggregator
    // plugin is turned on.
    if (function_exists('dhCategoryPosts')) {
        // Reorder the news categories for subscription email.
        $allCats = reorder_news_category_array($allCats, wtg_get_news_aggregator_categories());
    }

    // Get categories for this user
    $userInfo = get_userdata($userID);
    $userMeta = get_user_meta($userID);
    $userName = $userMeta['first_name'][0];
    $userEmail = $userInfo->user_email;
    $userCats = get_user_meta($userID, '_per_user_feeds_cats');

    if ($userCats[0]) {
        $userCatsArray = $userCats[0];
    } else {
        $userCatsArray = array();
    }

    $profileLink = get_bloginfo('url').'/profile';
    $introText = "Hello $userName. Here’s a round-up of the latest news and information on the subjects you’ve subscribed to. <a href='".$profileLink."' style='color:#41484c; font-family: arial, sans-serif;'>You can edit your subscriptions</a> at any time.";

    // Start building the plain version of the email.
    $plainEmailPart1 = "This week in your subscriptions\r\n\r\n";
    $plainEmailPart2 = $introText."\r\n";

    // Start building the html version of the email
    $htmlLinksEmailPart1 = "<h1 style='color:#41484c; font-family: arial, sans-serif;'>This week in your subscriptions</h1>\r\n";
    $htmlLinksEmailPart2 = "<p style='color:#41484c; font-family: arial, sans-serif;'>$introText</p>\r\n";

    $htmlLinksEmailPart3 = '';
    $plainEmailPart3 = '';

    $sendEmail = false;

    $last_successful = (int) get_user_meta($userID, '_newsletter_send_last_successful', true);

    $after = '1 week ago';
    if ($last_successful !== 0) {
        $after = gmstrftime('%Y-%m-%dT%H:%M', $last_successful);
    }

    foreach ($allCats as $aCat) {
        //see if the cat has an 'always' status, or is in this users selections
        $includeCat = get_field('include_in_newsletter', $aCat);
        if (($includeCat != 'never') && (($includeCat == 'always') || in_array($aCat->term_id, $userCatsArray))) {
            $args = array(
                'cat' => $aCat->term_id,
                'post_status' => 'publish',
                'date_query' => array(
                    array(
                        'column' => 'post_date_gmt',
                        'after' => $after,
                    ),
                ),
            );
            $query = new WP_Query($args);

            // The Loop
            if ($query->have_posts()) {
                $sendEmail = true;

                // show the cat title
                $plainEmailPart3 .= "\r\n".$aCat->name."\r\n".str_repeat('-', strlen($aCat->name))."\r\n";
                $htmlLinksEmailPart3 .= "<br><span style='color:#007ac3; font-family: arial, sans-serif;'><b>".$aCat->name."</b></span><br>\r\n";

                while ($query->have_posts()) {
                    $query->the_post();
                    $htmlLinksEmailPart3 .= "<a href='".get_permalink()."'style='color:#41484c; font-family: arial, sans-serif;'>".get_the_title()."</a><br>\r\n";
                    $plainEmailPart3 .= get_the_title().' - '.get_permalink()."\r\n";
                }
            } else {
                // Disabled as per DH-410, but left commented out code just in case.
                //                $htmlLinksEmailPart3 .= "<span style='color:#41484c; font-family: arial, sans-serif;'>There are no new articles this week</span><br>\r\n";
                //                $plainEmailPart3 .= "There are no new articles this week\r\n";
            }
            wp_reset_postdata();
        }
    }

    $htmlLinksEmailPart4 = "<br><br><span style='color:#41484c; font-family: arial, sans-serif;'>This is an automated email, sent from the DH intranet. You can <a href='".$profileLink."' style='color:#41484c; font-family: arial, sans-serif;'>edit your subscriptions</a> at any time. Is there a topic you’d like updates on that we haven’t covered? <a href='mailto: intranet@dh.gsi.gov.uk' style='color:#41484c; font-family: arial, sans-serif;'>Email the DH intranet team</a> with your ideas.</span>\r\n";
    $plainEmailPart4 = 'This is an automated email, sent from the DH intranet. You can edit your subscriptions at any time.['.$profileLink."]\r\nIs there a topic you’d like updates on that we haven’t covered? Email the DH intranet team with your ideas. [intranet@dh.gsi.gov.uk]";

    $htmlLinksEmailBlankLines = '<br><br><hr><br><br>';
    $plainEmailBlankLines = "\r\n\r\n\r\n\r\n\r\n";

    //Build plain-text and html versions of the email.
    $wholePlainEmail = $plainEmailPart1.$plainEmailPart2.$plainEmailPart3.$plainEmailPart4.$plainEmailBlankLines;
    $wholeHtmlLinksEmail = $htmlLinksEmailPart1.$htmlLinksEmailPart2.$htmlLinksEmailPart3.$htmlLinksEmailPart4.$htmlLinksEmailBlankLines;

    //$subject = 'DH Newsletter for '. date('l jS \of F Y');
    $subject = 'Your weekly summary of DH news and information';

    $from = 'DH Intranet Update <noreply@dh-intranet.co.uk>';
    $replyTo = 'intranet@dh.gsi.gov.uk';

    //send multi-part versions of the email to users email address.
    if ($sendEmail) {
        $result = send_email($userEmail, $from, $replyTo, $subject, $wholeHtmlLinksEmail, $wholePlainEmail);
        echo 'Mail '.($result ? 'sent' : 'error');
    } else {
        echo 'Mail not sent. Reason - no updates in the categories.';
    }
    echo "\n";

    update_user_meta($userID, '_newsletter_send_last_successful', time());

    //turn voting back on
    WtgAddVoteToContent();
}

/**
* Reorder the category array in the sequence that's set up
* in the news aggregator back-end.
*
* @param $all_categories - All available categories
* @param $news_aggregator_categories - News aggregator categories
*
* @return array reorganized array
*/
function reorder_news_category_array($all_categories, $news_aggregator_categories)
{
    $reordered_categories = array();

    foreach ($news_aggregator_categories as $n_a_cat) {
        foreach ($all_categories as $key => $cat) {
            if ($cat->cat_ID == $n_a_cat) {
                $reordered_categories[] = $cat;
                unset($all_categories[$key]);
            }
        }
    }

    // Add the categories that have been subscribed to, but are turned off
    // in the news aggregator.
    $reordered_categories = array_merge($reordered_categories, $all_categories);

    return $reordered_categories;
}

// Memoised version of get_categories()
function get_categories_memo($args)
{
    global $get_categories_memo;

    if (!isset($get_categories_memo) || !is_array($get_categories_memo)) {
        $get_categories_memo = [];
    }

    $a = json_encode($args);
    if (!isset($get_categories_memo[$a])) {
        $get_categories_memo[$a] = get_categories($args);
    }

    return $get_categories_memo[$a];
}
