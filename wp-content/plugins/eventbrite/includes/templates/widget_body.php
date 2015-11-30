<?php
    extract( $args, EXTR_SKIP );
    echo $before_widget;

    $event_items = EBP::widget(12);

    if(class_exists('Event_Categories')) {
        // Length for truncating the event titles.
        $event_title_length       = Event_Categories::get_event_title_length();
        $event_description_length = Event_Categories::get_event_description_length();
    }

    foreach($event_items as $k => $item) {
        if($event_title_length) {
            $event_items[$k]['title'] = wtg_truncate_string($item['title'], $event_title_length);
        }

        $event_items[$k]['start_day']       = date('j', strtotime($item['start_date']));
        $event_items[$k]['start_month']     = date('M', strtotime($item['start_date']));
        $event_items[$k]['start_formatted'] = date('j F Y H:i', strtotime($item['start_date']));

        if(strlen($item['description']) > $event_description_length) {
            $event_items[$k]['description'] = wtg_truncate_string($item['description'], $event_description_length);

            $event_items[$k]['description'] = str_ireplace('...', '', $event_items[$k]['description']) . "<a href=\"{$item['wp_link']}\">...more</a>";
        } else {
            $event_items[$k]['description'] = $item['description'];
        }

    }

if ( $title ) : ?>
    <header>
        <h2><a href="/events-aggregation"><?php echo $title; ?></a></h2>
    </header>
<?php endif ?>

<!-- standards events display -->
<div class="homepage-events" id="homepage-events">
    <?php
    $count = 0;
    foreach ($event_items as $item) :
        $count++;
        // if ($count % 2) echo "<div style='clear:both'></div>";
        if ($count > 6) break; //we want a max of four visible on standard resolutions
    ?>
    <div class="event-item">
        <div class="event-date">
            <?php echo $item['start_day']; ?><br>
            <?php echo $item['start_month']; ?>
        </div>

        <div class="event-info">
            <h3>
                <a href="<?php echo $item['wp_link'] ?>" title="<?php echo $item['title'] ?>">
                    <?php echo $item['title'] ?>
                </a>
            </h3>
            <strong><?php echo $item['start_formatted'] ?></strong>
            <p><?php echo $item['description'] ?></p>
        </div>
    </div>
    <?php endforeach; ?>
</div>


<div class="cta">
    <a href="/events-aggregation" class="button">All <?php echo strtolower($title) ?>&nbsp;&nbsp;</a>
</div>


<?php echo $after_widget; ?>
