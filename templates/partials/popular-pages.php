<?php if (have_rows('popular_pages', 'option')): ?>

<section class="popular-pages-widget group">
    <h3>Popular pages</h3>
    <div class="popular-pages">
        <ul>
        <?php while (have_rows('popular_pages', 'option')): the_row();
            $text = get_sub_field('text');
            $url = get_sub_field('url');
        ?>
            <li>
                <a href="<?php echo $url; ?>"> <?php echo $text; ?> </a>
            </li>
        <?php endwhile; ?>
        </ul>
    </div>
</section>

<?php endif; ?>
