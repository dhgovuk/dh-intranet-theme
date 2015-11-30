<?php wp_nonce_field( 'eventbrite', 'eventbrite_nonce' ); ?>
<?php if( is_array( $eventbrite_errors ) ) : ?>
    <div id="message" class="updated fade">
    <?php foreach ( $eventbrite_errors as $e ): ?>
        <p>
            <strong><?php echo $e->error_type; ?></strong>:
            <em><?php echo $e->error_message; ?></em>
        </p>
    <?php endforeach; ?>
    </div>
<?php endif; ?>
<p>
    <label for="eventbrite_ready">
        <input id="eventbrite_ready" name="event[eventbrite_ready]" type="checkbox"
			<?php disabled( $event_template, 1 ); ?> checked="checked" />
        <strong><?php _e( 'Sync on Eventbrite', 'eventbrite' ); ?></strong>
    </label>
</p>
<p>
    <label for="event_template">
        <input id="event_template" name="event[event_template]" type="checkbox" <?php disabled( $eventbrite_ready || $event_id, 1 ); ?> <?php checked( $event_template, 1 ); ?>/>
        <strong><?php _e( 'Events template', 'eventbrite' ); ?></strong>
    </label>
</p>
<p>
    <label for="start_date">
        <strong><?php _e( 'Start Date', 'eventbrite' ); ?> <br />Format: YYYY-MM-DD HH:MM:SS</strong>
    </label>
    <input type="text" id="start_date" name="event[start_date]" class="widefat date-time-pick" value="<?php echo $start_date; ?>" />
</p>
<p>
    <label for="end_date">
        <strong><?php _e( 'End Date', 'eventbrite' ); ?> <br />Format: YYYY-MM-DD HH:MM:SS</strong>
    </label>
    <input type="text" id="end_date" name="event[end_date]" class="widefat" value="<?php echo $end_date; ?>" />
</p>
<p>
    <label for="timezone">
        <strong><?php _e( 'Timezone', 'eventbrite' ); ?></strong>
    </label>
    <select id="timezone" name="event[timezone]" class="widefat">
        <?php echo wp_timezone_choice( $timezone ? $timezone : 'Europe/London' ); ?>
    </select>
</p>
<!-- <p>
    <label for="privacy">
        <strong><?php _e( 'Privacy', 'eventbrite' ); ?></strong>
    </label>
    <select id="privacy" name="event[privacy]" class="widefat">
        <option value="0" <?php selected( $privacy, 0 ); ?>>
            <?php _e( 'Private', 'eventbrite' ); ?>
        </option>
        <option value="1" <?php selected( $privacy, 1 ); ?>>
            <?php _e( 'Public', 'eventbrite' ); ?>
        </option>
    </select>
</p> -->
<input type="hidden" name="event[privacy]" id="privacy" value="private" />
<p>
    <label for="capacity">
        <strong><?php _e( 'Capacity', 'eventbrite' ); ?></strong>
    </label>
    <input type="text" id="capacity" name="event[capacity]" class="widefat" value="<?php echo $capacity; ?>" />
</p>
<!--
<p>
    <label for="currency">
        <strong><?php _e( 'Currency', 'eventbrite' ); ?></strong>
    </label>
    <select id="currency" name="event[currency]" class="widefat">
        <?php foreach ( $currency_list as $c ): ?>
            <option value="<?php echo $c ?>" <?php selected( $currency, $c ); ?>>
                <?php echo $c; ?>
            </option>
        <?php endforeach; ?>
    </select>
</p>
-->
<input type="hidden" name="event[currency]" value="GBP" />
<p style="display:none">
    <label for="instructions_check">
        <?php _e( 'Instructions for Pay by Check', 'eventbrite' ); ?>
    </label>
    <textarea id="instructions_check" name="event[instructions_check]" class="widefat"><?php
        echo $instructions_check;
    ?></textarea>
</p>
<p style="display:none">
    <label for="accept_cash">
        <input id="accept_cash" name="event[accept_cash]" type="checkbox" checked="checked" />
        <strong><?php _e( 'Pay with Cash', 'eventbrite' ); ?></strong>
    </label>
</p>
<p style="display:none">
    <label for="instructions_cash">
        <?php _e( 'Instructions for Pay with Cash', 'eventbrite' ); ?>
    </label>
    <textarea id="instructions_cash" name="event[instructions_cash]" class="widefat"><?php
        echo $instructions_cash;
    ?></textarea>
</p>

