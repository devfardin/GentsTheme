<?php 

get_header();

get_template_part('template-parts/home/hero');
get_template_part('template-parts/home/categories');
get_template_part('template-parts/home/popular-products');
get_template_part('template-parts/home/new-arrivals');
?>


    <?php get_template_part( 'template-parts/recently-viewed' ); ?>

<?php

get_footer();