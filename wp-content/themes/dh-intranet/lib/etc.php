<?php

// Relevanssi "did you mean" suggestions fix.
add_filter('relevanssi_didyoumean_query', 'wtg_fix_search_suggestions_query');
/**
* With a large enough database, the suggestion teaching algorithm can crash. To fix, we need to only
* use the words that appear more than once in the database. The problem is that, for some bizzare reason,
* probably due to the W3TC caching plugin, the insert search log query gets executed more than once,
* usually 2-3 times. It was not worth investigating this any further, and it was decided that the easiest,
* although not the best, workaround would be to only select those search words that have been logged
* more than 9 times.
*
* @srouce http://www.relevanssi.com/knowledge-base/did-you-mean-suggestions/
*
* @param string $query This is the original SQL query that we would use to get search suggestions
*
* @return string $query The altered SQL query
*/
function wtg_fix_search_suggestions_query($query)
{
    $query = str_ireplace('ORDER BY count(query) DESC', '', $query);
    $query = $query.' HAVING c > 9 ORDER BY count(query) DESC';

    return $query;
}

/**
* Truncates the given string at the specified length. Will truncate to the
* nearest word, adding '...' to the end of the string.
*
* @param string $str   The input string.
* @param int    $width The number of chars at which the string will be truncated.
*
* @return string
*/
function wtg_truncate_string($str, $width)
{
    return strtok(wordwrap($str, $width, "...\n"), "\n");
}

function wtg_comment_form_title()
{
    global $comment;

    $replytoid = isset($_GET['replytocom']) ? (int) $_GET['replytocom'] : 0;

    if (0 === $replytoid) {
        echo esc_html('Comments');
    } else {
        $comment = get_comment($replytoid);
        $commenter_full_name = get_user_meta($comment->user_id, 'first_name')[0].' '.get_user_meta($comment->user_id, 'last_name')[0];
        ?>
        Leave a Reply to
        <a href="#comment-<?php echo esc_attr(get_comment_ID()) ?>"><?php echo esc_html($commenter_full_name) ?></a>
        <?php
    }
}

/**
* show one 'just missed' event.
*/
function showJustMissed()
{
    global $post;
    $todaysDate = date('Y-m-d');
    $args = array(
        'post_type' => 'todo-item',
        'showposts' => 1,
        'meta_key' => 'due_date',
        'meta_query' => array(
            array('key' => 'due_date','value' => $todaysDate, 'compare' => '<'),
        ),
        'orderby' => 'meta_value',
        'order' => 'DESC',
    );
    $the_query = new WP_Query($args);
    while ($the_query->have_posts()) {
        $the_query->the_post();
        list($d, $err) = \Missing\Dates::parse(get_post_meta($post->ID, 'due_date', true));
        if ($err) {
            continue;
        }
        $date = strftime('%-d', $d).' '.strftime('%B', $d);
        ?>
        <li>
            <a class='justMissed' href="<?php echo esc_attr(get_permalink($post->ID)) ?>">
                <strong>Just missed: <span itemprop="date"><?php echo esc_html($date) ?></span></strong>
                <span itemprop="name"><?php echo esc_html(get_the_title($post->ID)) ?></span>
            </a>
        </li>
        <?php
    }
    wp_reset_postdata();
}

/**
* show all events from today onwards.
*/
function showTodayOnwards()
{
    global $post;

    $args = array(
        'post_type' => 'todo-item',
        'showposts' => 5,
        'meta_key' => 'due_date',
        'meta_value' => date('Y-m-d'),
        'meta_compare' => '>=',
        'orderby' => 'meta_value',
        'order' => 'ASC',
    );
    $the_query = new WP_Query($args);
    $highlightNext = true;
    while ($the_query->have_posts()) {
        $the_query->the_post();
        list($d, $err) = \Missing\Dates::parse(get_post_meta($post->ID, 'due_date', true));
        if ($err) {
            continue;
        }
        $date = strftime('%-d', $d).' '.strftime('%B', $d);
        ?>
        <li class="<?php echo $highlightNext ? 'active ' : '' ?>">
            <a href="<?php echo esc_attr(get_permalink($item->ID)) ?>">
                <strong>Next: <span itemprop="date"><?php echo esc_html($date) ?></span></strong>
                <span itemprop="name"><?php echo esc_html(get_the_title($post->ID)) ?></span>
            </a>
        </li>
        <?php
        $highlightNext = false;
    }
    wp_reset_postdata();
}

function the_little_excerpt($size=200)
{
    $little_excerpt = substr(get_the_excerpt(), 0, $size);
    $last_space = strrpos($little_excerpt, ' ');
    $little_excerpt = substr($little_excerpt, 0, $last_space);
    $little_excerpt .= '&#8230;<br><a href="'.esc_attr(get_the_permalink()).'">Continued</a>';
    echo $little_excerpt;
}

function didyoumean()
{
    if (function_exists('relevanssi_didyoumean')) {
        relevanssi_didyoumean(get_search_query(), '<p>Did you mean: ', '</p>', 5);
    }
}

// Get rid of "nice-search"
add_action('init', function () {
    remove_action('template_redirect', 'roots_nice_search_redirect');
});

// Register hooks
(new \DHIntranet\Search($_GET))->register();
