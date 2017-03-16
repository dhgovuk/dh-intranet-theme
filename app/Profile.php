<?php

namespace DHIntranet;

use Dxw\Iguana\Registerable;
use WP_Error;
use PHPMailer;

/**
 * Class Profile
 *
 * This class was mostly copied from the `wtg-security` plugin which we have replaced
 *
 * @package DHIntranet
 */
class Profile implements Registerable
{
    /**
     * @var array
     */
    private $emailDomainWhitelist = [
        'dh.gsi.gov.uk',
        'dxw.com'
    ];

    /**
     *
     */
    public function register()
    {
        add_action('user_profile_update_errors', array($this, 'validateProfile'), 10, 3);
        remove_action('wp_head', 'feed_links',                       2);
        remove_action('wp_head', 'feed_links_extra',                 3);
        remove_action('wp_head', 'rsd_link');
        remove_action('wp_head', 'wlwmanifest_link');
        remove_action('wp_head', 'parent_post_rel_link',            10);
        remove_action('wp_head', 'start_post_rel_link',             10);
        remove_action('wp_head', 'adjacent_posts_rel_link_wp_head', 10);
        remove_action('wp_head', 'rel_canonical');
        add_action('login_head', 'wp_no_robots');
        add_action('init', array($this, 'profileRedirect'));
        add_shortcode('profile-page', array($this, 'profileShortCode'));
        add_action('profile_update', [$this, 'profileUpdate']);
        add_filter('phpmailer_init', array($this, 'homeEmailResetPassword'));
    }

    /**
     * Profile redirect
     *
     * Prevent people from going to the old profile page in the backend and force them to go to the new profile
     * page.
     *
     * @access		public
     * @since		0.3
     * @return		void
     */
    public function profileRedirect()
    {
        global $pagenow;

        if (is_user_logged_in() && is_admin() && $pagenow === 'profile.php' && ! isset($_REQUEST['page'])) {
            wp_redirect('/profile');
        }
    }

    /**
     * Validate profile
     *
     * @access		public
     * @since		0.3
     * @param		WP_Error		$errors
     * @return		WP_Error
     */
    public function validateProfile(WP_Error $errors)
    {
        $message_bag = array();

        $current_user = wp_get_current_user();

        if (! isset($_POST['first_name']) || trim($_POST['first_name']) === '') {
            $message_bag[] = '<strong>ERROR</strong>: Please enter your first name.';
        }

        if (! isset($_POST['last_name']) || trim($_POST['last_name']) === '') {
            $message_bag[] = '<strong>ERROR</strong>: Please enter your last name';
        }

        // Skip email validation if the user is not trying to change the email.
        // Skip email validation if the admin is adding a new user.
        // Added for DH-407 and DH-408.
        if ($current_user->user_email != $_POST['email'] && $_POST['action'] != 'createuser') {
            if (! filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
                $message_bag[] = '<strong>ERROR</strong>: The email address isn’t correct.';
            } elseif (! in_array(array_pop(explode('@', strtolower($_POST['email']))), $this->emailDomainWhitelist)) {
                if ($_SERVER['REQUEST_URI'] != '/wp-admin/user-edit.php') {
                    $message_bag[] = '<strong>ERROR</strong>: The email address isn’t correct.';
                }
            }
        }

        empty($message_bag) ? null : $errors->add('validation_errors', implode('<br />', $message_bag));

        return $errors;
    }

    /**
     * Home Email Reset Password
     *
     * Hooks into the PHPMailer to add the home_email value from the users custom meta data if it exists.
     *
     * @access		public
     * @since		0.3
     * @param		PHPMailer		$phpmailer
     * @return		PHPMailer
     */
    public function homeEmailResetPassword(PHPMailer $phpmailer)
    {
        if (isset($_REQUEST['action']) && $_REQUEST['action'] === 'lostpassword') {
            /*
             * Okay, I'm not proud of this, but the version of PHPMailer that WP 3.9 uses is out of date and the
             * method getToAddresses() doesn't exist, so there is no other way to retrieve the users email.  In
             * theory, the $_POST data could be either the email or the username, so we can't risk using that
             * either.  The only way to retrieve the users email is to use reflection!
             */
            $reflector                = new \ReflectionClass($phpmailer);
            $classProperty            = $reflector->getProperty('to');
            $classProperty->setAccessible(true);
            $workEmail                = reset(reset($classProperty->getValue($phpmailer)));

            $user                    = get_user_by('email', $workEmail);
            $homeEmail                = get_user_meta($user->ID, 'home_email', true);

            $homeEmail && $homeEmail !== '' ? $phpmailer->addAddress($homeEmail) : null;
        }

        return $phpmailer;
    }

    /**
     * Profile Short code
     *
     * This shortcode produces the form for the user to see and edit their profile.
     *
     * @access		public
     * @since		0.3
     * @return		bool|string
     */
    public function profileShortCode()
    {
        require_once(ABSPATH . 'wp-admin/includes/user.php');
        require_once(ABSPATH . 'wp-admin/includes/misc.php');

        define('IS_PROFILE_PAGE', true);

        register_admin_color_schemes();
        wp_enqueue_script('user-profile');

        $current_user = wp_get_current_user();

        $message = false;
        $errors = false;

        // Quick hack here, WordPress doesn't have any built in functions to get the URI segments?
        $segments = explode("/", parse_url(trim($_SERVER['REQUEST_URI'], '/'), PHP_URL_PATH));

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && preg_match('/profile/', $segments[0])) {
            $errors = false;

            check_admin_referer('update_user_' . $current_user->ID);

            if (! current_user_can('edit_user', $current_user->ID)) {
                wp_die('You do not have permission to edit this user.');
            }

            do_action('personal_options_update', $current_user->ID);

            // Nasty little hack due to a bug in this version of WordPress.
            require_once(ABSPATH . 'wp-admin/includes/screen.php');

            update_user_meta($current_user->ID, 'home_email', $_POST['home_email']);
            update_user_meta($current_user->ID, '_per_user_feeds_cats', array_keys($_POST['newsletter_cats']));
            update_user_meta($current_user->ID, 'news_locale', (int)$_POST['news-locale']);

            $errors = edit_user($current_user->ID);
            $errors =  is_object($errors) ? $errors : false;

            $message = $errors ? false : 'Your profile details have been updated successfully.';
        }

        $data = array(
            'current_user'                => wp_get_current_user(),
            'profileuser'                => get_user_to_edit(wp_get_current_user()->ID),
            'errors'                    => $this->sortErrors($errors),
            'message'                    => $message,
            'home_email'                => get_user_meta($current_user->ID, 'home_email', true),
            'locations'                    => get_terms('news-locale', array('hide_empty' => false)),
            'current_location'            => $this->getCurrentLocation(),
            'newsletter_cats'            => $this->getNewsletterCats(),
            'selected_newsletter_cats'    => reset(get_user_meta($current_user->ID, '_per_user_feeds_cats'))
        );

        return $this->view('profile', $data);
    }

    /**
     * Sort Errors
     *
     * This method pulls apart the WP_ERROR object and puts into a usable string for the page.
     *
     * @access		private
     * @since		0.3
     * @param		$errorsToSort
     * @return		bool|string
     */
    private function sortErrors($errorsToSort)
    {
        if ($errorsToSort) {
            $output = '';
            $wp_error = new WP_Error();

            $wp_error->add('error', $errorsToSort);

            if ($wp_error->get_error_code()) {
                $errors = '';
                $messages = '';

                foreach ($wp_error->get_error_codes() as $code) {
                    $severity = $wp_error->get_error_data($code);

                    foreach ($wp_error->get_error_messages($code) as $error) {
                        if ($severity === 'message') {
                            $messages .= '	' . $error->get_error_message() . "<br />\n";
                        } else {
                            $errors .= '	' . $error->get_error_message() . "<br />\n";
                        }
                    }
                }

                if (! empty($errors)) {
                    $output .= '<p class="form_error">' . apply_filters('login_errors', $errors) . "</p>\n";
                }
                if (! empty($messages)) {
                    $output .= '<p class="message">' . apply_filters('login_messages', $messages) . "</p>\n";
                }
            }
            return $output;
        }

        return false;
    }

    /**
     * Update Home Email
     *
     * Checks that the logged in user can edit the home email field and if they can, updates it.
     *
     * @access		public
     * @since		0.3
     * @param		$user_id
     * @param		$email
     * @return		bool|void
     */
    public function updateHomeEmail($user_id, $email)
    {
        if (! current_user_can('edit_user', $user_id)) {
            return false;
        }

        update_user_meta($user_id, 'home_email', $email);
    }

    /**
     * Change Location
     *
     * @access		private
     * @since		0.3
     * @param		$location
     * @param		$user_id
     * @return		void
     */
    private function changeLocation($location, $user_id)
    {
        $term = get_term_by('id', (int) $location, 'news-locale');

        $term === false ? wp_die('{"error": "location not found"}') : null;

        update_user_meta($user_id, 'news_locale', (int) $term->term_id);
    }

    /**
     * Get Newsletter Categories
     *
     * This is in a separate method as it's most likely required to be changed again in the future to be slightly
     * more selective.  Warning, this is a bit dirty, the code is duplicated from the newsletter plugin, but it
     * made more sense at the time to put it in here to keep the methods for the profile page all in one place.
     *
     * @access		private
     * @since		0.3
     * @return		array|WP_Error
     * @todo		Revise this at some point, this code should be in one place, possibly the theme functions.
     */
    private function getNewsletterCats()
    {
        $args = array(
            'type' => 'post',
            'child_of' => 0,
            'parent' => '',
            'orderby' => 'name',
            'order' => 'ASC',
            'hide_empty' => false,
            'hierarchical' => true,
            'exclude' => '',
            'include' => '',
            'number' => '',
            'taxonomy' => 'category',
            'pad_counts' => false
        );

        $categories = get_categories($args);

        foreach ($categories as $key => $cat) {
            $flag = get_field('include_in_newsletter', $cat);

            if ($flag === 'never') {
                unset($categories[$key]);
            }
            if ($flag === 'always') {
                $categories[$key]->disabled = true;
            }
        }

        return $categories;
    }

    /**
     * Get Current Location
     *
     * Warning: This is dirty.  This code is duplicated from the dh-intranet theme files, but we wanted to keep this
     * all in one place to prevent possible errors.
     *
     * @access		private
     * @since		0.3
     * @return		bool|int
     * @todo		Consider moving all this code into the theme libraries.  Kept in wtg_security code due to
     * 				time restraints and trying to keep everything in one place for the profile/user forms.
     */
    private function getCurrentLocation()
    {
        if (is_user_logged_in()) {
            $location = (int) get_user_meta(get_current_user_id(), 'news_locale', true);

            if ($location === 0) {
                return $this->defaultLocation();
            }

            return $location;
        } else {
            if (isset($_COOKIE['news_locale'])) {
                return (int) $_COOKIE['news_locale'];
            } elseif (isset($_SESSION['news_locale'])) {
                return (int) $_SESSION['news_locale'];
            }

            return $this->defaultLocation();
        }
    }

    /**
     * Default Location
     *
     * Warning: This is dirty.  This code is duplicated from the dh-intranet theme files, but we wanted to keep this
     * all in one place to prevent possible errors if the theme is swapped, etc.
     *
     * @access		private
     * @since		0.3
     * @return		bool|int
     * @todo		Consider moving this whole library to the theme, see Get Current Location method above.
     */
    private function defaultLocation()
    {
        // Go through all the terms
        // Pick the first with a "default" checkbox checked
        $terms = get_terms('news-locale', array('hide_empty' => false));

        foreach ($terms as $term) {
            $default = get_field('default', "news-locale_{$term->term_id}");
            if ($default === array('default')) {
                return (int)$term->term_id;
            }
        }

        return isset($term->term_id) ? (int)$term->term_id : false;
    }

    /**
     * View
     *
     * A simple view function to allow us to call in HTML with an array of variables.
     *
     * @access		protected
     * @since		0.1
     * @param		$view
     * @param		array		$data
     * @return		bool|string
     */
    protected function view($view, $data = array())
    {
        // Set up the view file varable.
        $view_file = get_template_directory() . '/' . $view . '.php';

        // If the file doesn't exist, then bail.
        if (! file_exists($view_file)) {
            return false;
        }

        // Start an object buffer.
        ob_start();

        // Pull all the variables we need into this object space.
        extract(is_array($data) ? $data : (array) $data);

        // Pull in the file, not "include_once" on purpose so that the same file can be used more than once.
        include($view_file);

        // Get the buffer contents and clean it up. Again, clean the buffer so it can be used again.
        $buffer = ob_get_contents();
        @ob_end_clean();

        // Return the buffer contents.
        return $buffer;
    }

    /**
     * Profile update
     *
     * When the user changes there password, delete the cache and log them back in with the new password.
     *
     * @param $errors
     */
    public function profileUpdate($errors)
    {
        $password = $_POST['pass1'];
        //if user has enter a new password
        if ((isset($password)) && ($password !== '')) {
            global $wpdb;
            $user_id = $_POST['user_id'];

            $md5password = wp_hash_password($password);
            $userdata = get_userdata($user_id);
            $username = $userdata->user_login;

            // use $wpdb->prepare()
            $sql = $wpdb->prepare("UPDATE wp_users SET user_login = %s, user_pass = %s WHERE ID = %d'", $username, $md5password, $user_id);
            //$q = "UPDATE wp_users SET user_login = '".$username."', user_pass = '{$md5password}' WHERE ID = '" . $user_id . "'";
            $wpdb->query($sql);
            // delete cache data and sign back into wordpress
            wp_cache_delete($user_id, 'users');
            wp_cache_delete($username, 'userlogins'); // This might be an issue for how you are doing it. Presumably you'd need to run this for the ORIGINAL user login name, not the new one.
            wp_logout();
            wp_signon(array('user_login' => $username, 'user_password' => $password));
        }
        // will redirect to the homepage if profile successfully updates, otherwise the profile
        // page will be reloaded with an error message.
    }
}
