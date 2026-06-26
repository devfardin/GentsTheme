<?php
if (!defined('ABSPATH')) {
    exit;
}

$categories = get_terms(
    array(
        'taxonomy' => 'product_cat',
        'hide_empty' => true,
    )
)
    ?>
<section class="gt-categories">
    <div class="container">
        <h2> Product Categories </h2>
        <div class="gt-categories__grid">
            <?php foreach ($categories as $category): ?>
                <div class="category">
                    <?php
                    $thumbnail_id = get_woocommerce_term_meta($category->term_id, 'thumbnail_id', true);
                    $image = wp_get_attachment_url($thumbnail_id);
                    ?>
                    <a class="category-link" href="<?php echo esc_attr(get_term_link($category)) ?>">
                        <img class="category_feature" src="<?php echo esc_attr($image) ?>"
                            alt="<?php echo esc_attr($category->name) ?>">
                        <span class="category-name">
                            <span class="category-name__text"><?php echo esc_html($category->name) ?></span>
                            <svg aria-hidden="true" role="img" focusable="false" xmlns="http://www.w3.org/2000/svg" width="8" height="8" viewBox="0 0 8 8" fill="currentColor" class="category-arrow"><path d="M0.861539 8L0 7.13846L5.90769 1.23077H0.615385V0H8V7.38462H6.76923V2.09231L0.861539 8Z" fill="currentColor"></path></svg>
                        </span>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
</section>