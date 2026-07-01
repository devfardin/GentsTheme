<?php
get_header();
?>

<!-- Breadcrumb -->
<div class="breadcrumb-bar">
    <div class="container">
        <h1 class="breadcrumb-title"><?php the_title(); ?></h1>
        <nav class="breadcrumb" aria-label="Breadcrumb">
            <a href="<?php echo esc_url( home_url('/') ); ?>"> Home </a>
            <span class="breadcrumb-sep" aria-hidden="true">&#8250;</span>
            <span class="breadcrumb-current" aria-current="page"><?php the_title(); ?></span>
        </nav>
    </div>
</div>

<div class="container" style="padding-block: 40px 60px;">
    <?php while ( have_posts() ) : the_post(); ?>
        <div class="page-content">
            <?php the_content(); ?>
        </div>
    <?php endwhile; ?>
</div>

<?php
get_footer();
