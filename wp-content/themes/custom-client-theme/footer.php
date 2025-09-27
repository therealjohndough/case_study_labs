<?php
/**
 * Footer Template
 *
 * @package CustomClientTheme
 */
?>

    <footer id="colophon" class="site-footer scanlines">
        <div class="container">
            <div class="footer-content grid">
                <div class="footer-section footer-info">
                    <h3 class="footer-title"><?php bloginfo('name'); ?></h3>
                    <?php
                    $tagline = get_theme_option('site_tagline', get_bloginfo('description'));
                    if ($tagline) :
                        ?>
                        <p class="footer-tagline"><?php echo esc_html($tagline); ?></p>
                        <?php
                    endif;
                    ?>
                </div>

                <div class="footer-section footer-contact">
                    <h3 class="footer-title"><?php _e('Contact', 'custom-client-theme'); ?></h3>
                    <?php
                    $contact_email = get_theme_option('contact_email');
                    $contact_phone = get_theme_option('contact_phone');
                    
                    if ($contact_email) :
                        ?>
                        <p class="contact-item">
                            <strong><?php _e('Email:', 'custom-client-theme'); ?></strong>
                            <a href="mailto:<?php echo esc_attr($contact_email); ?>">
                                <?php echo esc_html($contact_email); ?>
                            </a>
                        </p>
                        <?php
                    endif;
                    
                    if ($contact_phone) :
                        ?>
                        <p class="contact-item">
                            <strong><?php _e('Phone:', 'custom-client-theme'); ?></strong>
                            <a href="tel:<?php echo esc_attr($contact_phone); ?>">
                                <?php echo esc_html($contact_phone); ?>
                            </a>
                        </p>
                        <?php
                    endif;
                    ?>
                </div>

                <div class="footer-section footer-navigation">
                    <h3 class="footer-title"><?php _e('Quick Links', 'custom-client-theme'); ?></h3>
                    <?php
                    wp_nav_menu(array(
                        'theme_location' => 'footer',
                        'menu_id'        => 'footer-menu',
                        'container'      => false,
                        'depth'          => 1,
                        'fallback_cb'    => false,
                    ));
                    ?>
                </div>

                <div class="footer-section footer-social">
                    <h3 class="footer-title"><?php _e('Follow Us', 'custom-client-theme'); ?></h3>
                    <?php display_social_links(); ?>
                </div>
            </div>

            <?php if (is_active_sidebar('footer-widgets')) : ?>
                <div class="footer-widgets">
                    <?php dynamic_sidebar('footer-widgets'); ?>
                </div>
            <?php endif; ?>

            <div class="site-info">
                <div class="copyright">
                    <p>&copy; <?php echo date('Y'); ?> <?php bloginfo('name'); ?>. 
                       <?php _e('All rights reserved.', 'custom-client-theme'); ?>
                    </p>
                </div>
                
                <?php if (current_user_can('manage_options')) : ?>
                    <div class="admin-links">
                        <a href="<?php echo admin_url('admin.php?page=theme-settings'); ?>">
                            <?php _e('Theme Settings', 'custom-client-theme'); ?>
                        </a>
                        <a href="<?php echo admin_url('customize.php'); ?>">
                            <?php _e('Customize', 'custom-client-theme'); ?>
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </footer>

    <?php
    // Plugin Integration Areas
    
    // Brevo Integration
    if (function_exists('brevo_form')) {
        echo '<div id="brevo-integration">';
        custom_theme_brevo_integration();
        echo '</div>';
    }
    
    // Agile Store Locator Integration
    if (function_exists('asl_store_locator')) {
        echo '<div id="store-locator-integration">';
        custom_theme_store_locator_integration();
        echo '</div>';
    }
    ?>

</div><!-- #page -->

<?php wp_footer(); ?>

<script>
// Basic JavaScript for theme functionality
document.addEventListener('DOMContentLoaded', function() {
    // Mobile menu toggle
    const menuToggle = document.querySelector('.menu-toggle');
    const navigation = document.querySelector('.main-navigation');
    
    if (menuToggle && navigation) {
        menuToggle.addEventListener('click', function() {
            const expanded = menuToggle.getAttribute('aria-expanded') === 'true';
            menuToggle.setAttribute('aria-expanded', !expanded);
            navigation.classList.toggle('toggled');
        });
    }
    
    // Add cyberpunk effects on scroll
    window.addEventListener('scroll', function() {
        const scrolled = window.pageYOffset;
        const parallax = document.querySelector('.pixel-pattern');
        const speed = scrolled * 0.1;
        
        if (parallax) {
            parallax.style.transform = `translateY(${speed}px)`;
        }
    });
    
    // Glitch effect on hover for cards
    const cards = document.querySelectorAll('.card');
    cards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.animation = 'glitch 0.3s ease-in-out';
        });
        
        card.addEventListener('animationend', function() {
            this.style.animation = '';
        });
    });
});
</script>

<style>
/* Additional CSS for footer and mobile */
.footer-content {
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 2rem;
    margin-bottom: 2rem;
}

.footer-title {
    color: var(--primary);
    margin-bottom: 1rem;
    font-size: 1.2rem;
}

.social-links {
    display: flex;
    gap: 1rem;
}

.social-link {
    display: inline-block;
    width: 40px;
    height: 40px;
    border: 1px solid var(--border);
    border-radius: var(--radius);
    text-align: center;
    line-height: 38px;
    transition: all 0.3s ease;
}

.social-link:hover {
    border-color: var(--primary);
    background: var(--primary);
    color: var(--primary-foreground);
}

.site-info {
    border-top: 1px solid var(--border);
    padding-top: 2rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 1rem;
}

.admin-links {
    display: flex;
    gap: 1rem;
}

.admin-links a {
    font-size: 0.9rem;
    opacity: 0.7;
}

/* Mobile menu */
.menu-toggle {
    display: none;
    background: none;
    border: 1px solid var(--border);
    border-radius: var(--radius);
    padding: 0.5rem;
    cursor: pointer;
}

.hamburger {
    display: block;
    width: 20px;
    height: 15px;
    position: relative;
}

.hamburger span {
    display: block;
    height: 2px;
    width: 100%;
    background: var(--foreground);
    margin-bottom: 3px;
    transition: all 0.3s ease;
}

.hamburger span:last-child {
    margin-bottom: 0;
}

/* Glitch animation */
@keyframes glitch {
    0% { transform: translate(0); }
    20% { transform: translate(-2px, 2px); }
    40% { transform: translate(-2px, -2px); }
    60% { transform: translate(2px, 2px); }
    80% { transform: translate(2px, -2px); }
    100% { transform: translate(0); }
}

@media (max-width: 768px) {
    .menu-toggle {
        display: block;
    }
    
    .main-navigation ul {
        display: none;
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        background: var(--background);
        border: 1px solid var(--border);
        border-top: none;
        flex-direction: column;
        z-index: 999;
    }
    
    .main-navigation.toggled ul {
        display: flex;
    }
    
    .main-navigation ul li {
        border-bottom: 1px solid var(--border);
    }
    
    .main-navigation ul li:last-child {
        border-bottom: none;
    }
    
    .main-navigation ul li a {
        display: block;
        padding: 1rem;
    }
    
    .site-info {
        flex-direction: column;
        text-align: center;
    }
}
</style>

</body>
</html>