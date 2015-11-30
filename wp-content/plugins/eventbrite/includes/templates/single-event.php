<?php get_header(); ?>

        <div id="container">
            <div id="content" role="main">
    
            <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
                <?php extract ( EBT::get_event( get_the_ID() ) ); ?>
                <div class="post download">
                    <h1 class="entry-title">
                        <a href="<?php the_permalink(); ?>" rel="bookmark" title="<?php the_title_attribute(); ?>">
                            <?php the_title(); ?>
                        </a>
                    </h1>

                    <div class="clear"></div>
                    
                    <div class="entry-content">
                        <?php the_content(); ?>
                    </div>
                    
                    <div class="event-tickets">
                        <iframe src="http://www.eventbrite.com/tickets-external?eid=<?php echo $event_id ?>" height="306" width="100%" ></iframe>
                    </div>
                </div>
            <?php endwhile; endif; ?>

            </div><!-- #content -->
        </div><!-- #container -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>
