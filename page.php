<?php
get_header();
?>

<!-- Breadcrumb -->
<div class="breadcrumb-bar">
    <div class="container">
        <h1 class="breadcrumb-title"><?php the_title(); ?></h1>
        <nav class="breadcrumb" aria-label="Breadcrumb">
            <a href="<?php echo esc_url(home_url('/')); ?>"> Home </a>
            <span class="breadcrumb-sep" aria-hidden="true">&#8250;</span>
            <span class="breadcrumb-current" aria-current="page"><?php the_title(); ?></span>
        </nav>
    </div>
</div>


    <?php while (have_posts()):
        the_post(); ?>
        <div class="page-content">
            <?php
            $template = get_page_template_slug();

            if (is_page('about-us')) {
                get_template_part('template-parts/pages/about');
            } else if (is_page('new-arrivals')) {
                get_template_part('template-parts/pages/new-arrivals');
            } else if (is_page('contact')) {
                get_template_part('template-parts/pages/contact');
            }else if (is_page('privacy-policy')) {
                get_template_part('template-parts/pages/privacy-policy');
            }else if (is_page('terms-and-conditions')) {
                get_template_part('template-parts/pages/terms-and-conditions');
            }else if (is_page('refund-and-return-policy')) {
                get_template_part('template-parts/pages/refund-and-return-policy');
            } else {
               ?>
               <div>
                   <?php the_content(); ?>
               </div>
               <?php 
            }
            ?>
        </div>
    <?php endwhile; ?>

<?php

get_footer();
