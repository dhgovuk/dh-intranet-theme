<div class="todo">
  <h3>Item details</h3>
  <ul>
    <li class="page-review">
      Due on:
      <?php
        $date = get_field('due_date');
        $y = substr($date, 0, 4);
        $m = substr($date, 5, 2);
        $d = substr($date, 8, 2);
        $showDate = date('d-m-Y', strtotime("{$d}-{$m}-{$y}"));
      ?>
      <span itemprop="date"><?php echo esc_html($showDate) ?></span>
    </li>
  </ul>
</div>
