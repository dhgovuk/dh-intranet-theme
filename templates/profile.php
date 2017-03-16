<div class="login profile" id="wtg-profile">
	<p>Use this screen to update your personal information and the news you have subscribed for. Remember, when you’ve
		registered you can access the intranet from anywhere on any device – so don’t forget to add a home email address
		in case you forget your password.</p>
       <p>Fields marked with <span class="required">*</span> are required</p>

       <?php echo $message ? $message : null; ?>
       <?php echo $errors ? $errors : null; ?>

       <form id="your-profile" class="form-horizontal" action="" method="post">
        <?php wp_nonce_field('ef_edit_profile_usergroups_nonce', 'ef_edit_profile_usergroups_nonce'); ?>
        <fieldset>
         <?php wp_nonce_field('update_user_' . $current_user->ID); ?>
         <input type="hidden" name="from" value="profile" />
         <input type="hidden" name="checkuser_id" value="<?php echo esc_attr($current_user->ID); ?>" />

         <?php if (has_action('personal_options')) : ?>

         <h3><?php _e('Personal Options'); ?></h3>
         <?php do_action('personal_options', $profileuser); ?>

     <?php endif; ?>

     <?php do_action('profile_personal_options', $profileuser); ?>
     <ul>
         <li>
            <label for="first_name"><?php _e('First Name'); ?>
               <span class="required">*</span>
           </label>
           <input type="text" name="first_name" id="first_name"
           value="<?php echo $_POST ? esc_attr($_POST['first_name']) : esc_attr($profileuser->first_name); ?>"
           class="regular-text" />
       </li>

       <li>
        <label for="last_name"><?php _e('Last Name'); ?>
           <span class="required">*</span>
       </label>
       <input type="text" name="last_name" id="last_name"
       value="<?php echo $_POST ? esc_attr($_POST['last_name']) : esc_attr($profileuser->last_name); ?>"
       class="regular-text" />
   </li>
   <li>
    <label for="email"><?php _e('Work e-mail'); ?>
       <span class="required">*</span>
   </label>
   <input type="text" name="email" id="email" value="<?php echo esc_attr($profileuser->user_email); ?>"
   class="regular-text" />
</li>
 <li>
    <label for="home_email"><?php _e('Home email'); ?></label>
    <input type="text" name="home_email" id="home_email"
    value="<?php echo esc_attr(get_the_author_meta('home_email', $profileuser->ID)); ?>"
    class="regular-text code" />
</li>
<li>
    <p class="form_indent">If you forget your password, we'll also email a reminder to this address. Your
       email will be stored securely. It won't be available for any other use by DH.</p>
   </li>
<?php foreach (_wp_get_user_contactmethods() as $name => $desc) : ?>
    <li>
       <label for="<?php echo esc_attr($name); ?>"><?php echo apply_filters('user_'.$name.'_label', $desc); ?></label>
       <input type="text" name="<?php echo esc_attr($name); ?>" id="<?php echo esc_attr($name); ?>"
       value="<?php echo esc_attr($profileuser->$name); ?>" class="regular-text" />
   </li>
<?php endforeach; ?>
<li>
    <label for="location-selector">Building</label>
    <select title="location selector" id="location-selector" name="news-locale">
       <?php foreach ($locations as $location) : ?>
       <option value="<?php echo esc_attr($location->term_id) ?>"
         <?php echo (int)$location->term_id === (int) $current_location ? 'selected' : '' ?>>
         <?php echo esc_html($location->name) ?>
     </option>
 <?php endforeach ?>
</select>
</li>
<?php
$show_password_fields = apply_filters('show_password_fields', true, $profileuser);
if ($show_password_fields) :
    ?>
<li>
   <p class="form_indent">
      <?php _e('If you would like to change the password type a new one. Otherwise leave this blank.'); ?>
  </p>
</li>
<li id="password">
   <label for="pass3"><?php _e('New Password'); ?></label>
   <input type="password" name="pass1" id="pass3" size="16" value="" autocomplete="off" />
</li>
<li>
   <p class="form_indent">Your password needs to be at least 8 characters long and contain at least 3 of the following:
      an uppercase letter, a lowercase letter, a number and another character (eg £!*%).</p>
  </li>
  <li>
   <label for="pass4"><?php _e('Confirm Password'); ?></label>
   <input type="password" name="pass2" id="pass4" size="16" value="" autocomplete="off" />
</li>
<?php endif; ?>

<?php do_action('show_user_profile', $profileuser); ?>
<li>
   <input type="hidden" name="action" value="profile" />
   <input type="hidden" name="instance" value="<?php // $template->the_instance(); ?>" />
   <input type="hidden" name="user_id" id="user_id" value="<?php echo esc_attr($current_user->ID); ?>" />
   <input type="submit" class="button" value="<?php esc_attr_e('Update my profile'); ?>" name="submit" /> </li>
</ul>
</fieldset>
<fieldset class="subscriptions">
 <h3>Subscriptions</h3>
 <p>You'll receive news and updated guidance on areas of your interest once a week.  You can opt in or out
    of each subscription at any time.</p>

    <div class="newsletter-categories">
        <ul>
            <?php
            $newsletter_cats_count = count($newsletter_cats);
            $col_size = ceil($newsletter_cats_count / 3);

            foreach ($newsletter_cats as $i => $cat) : ?>
            <?php if ($i-1 == $col_size || $i-1 == $col_size * 2) : ?>
        </ul>
    </div>

    <div class="newsletter-categories">
        <ul>
        <?php endif ?>
        <li>
            <input type="checkbox" name="newsletter_cats[<?php echo esc_attr($cat->term_id) ?>]"
            id="sub_<?php echo esc_attr($cat->term_id) ?>" value="<?php echo esc_attr($cat->name) ?>"
            <?php echo is_array($selected_newsletter_cats) &&
            in_array((int) $cat->term_id, $selected_newsletter_cats) ? ' checked="checked"' : null ?>
            <?php echo isset($cat->disabled) ? 'checked="checked" disabled="disabled" ' : null ?>/>
            <label for="sub_<?php echo esc_attr($cat->term_id) ?>"><?php echo esc_html($cat->name) ?></label>
        </li>
    <?php endforeach; ?>
</ul>
</div>


<div class="cta">
    <input type="submit" class="button" value="<?php esc_attr_e('Update my profile') ?>"name="submit" />
</div>

</fieldset>
</form>
</div>
