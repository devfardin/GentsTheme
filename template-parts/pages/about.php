<?php if (!defined('ABSPATH')) exit; ?>

<!-- Breadcrumb -->
<div class="breadcrumb-bar">
    <div class="container">
        <h1 class="breadcrumb-title">About Us</h1>
        <nav class="breadcrumb" aria-label="Breadcrumb">
            <a href="<?php echo esc_url(home_url('/')); ?>">Home</a>
            <span class="breadcrumb-sep">/</span>
            <span class="breadcrumb-current">About Us</span>
        </nav>
    </div>
</div>

<!-- Hero / Story Section -->
<section class="about-hero">
    <div class="container about-hero__grid">
        <div class="about-hero__image">
            <img src="<?php echo esc_url(get_template_directory_uri()); ?>/assets/images/home-1.png"
                 alt="Gents Time Story" loading="lazy">
            <div class="about-hero__badge">
                <span class="about-hero__badge-number">10+</span>
                <span class="about-hero__badge-label">Years of Excellence</span>
            </div>
        </div>
        <div class="about-hero__content">
            <span class="about-eyebrow">Our Story</span>
            <h2 class="about-heading">Crafted for the Modern Gentleman</h2>
            <p class="about-text">
                Gents Time was born from a passion for timeless style and impeccable craftsmanship.
                We believe every man deserves clothing that speaks confidence — pieces that are
                refined, durable, and effortlessly elegant.
            </p>
            <p class="about-text">
                From our carefully curated collections to our commitment to quality fabrics,
                every detail is chosen with the discerning gentleman in mind.
            </p>
            <a href="<?php echo esc_url(home_url('/shop')); ?>" class="btn-primary">Explore Collection</a>
        </div>
    </div>
</section>

<!-- Values Section -->
<section class="about-values">
    <div class="container">
        <span class="about-eyebrow" style="text-align:center;display:block;">Why Choose Us</span>
        <h2 class="about-heading" style="text-align:center;">Our Core Values</h2>
        <div class="about-values__grid">
            <div class="about-value-card">
                <div class="about-value-card__icon">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                </div>
                <h3 class="about-value-card__title">Premium Quality</h3>
                <p class="about-value-card__text">Every garment is crafted from carefully selected fabrics that stand the test of time.</p>
            </div>
            <div class="about-value-card">
                <div class="about-value-card__icon">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4zm0 4l5 2.18V11c0 3.5-2.33 6.79-5 7.93-2.67-1.14-5-4.43-5-7.93V7.18L12 5z"/></svg>
                </div>
                <h3 class="about-value-card__title">Trusted Brand</h3>
                <p class="about-value-card__text">Over a decade of serving gentlemen who value style, comfort, and authenticity.</p>
            </div>
            <div class="about-value-card">
                <div class="about-value-card__icon">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M20 8h-3V4H3c-1.1 0-2 .9-2 2v11h2c0 1.66 1.34 3 3 3s3-1.34 3-3h6c0 1.66 1.34 3 3 3s3-1.34 3-3h2v-5l-3-4zM6 18.5c-.83 0-1.5-.67-1.5-1.5s.67-1.5 1.5-1.5 1.5.67 1.5 1.5-.67 1.5-1.5 1.5zm13.5-9 1.96 2.5H17V9.5h2.5zm-1.5 9c-.83 0-1.5-.67-1.5-1.5s.67-1.5 1.5-1.5 1.5.67 1.5 1.5-.67 1.5-1.5 1.5z"/></svg>
                </div>
                <h3 class="about-value-card__title">Fast Delivery</h3>
                <p class="about-value-card__text">Swift, secure shipping across Bangladesh so your order arrives fresh and on time.</p>
            </div>
            <div class="about-value-card">
                <div class="about-value-card__icon">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M11.5 2C6.81 2 3 5.81 3 10.5S6.81 19 11.5 19h.5v3c4.86-2.34 8-7 8-11.5C20 5.81 16.19 2 11.5 2zm1 14.5h-2v-2h2v2zm0-4h-2c0-3.25 3-3 3-5 0-1.1-.9-2-2-2s-2 .9-2 2h-2c0-2.21 1.79-4 4-4s4 1.79 4 4c0 2.5-3 2.75-3 5z"/></svg>
                </div>
                <h3 class="about-value-card__title">Expert Support</h3>
                <p class="about-value-card__text">Our dedicated team is always ready to help you find the perfect fit and style.</p>
            </div>
        </div>
    </div>
</section>

<!-- Stats Section -->
<section class="about-stats">
    <div class="container">
        <div class="about-stats__grid">
            <div class="about-stat">
                <span class="about-stat__number">10K+</span>
                <span class="about-stat__label">Happy Customers</span>
            </div>
            <div class="about-stat">
                <span class="about-stat__number">500+</span>
                <span class="about-stat__label">Products</span>
            </div>
            <div class="about-stat">
                <span class="about-stat__number">10+</span>
                <span class="about-stat__label">Years Experience</span>
            </div>
            <div class="about-stat">
                <span class="about-stat__number">98%</span>
                <span class="about-stat__label">Satisfaction Rate</span>
            </div>
        </div>
    </div>
</section>

<!-- Team Section -->
<section class="about-team">
    <div class="container">
        <span class="about-eyebrow" style="text-align:center;display:block;">The People Behind</span>
        <h2 class="about-heading" style="text-align:center;">Meet Our Team</h2>
        <div class="about-team__grid">
            <?php
            $team = [
                ['name' => 'Arif Rahman',    'role' => 'Founder & CEO',       'initials' => 'AR'],
                ['name' => 'Tanvir Hossain', 'role' => 'Head of Design',      'initials' => 'TH'],
                ['name' => 'Nadia Islam',    'role' => 'Customer Experience', 'initials' => 'NI'],
            ];
            foreach ($team as $member) : ?>
                <div class="about-team-card">
                    <div class="about-team-card__avatar"><?php echo esc_html($member['initials']); ?></div>
                    <h3 class="about-team-card__name"><?php echo esc_html($member['name']); ?></h3>
                    <p class="about-team-card__role"><?php echo esc_html($member['role']); ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="about-cta">
    <div class="container about-cta__inner">
        <h2 class="about-cta__heading">Ready to Elevate Your Style?</h2>
        <p class="about-cta__text">Discover our latest collections crafted for the modern gentleman.</p>
        <div class="about-cta__actions">
            <a href="<?php echo esc_url(home_url('/shop')); ?>" class="btn-primary">Shop Now</a>
            <a href="<?php echo esc_url(home_url('/contact')); ?>" class="btn-outline">Contact Us</a>
        </div>
    </div>
</section>
