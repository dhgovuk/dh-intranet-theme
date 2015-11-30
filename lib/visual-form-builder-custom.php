<?php

add_filter('vfb_before_form_output', 'vfb_filter_before_form', 10, 2);

function vfb_filter_before_form($output, $form_id)
{
    return '<p>Fields marked with <span class="vfb-required-asterisk last-child">*</span> are required</p>';
}
