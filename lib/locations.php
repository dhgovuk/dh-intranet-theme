<?php
/**
* @return bool|int
*/
function default_location()
{
    // Go through all the terms
    // Pick the first with a "default" checkbox checked
    $terms = get_terms('news-locale', array('hide_empty' => false));

    foreach ($terms as $term) {
        $default = get_field('default', "news-locale_{$term->term_id}");
        if ($default === array('default')) {
            return (int) $term->term_id;
        }
    }

    return isset($term->term_id) ? (int) $term->term_id : false;
}

/**
* @return bool
*/
function location_not_set()
{
    return current_location() === false;
}

/**
* @return bool|int
*/
function current_location()
{
    if (isset($_GET['news_locale'])) {
        return absint(stripslashes($_GET['news_locale']));
    }

    if (is_user_logged_in()) {
        return absint(get_user_meta(get_current_user_id(), 'news_locale', true));
    }

    return default_location();
}

function location_selector()
{
    $terms = get_terms('news-locale', array('hide_empty' => false));

    ?>
    <select title="location selector" class="location-selector" data-location-selector data-action="<?php echo esc_attr(admin_url('admin-ajax.php')) ?>" data-nonce="<?php echo esc_attr(wp_create_nonce('select-location')) ?>"<?php echo location_not_set() ? ' data-location-not-set' : '' ?>>
        <?php foreach ($terms as $term) : ?>
            <option value="<?php echo esc_attr($term->term_id) ?>" <?php echo (int) $term->term_id === (int) current_location() ? 'selected' : '' ?>><?php echo esc_html($term->name) ?></option>
        <?php endforeach ?>
    </select>
    <?php

}

function location_dialog()
{
    if (location_not_set()) {
        ?>
        <div class="modal fade location-not-set-modal">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Please set your location</h4>
                    </div>
                    <div class="modal-body">
                        <p><?php location_selector() ?></p>
                        <p>The "In your building" section of the front page will contain news relevant to your chosen
                        location.</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" data-dismiss="modal">Set</button>
                    </div>
                </div>
            </div>
        </div>
        <?php

    }
}

add_action('wp_ajax_select-location', function () {
    $p = stripslashes_deep($_POST);

    if (!isset($p['_wpnonce']) || !wp_verify_nonce($p['_wpnonce'], 'select-location')) {
        wp_die('{"error": "invalid nonce"}');
    }

    $term = get_term_by('id', (int) $p['location'], 'news-locale');

    if ($term === false) {
        wp_die('{"error": "location not found"}');
    }

    update_user_meta(get_current_user_id(), 'news_locale', (int) $term->term_id);

    wp_die('{"ok": true}');
});

add_action('wp_ajax_nopriv_select-location', function() {
    wp_die('{"error": "user not logged in"}');
});

add_action('wp_ajax_get-location', 'ajax_get_location');
add_action('wp_ajax_nopriv_get-location', 'ajax_get_location');
function ajax_get_location()
{
    echo (int) current_location();
    die();
}

function get_location_items()
{
    ?>
    <ul>
        <?php
        $term = get_term_by('id', current_location(), 'news-locale');
        $items = get_posts(array(
            'post_type' => 'local-news',
            'orderby' => 'meta_value',
            'posts_per_page' => -1,
            'news-locale' => $term->slug,
            // Important things first
            'meta_key' => 'important',
            'order' => 'DESC',
        ));
        foreach ($items as $item) : ?>
        <?php $important = get_post_meta($item->ID, 'important', true) !== '' ?>
        <li class="<?php echo $important ? 'title' : '' ?>">
            <a href="<?php echo esc_attr(get_permalink($item->ID)) ?>">
                <?php if ($important) : ?>
                    <strong><?php echo esc_html(get_the_title($item->ID)) ?></strong>
                    <span><?php echo esc_html(excerpt($item->post_content, 10)) ?></span>
                <?php else : ?>
                    <?php echo esc_html(get_the_title($item->ID)) ?>
                <?php endif ?>
            </a>
        </li>
    <?php endforeach ?>
</ul>
<?php
}
