<?php
/**
 * Hero section
 */
if (!defined('ABSPATH')) exit;

$opts   = get_option('gent_hero_slider', []);
$slides = $opts['slides']         ?? [];
$stats  = $opts['stats']          ?? [];
$speed  = (int) ($opts['autoplay_speed'] ?? 5000);

if (empty($slides)) return;
?>

<div class="hs-hero" data-speed="<?php echo esc_attr($speed); ?>">

    <?php foreach ($slides as $index => $slide):
        $img    = $slide['image_id'] ? wp_get_attachment_image_url($slide['image_id'], 'full') : '';
        $active = $index === 0 ? ' hs-slide--active' : '';
    ?>
        <div class="hs-slide<?php echo $active; ?>">
            <div class="hs-slide__bg" style="background-image:url('<?php  echo esc_url($img); ?>')"></div>
        </div>
    <?php endforeach; ?>

    <!-- Overlays -->
    <div class="hs-overlay hs-overlay--lr"></div>
    <div class="hs-overlay hs-overlay--tb"></div>
    <div class="hs-overlay hs-overlay--grid"></div>

    <!-- Content + Stats -->
    <div class="container">
        <div class="hs-content-wrap">
        <div class="hs-inner">

            <!-- Slide content -->
            <div class="hs-content-col">
                <?php foreach ($slides as $index => $slide):
                    if (!empty($slide['image_only'])) continue;
                    $active   = $index === 0 ? ' hs-content--active' : '';
                    $heading  = nl2br(wp_kses($slide['heading'] ?? '', ['span' => [], 'br' => []]));
                ?>
                    <div class="hs-content<?php echo $active; ?>" data-slide="<?php echo $index; ?>">

                        <?php if (!empty($slide['badge'])): ?>
                            <div class="hs-badge">
                                <span class="hs-badge__line"></span>
                                <span class="hs-badge__text"><?php echo esc_html($slide['badge']); ?></span>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($slide['heading'])): ?>
                            <h1 class="hs-heading"><?php echo $heading; ?></h1>
                        <?php endif; ?>

                        <?php if (!empty($slide['description'])): ?>
                            <p class="hs-desc"><?php echo esc_html($slide['description']); ?></p>
                        <?php endif; ?>

                        <?php if (!empty($slide['btn_primary_text']) || !empty($slide['btn_secondary_text'])): ?>
                            <div class="hs-buttons">
                                <?php if (!empty($slide['btn_primary_text'])): ?>
                                    <a href="<?php echo esc_url($slide['btn_primary_url'] ?: '#'); ?>"
                                        class="hs-btn hs-btn--primary">
                                        <?php echo esc_html($slide['btn_primary_text']); ?>
                                        <i class="ri-arrow-right-line"></i>
                                    </a>
                                <?php endif; ?>
                                <?php if (!empty($slide['btn_secondary_text'])): ?>
                                    <a href="<?php echo esc_url($slide['btn_secondary_url'] ?: '#'); ?>"
                                        class="hs-btn hs-btn--secondary">
                                        <?php echo esc_html($slide['btn_secondary_text']); ?>
                                    </a>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>

                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    </div>

    <!-- Arrows -->
    <?php if (count($slides) > 1): ?>
        <button class="hs-arrow hs-arrow--prev">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                <path d="M6.52367 9.16658H16.6666V10.8332H6.52367L10.9936 15.3032L9.81515 16.4817L3.33331 9.99992L9.81515 3.51807L10.9936 4.69657L6.52367 9.16658Z" fill="white"/>
            </svg>
        </button>
        <button class="hs-arrow hs-arrow--next">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                <path d="M13.4763 9.16658L9.00631 4.69657L10.1848 3.51807L16.6666 9.99992L10.1848 16.4817L9.00631 15.3032L13.4763 10.8332H3.33331V9.16658H13.4763Z" fill="white"/>
            </svg>
        </button>
    <?php endif; ?>

    <!-- Dots -->
    <?php if (count($slides) > 1): ?>
        <div class="hs-dots">
            <?php foreach ($slides as $index => $slide): ?>
                <button class="hs-dot<?php echo $index === 0 ? ' hs-dot--active' : ''; ?>"
                    data-index="<?php echo $index; ?>"></button>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <!-- Scroll indicator -->
    <div class="hs-scroll">
        <span class="hs-scroll__label">SCROLL</span>
        <div class="hs-scroll__line"></div>
    </div>

</div>
