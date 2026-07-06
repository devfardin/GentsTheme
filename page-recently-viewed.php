<?php
/**
 * Template Name: Recently Viewed Products
 * GentsTime — Recently Viewed Products Page
 */
defined( 'ABSPATH' ) || exit;

get_header();
?>

<div class="breadcrumb-bar">
    <div class="container">
        <h1 class="breadcrumb-title">Recently Viewed</h1>
        <nav class="breadcrumb" aria-label="Breadcrumb">
            <a href="<?php echo esc_url( home_url( '/' ) ); ?>">Home</a>
            <span class="breadcrumb-sep" aria-hidden="true">&#8250;</span>
            <span class="breadcrumb-current" aria-current="page">Recently Viewed</span>
        </nav>
    </div>
</div>

<div class="container gt-rv-page">
    <?php get_template_part( 'template-parts/recently-viewed' ); ?>
</div>

<?php get_footer(); ?>
