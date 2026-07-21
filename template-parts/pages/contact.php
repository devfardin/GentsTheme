<?php if (!defined('ABSPATH'))
    exit;

$gent_general = get_option('gent_general');
$gent_general = is_array($gent_general) ? $gent_general : [];

$options = get_option('gent_whatsapp');
$options = is_array($options) ? $options : [];
$whatsapp_number = isset($options['whatsapp_number']) ? $options['whatsapp_number'] : '';
$message = urlencode(isset($options['predefined_message']) ? $options['predefined_message'] : '');
?>


<!-- Contact Info + Form -->
<section class="contact-main">
    <div class="container">
        <div class="contact-grid">

            <!-- Info Cards -->
            <div class="contact-info">

                <div class="contact-info-card">
                    <div class="contact-info-card__icon">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                            <path
                                d="M6.62 10.79a15.05 15.05 0 0 0 6.59 6.59l2.2-2.2a1 1 0 0 1 1.01-.24 11.47 11.47 0 0 0 3.58.57 1 1 0 0 1 1 1V20a1 1 0 0 1-1 1A17 17 0 0 1 3 4a1 1 0 0 1 1-1h3.5a1 1 0 0 1 1 1 11.47 11.47 0 0 0 .57 3.58 1 1 0 0 1-.25 1.01l-2.2 2.2z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="contact-info-card__title">Phone</h3>
                        <p class="contact-info-card__text"><?php echo esc_html($gent_general['phone_number']) ?></p>
                    </div>
                </div>

                <?php if ($whatsapp_number): ?>
                    <div class="contact-info-card">
                        <div class="contact-info-card__icon">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                                <path
                                    d="M12.04 2C6.52 2 2.04 6.48 2.04 12c0 1.77.46 3.49 1.33 5.01L2 22l5.13-1.34A9.94 9.94 0 0 0 12.04 22C17.56 22 22.04 17.52 22.04 12S17.56 2 12.04 2zm5.83 14.17c-.25.71-1.46 1.35-2.02 1.44-.52.08-1.17.12-1.89-.11-.44-.14-1-.33-1.73-.64-3.04-1.31-5.02-4.53-5.17-4.74-.15-.21-1.24-1.65-1.24-3.15s.79-2.23 1.07-2.54c.29-.31.63-.39.84-.39.21 0 .42 0 .6.01.19.01.45-.07.7.54.25.61.86 2.11.94 2.27.08.17.13.36.03.57-.1.21-.15.34-.31.52-.15.18-.33.4-.47.53-.16.16-.32.34-.14.67.18.33.81 1.34 1.74 2.17 1.2 1.07 2.21 1.41 2.53 1.57.31.16.5.13.68-.08.18-.21.77-.89.98-1.2.21-.31.42-.26.71-.15.29.1 1.82.86 2.13 1.01.31.16.52.23.6.36.08.13.08.75-.17 1.46z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="contact-info-card__title">WhatsApp</h3>
                            <p class="contact-info-card__text"><a
                                    href="https://wa.me/<?php echo esc_attr($whatsapp_number); ?>?text=<?php echo $message; ?>"
                                    target="_blank" rel="nofollow noreferrer">
                                    <?php echo esc_html($whatsapp_number); ?>
                                </a>
                            </p>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="contact-info-card">
                    <div class="contact-info-card__icon">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                            <path
                                d="M20 4H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2zm0 4-8 5-8-5V6l8 5 8-5v2z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="contact-info-card__title">Email</h3>
                        <p class="contact-info-card__text"><?php echo esc_html($gent_general['company_email']) ?></p>
                    </div>
                </div>

                <div class="contact-info-card">
                    <div class="contact-info-card__icon">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                            <path
                                d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5A2.5 2.5 0 1 1 12 6.5a2.5 2.5 0 0 1 0 5z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="contact-info-card__title">Address</h3>
                        <p class="contact-info-card__text"><?php echo esc_html($gent_general['address']) ?></p>
                    </div>
                </div>

                <div class="contact-info-card">
                    <div class="contact-info-card__icon">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 2a10 10 0 1 0 0 20A10 10 0 0 0 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="contact-info-card__title">Business Hours</h3>
                        <p class="contact-info-card__text">Sat – Thu: 9AM – 9PM</p>
                    </div>
                </div>

            </div>

            <!-- Contact Form via Shortcode -->
            <div class="contact-form-wrap">
                <h2 class="contact-form-title">Send Us a Message</h2>
                <p class="contact-form-subtext">Have a question or need help? We'd love to hear from you. Fill out the
                    form below and we'll get back to you as soon as possible.</p>
                <?php echo do_shortcode('[fluentform id="4"]'); ?>
            </div>

        </div>
    </div>
</section>