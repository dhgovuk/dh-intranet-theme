<div class="events-single">

    <?php while (have_posts()) : ?>
        <?php the_post(); ?>
        <?php $event = EBT::get_event(get_the_ID()); ?>
        <article class="post download">

            <header>
                <h1>
                    <?php the_title(); ?>
                </h1>
            </header>

            <div class="meta">
                <p class="event-start"><strong>Start time:</strong>  <?php echo esc_html(date('j F Y H:i', strtotime($event['start_date'])))?></p>
                <p class="event-end"><strong>End time:</strong> <?php echo esc_html(date('j F Y H:i', strtotime($event['end_date']))) ?></p>
                <?php echo isset($event['venue']) ? "<p class='event-location'><strong>Location:</strong> ".esc_html($event['venue'])." </p>" : '' ?>
                <?php echo isset($event['adress']) ? "<p class='event-address'><strong>Address:</strong> ". esc_html(implode(', ', array_filter(array($event['adress'], $event['city'], $event['region'], $event['postal_code'])))).'</p>' : '' ?>
            </div>

            <div class="rich-text entry">
                <?php the_content(); ?>
            </div>

            <div class="cta">
                <a href="http://www.eventbrite.com/event/<?php echo esc_attr($event['event_id']) ?>?ref=ebtn" target="_blank"
                class="button">Register for this event</a>
            </div>

        </article>
    <?php endwhile ?>

</div>

