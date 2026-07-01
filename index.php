<?php
get_header();
?>

<div class="container">
    <?php if ( have_posts() ) : ?>

        <div class="gt-posts-grid">
            <?php while ( have_posts() ) : the_post(); ?>
                <article id="post-<?php the_ID(); ?>" <?php post_class('gt-post-card'); ?>>
                    <?php if ( has_post_thumbnail() ) : ?>
                        <a href="<?php the_permalink(); ?>" class="gt-post-thumb">
                            <?php the_post_thumbnail('medium_large'); ?>
                        </a>
                    <?php endif; ?>
                    <div class="gt-post-body">
                        <span class="gt-post-date"><?php echo get_the_date(); ?></span>
                        <h2 class="gt-post-title">
                            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                        </h2>
                        <p class="gt-post-excerpt"><?php the_excerpt(); ?></p>
                        <a href="<?php the_permalink(); ?>" class="gt-post-readmore">Read More</a>
                    </div>
                </article>
            <?php endwhile; ?>
        </div>

        <?php the_posts_pagination(); ?>

    <?php else : ?>
        <p><?php esc_html_e( 'No posts found.', 'gentstime' ); ?></p>
    <?php endif; ?>
</div>

<?php
get_footer();
