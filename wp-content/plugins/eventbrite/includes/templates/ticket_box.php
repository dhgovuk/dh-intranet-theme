<?php
    wp_nonce_field( 'eventbrite', 'eventbrite_ticket_nonce' );
    $tid = 0;
?>
<ul class="tickets">
    <li style="width: 30%; float: left; padding: 5px 10px;">
        <?php include 'ticket.php'; ?>
    </li>
    <?php if( !empty( $tickets ) ) : ?>
        <?php foreach ( $tickets as $t ): ?>
            <li style="width: 30%; float: left; padding: 5px 10px;">
                <?php
                    // Reset all variables
                    extract( $ticket_fields );
                    
                    $tid++;
                    ob_start();
                    extract( $t );
                    include 'ticket.php';
                    echo ob_get_clean();
                ?>
            </li>
        <?php endforeach; ?>
    <?php endif; ?>
</ul>
<div style="clear: both;"></div>
