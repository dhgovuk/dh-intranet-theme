<p>
    <label for="organizer_name">
        <strong><?php _e( 'Organizer Name', 'eventbrite' ); ?></strong>
    </label>
    <input type="text" id="organizer_name" name="event[organizer_name]" class="widefat" value="<?php echo $organizer_name; ?>" />
</p>
<p>
    <label for="organizer_description">
        <strong><?php _e( 'Organizer Description', 'eventbrite' ); ?></strong>
    </label>
    <textarea id="organizer_description" name="event[organizer_description]" class="widefat" style="height: 100px;" ><?php 
        echo $organizer_description; 
    ?></textarea>
</p>

<h2 style="text-align: center"><?php _e( 'OR', 'eventbrite' ); ?></h2>

<p>
    <label for="organizer_id">
        <strong><?php _e( 'Choose a registered organizer', 'eventbrite' ); ?></strong>
    </label>
    <select id="organizer_id" name="event[organizer_id]" class="widefat">
        <option value="" <?php selected( $organizer_id, '' ); ?>>
            <?php _e( 'None', 'eventbrite' ); ?>
        </option>
        <?php if( !empty( $organizers_list ) ): ?>
            <?php foreach ( $organizers_list as $o ): ?>
                <option value="<?php echo $o['id'] ?>" <?php selected( $organizer_id, $o['id'] ); ?>>
                    <?php echo $o['name']; ?>
                </option>
            <?php endforeach; ?>
        <?php endif; ?>
    </select>
</p>
