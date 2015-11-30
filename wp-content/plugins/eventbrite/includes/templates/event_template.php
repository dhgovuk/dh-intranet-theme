<label class="screen-reader-text" for="post_template">
    <?php _e( 'Page Template' ) ?>
</label>
<select name="post_template" id="post_template">
    <option value='default'><?php _e( 'Default Template' ); ?></option>
    <?php page_template_dropdown( $post_template ); ?>
</select>