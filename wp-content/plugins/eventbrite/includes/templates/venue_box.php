<p>
    <label for="venue_organizer_id">
        <strong><?php _e( 'Related Organizer', 'eventbrite' ); ?></strong>
    </label>
    <select id="venue_organizer_id" name="event[venue_organizer_id]" class="widefat">
        <?php if( !empty( $organizers_list ) ): ?>
            <?php foreach ( $organizers_list as $o ): ?>
                <option value="<?php echo $o['id'] ?>" <?php selected( $venue_organizer_id, $o['id'] ); ?>>
                    <?php echo $o['name']; ?>
                </option>
            <?php endforeach; ?>
        <?php else: ?>
            <option value="" <?php selected( $venue_organizer_id, '' ); ?>>
                <?php _e( 'Please register an organizer and sync with Eventbrite', 'eventbrite' ); ?>
            </option>
        <?php endif; ?>
    </select>
</p>
<p>
    <label for="venue">
        <strong><?php _e( 'Venue Name', 'eventbrite' ); ?></strong>
    </label>
    <input type="text" id="venue" name="event[venue]" class="widefat" value="<?php echo $venue; ?>" />
</p>
<p>
    <label for="adress">
        <strong><?php _e( 'Venue address', 'eventbrite' ); ?></strong>
    </label>
    <textarea id="adress" name="event[adress]" class="widefat" style="height: 100px;" ><?php 
        echo $adress; 
    ?></textarea>
</p>
<p>
    <label for="city">
        <strong><?php _e( 'City', 'eventbrite' ); ?></strong>
    </label>
    <input type="text" id="city" name="event[city]" class="widefat" value="<?php echo $city; ?>" />
</p>
<p>
    <label for="region">
        <strong><?php _e( 'Region', 'eventbrite' ); ?></strong>
    </label>
    <input type="text" id="region" name="event[region]" class="widefat" value="<?php echo $region; ?>" />
    <em><?php _e( 'The venue region depending on the country. 2-letter state code is required for US addresses.', 'eventbrite' ); ?></em>
</p>
<p>
    <label for="postal_code">
        <strong><?php _e( 'Postal Code', 'eventbrite' ); ?></strong>
    </label>
    <input type="text" id="postal_code" name="event[postal_code]" class="widefat" value="<?php echo $postal_code; ?>" />
</p>
<p>
    <label for="country_code">
        <strong><?php _e( 'Country Code', 'eventbrite' ); ?></strong>
    </label>
    <input type="text" id="country_code" name="event[country_code]" class="widefat"
           value="<?php echo isset($country_code) && $country_code != '' ? $country_code : 'GB'; ?>" />
    <em><?php _e( '2-letter country code.', 'eventbrite' ); ?></em>
</p>

<h2 style="text-align: center; display:none"><?php _e( 'OR', 'eventbrite' ); ?></h2>

<p style="display:none;">
    <label for="venue_id">
        <strong><?php _e( 'Choose a registered venue', 'eventbrite' ); ?></strong>
    </label>
    <select id="venue_id" name="event[venue_id]" class="widefat">
        <option value="" selected="selected">
            <?php _e( 'None', 'eventbrite' ); ?>
        </option>
    </select>
</p>
