<?php 
$slides = [
    'http://gents-time.local/wp-content/uploads/2026/06/T_1_back.jpg',
    'https://fashion-cs.wpmethods.com/wp-content/uploads/2026/04/banner-offer.webp',
    'https://fashion-cs.wpmethods.com/wp-content/uploads/2026/04/banner-image-click-shop.png',
];
?>

<section class="hero">
    <div class="container">
        <div class="hero-slider">
            <div class="hero-track">
                <?php foreach ( $slides as $i => $src ) : ?>
                    <div class="hero-slide <?php echo $i === 0 ? 'active' : ''; ?>">
                        <img src="<?php echo esc_url( $src ); ?>" alt="Slide <?php echo $i + 1; ?>">
                    </div>
                <?php endforeach; ?>
            </div>

            <button class="hero-btn prev" aria-label="Previous">&#8592;</button>
            <button class="hero-btn next" aria-label="Next">&#8594;</button>

            <div class="hero-dots">
                <?php foreach ( $slides as $i => $_ ) : ?>
                    <span class="dot <?php echo $i === 0 ? 'active' : ''; ?>" data-index="<?php echo $i; ?>"></span>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>
