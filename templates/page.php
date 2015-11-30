<?php

/*
* Wordpress' template engine will only use custom category templates for posts, not pages.
* So test if this page is in the Policy Article category and load appropriate template part.
*/
$cats = get_the_category();
$isPolicyArticle = false;
foreach ($cats as $cat) {
    if ($cat->slug === 'policy-article') {
        $isPolicyArticle = true;
        break;
    }
}

if ($isPolicyArticle) {
    get_template_part('partials/policy-article-page');
} else {
    get_template_part('partials/content', 'page');
}
