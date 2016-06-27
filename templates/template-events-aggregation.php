<?php /* Template Name: Events Aggregation */ ?>
<?php the_post(); ?>

<section class="events-section group">
  <div class="events-section-content">
    <header>
      <h1>Events</h1>

        <div class="rich-text entry">
          <?php the_content(); ?>
        </div>

      <div class="event-categories">
        <h4>Jump to</h4>
        <ul>
          <li><a href="#corporate-events">Corporate events</a></li>
          <li><a href="#">Connecting Comes to You events</a></li>
          <li><a href="#">Policy events</a></li>
          <li><a href="#">Analysts' Induction Academy</a></li>
          <li><a href="#">Social and well-being events</a></li>
        </ul>
        <?php # could this be generated automatically from the list categories spat out? ?>
      </div>
    </header>
    <aside class="sidebar events-section-sidebar">
      <?php get_template_part('partials/service-links'); ?>
    </aside>
  </div>

  <article>
    <h2 id="corporate-events">Corporate events</h2>
    <?php # category slug to be used as heading anchor ?>

    <!-- foreach -->
    <?php get_template_part('partials/events/event-item'); ?>
    <?php get_template_part('partials/events/event-item'); ?>
    <?php get_template_part('partials/events/event-item'); ?>
    <?php get_template_part('partials/events/event-item'); ?>
    <?php get_template_part('partials/events/event-item'); ?>
    <?php get_template_part('partials/events/event-item'); ?>
    <!-- endfor -->

  <div class="buttons"><a href="#" class="button">More corporate events</a></div>
  </article>

</section>

