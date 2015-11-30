<?php/*Template Name: IT Updates*/?>
<header>
    <?php the_title('<h1>', '</h1>'); ?>
</header>

<div class="rich-text entry it-updates">
  <article>
    <?php echo \DHIntranet\IT_Updates::display_all_statuses(); ?>
  </article>
</div>
