<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Page Not Found</title>
    <?php wp_head(); ?>
</head>
<body>

<div style="display: flex; flex-direction: column; align-items: center;justify-content: center; height: 100vh; padding: 80px 20px;">
    
    <img src="<?php echo esc_url( get_stylesheet_directory_uri() . '/assets/images/404.png' ); ?>" alt="404" style="max-width: 400px; width: 100%; margin: 0 auto 24px; display: block;">
    <h2>Page Not Found</h2>
    <p style="margin-bottom: 24px;">But no worries! Our team is looking everywhere while you wait safely.</p>
    <a href="<?php echo esc_url( home_url('/') ); ?>" class="btn-primary" >Back To Home</a>
</div>

<?php wp_footer(); ?>
</body>
</html>
