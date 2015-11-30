<?php
include "newsletter-settings-page.php";

/*
Plugin Name: WTG Category Newsletter

Plugin URI:
Description:
Author: WTG
Author URI:
License:

mewsletter-send.php needs to be called weekly from server cron file.
*/

Category_Newsletter::init();

// Create and instance of the Newsletter settings
if( is_admin() )
{
    $settings_page = new NewsletterSettingsPage();
}

class Category_Newsletter
{
    // Where we'll store the user cats
    const META_KEY = '_per_user_feeds_cats';
    // Nonce for the form fields
    const NONCE = '_user_user_feeds_nonce';
    // Taxonomy to use
    const TAX = 'category';
    // The query variable for the rewrite
    const Q_VAR = 'puf_feed';
    // container for the instance of this class
    private static $ins = null;
    // container for the terms allowed for this plugin
    private static $terms = null;

    /**
     *
     */
    public static function init()
    {
        add_action('plugins_loaded', array(__CLASS__, 'instance'));

        //add short codes
        add_shortcode( 'showusername', array(__CLASS__, 'ShowUserFirstName' ) );
        add_shortcode( 'showuserfirstname', array(__CLASS__, 'ShowUserFirstName' ) );
        add_shortcode( 'showusercats', array(__CLASS__, 'ShowUserCats' ) );

    }

    /**
     * short code [showuserfirstname]
     *
     * @param $atts
     * @return mixed
     */
    public static function ShowUserFirstName( $atts )
    {
        $userID = get_current_user_id();
        $userMeta = get_user_meta($userID);
        $userName = $userMeta['first_name'][0];
        return $userName;
    }



    /**
     * short code [showusercats]
     *
     * @param $atts
     * @return string
     */
    public static function ShowUserCats( $atts )
    {

        /*
         * Display a list of post, pages and policy articles publish or modified this week based on the users subscription
         * to categories selected in there user profile.
         */

        //turn off voting for all included posts - but keep security filters.
        //WtgRemoveVoteFromContent();

        // Get all categories
        $args = array(
            'type' => 'post',
            'child_of' => 0,
            'parent' => '',
            'orderby' => 'name',
            'order' => 'ASC',
            'hide_empty' => 1,
            'hierarchical' => 1,
            'exclude' => '',
            'include' => '',
            'number' => '',
            'taxonomy' => 'category',
            'pad_counts' => false
        );
        $allCats = get_categories($args);

        // Get categories for this user
        $userID = get_current_user_id();
        $userMeta = get_user_meta($userID);
        $userCats = $userMeta['_per_user_feeds_cats'];
        $userCatsArray = unserialize($userCats[0]);

        global $post;

        $outputString = '';

        foreach ($allCats as $aCat) {
            //see if the cat has an 'always' status, or is in this users selections
            $includeCat = get_field('include_in_newsletter', $aCat);
            if (($includeCat == 'always') || in_array($aCat->term_id, $userCatsArray)) {
                $args = array(
                    'cat' => $aCat->term_id,
                    'post_status' => 'publish',
                    'date_query' => array(
                        array(
                            'column' => 'post_date_gmt',                            
                            'after' => '1 week ago',
                        ),
                    ),
                );
                $query = new WP_Query($args);

                // The Loop
                if ($query->have_posts()) {
                    //Category name as title
                    $outputString .= '<h3>' . $aCat->name . '</h3>';
                    $outputString .= '<ul class="list-unstyled row">';
                    while ($query->have_posts()) {
                        $query->the_post();
                        $outputString .= '<article class="homepage-story entry clearfix">';
                        //show image if there is one.
                        if (has_post_thumbnail()) {
                            ob_start();
                            comments_popup_link('0', '1', '%');
                            $get_comments_popup_link = ob_get_clean();

                            $outputString .= '<div class="col-md-4 col-sm-4">';
                            $outputString .= '<figure>';
                            $outputString .= '<div class="meta">';
                            $outputString .= '<span>' . $get_comments_popup_link . '</span>';
                            $outputString .= '</div>';
                            $outputString .= get_the_post_thumbnail($post->ID, 'category-thumb');
                            $outputString .= '</figure>';
                            $outputString .= '</div>';
                        }
                        $outputString .= '<div class="col-md-8  col-sm-8">';
                        $outputString .= '<h5><a href="' . get_the_permalink() . '">' . get_the_title() . '</a></h5>';
                        $outputString .= '<time class="published" datetime="' . get_the_time('c') . '">' . get_the_time('d/m/Y') . '</time>';
                        $little_excerpt = substr(get_the_excerpt(), 0, 240);
                        $last_space = strrpos($little_excerpt, ' ');
                        $little_excerpt = substr($little_excerpt, 0, $last_space);
                        $little_excerpt .= '&#8230;<br><a href="' . get_the_permalink() . '">Continued</a>';
                        $outputString .= '<div class="entry-summary"><p>' . $little_excerpt . '</p></div>';
                        $outputString .= '</div>';
                        $outputString .= '</article>';
                    }
                    $outputString .= '</ul>';
                }
            }
        }
        //turn voting back on
        //WtgAddVoteToContent();
        wp_reset_postdata();

        return $outputString;
    }


    /**
     * @return Category_Newsletter|null
     */
    public static function instance()
    {
        is_null(self::$ins) && self::$ins = new self;
        return self::$ins;
    }

    /**
     *
     */
    protected function __construct()
    {
        add_action('personal_options_update', array($this, 'save'));
        add_action('edit_user_profile_update', array($this, 'save'));
        //not using WP inbult cron anymore.
        //add_action('catnews_daily_event_hook', array($this, 'catnews_run_daily'));
    }


    /**
     * @param $user
     */
    public function field($user)
    {
        wp_nonce_field(self::NONCE . $user->ID, self::NONCE, false);
        echo'<h3>Newsletter Options</h3>';
        /* remove opt-in choice for now
        $optin = esc_attr( get_the_author_meta( 'newsoptin', $user->ID ) );
        echo '<input type="checkbox" name="newsoptin" id="newsoptin" value="optin" <?php echo $optin == "optin" ? "checked" : '' ?>/>Opt-in to the Weekly DH Newsletter<br />';
        */
        echo '<p><b>Please tick your areas of interest</b></p>';
        //build tick boxes rather than select
        if($terms = self::get_terms())
        {
            $val = self::get_user_terms($user->ID);
            $val = $val ? $val : array();
            foreach($terms as $t)
            {
                printf(
                    '<input type="checkbox" name="%4$s[]" value="%1$s" %3$s>%2$s<br>',
                    esc_attr($t->term_id),
                    esc_html($t->name),
                    in_array($t->term_id, $val) ? 'checked' : '',
                    esc_attr(self::META_KEY)
                );
            }
        }
    }

    /**
     * @param $user_id
     */
    public function save($user_id)
    {
        if(
            !isset($_POST[self::NONCE]) ||
            !wp_verify_nonce($_POST[self::NONCE], self::NONCE . $user_id)
        ) return;

        if(!current_user_can('edit_user', $user_id))
            return;
        if(!empty($_POST[self::META_KEY]))
        {
            $allowed = array_map(function($t) {
                return $t->term_id;
            }, self::get_terms());

            // PHP > 5.3: Make sure the items are in our allowed terms.
            $res = array_filter(
                (array)$_POST[self::META_KEY],
                function($i) use ($allowed) {
                    return in_array($i, $allowed);
                }
            );

            update_user_meta($user_id, self::META_KEY, array_map('absint', $res));
        }
        else
        {
            delete_user_meta($user_id, self::META_KEY);
        }
        update_usermeta( $user_id, 'newsoptin', $_POST['newsoptin'] );
    }


    /***** Helpers *****/

    /**
     * Get the categories available for use with this plugin.
     *
     * @uses    get_terms
     * @uses    apply_filters
     * @return  array The categories for use
     */
    public static function get_terms()
    {
        if(is_null(self::$terms))
            self::$terms = get_terms(self::TAX, array('hide_empty' => false));

        //no filter left to apply?
        return apply_filters('per_user_feeds_terms', self::$terms);
    }

    /**
     * Get the feed terms for a given user.
     *
     * @param   int $user_id The user for which to fetch terms
     * @uses    get_user_meta
     * @uses    apply_filters
     * @return  mixed The array of allowed term IDs or an empty string
     */
    public static function get_user_terms($user_id)
    {
        return apply_filters('per_user_feeds_user_terms',
            get_user_meta($user_id, self::META_KEY, true), $user_id);
    }

    /**
     * Get a WP_Query object for a given user.
     *
     * @acces   public
     * @uses    WP_Query
     * @return  object WP_Query
     */
    public static function get_user_query($user_id)
    {
        $terms = self::get_user_terms($user_id);

        if(!$terms)
            return apply_filters('per_user_feeds_query_args', false, $terms, $user_id);

        $args = apply_filters('per_user_feeds_query_args', array(
            'tax_query' => array(
                array(
                    'taxonomy'  => self::TAX,
                    'terms'     => $terms,
                    'field'     => 'id',
                    'operator'  => 'IN',
                ),
            ),
        ), $terms, $user_id);

        return new WP_Query($args);
    }
}

