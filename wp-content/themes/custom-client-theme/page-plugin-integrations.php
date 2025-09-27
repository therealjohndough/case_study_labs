<?php
/**
 * Template Name: Plugin Integrations Demo
 * 
 * This template demonstrates how the required plugins (Brevo and Agile Store Locator) 
 * will integrate with the theme once they are installed and configured.
 *
 * @package CustomClientTheme
 */

get_header(); ?>

<main id="primary" class="site-main">
    <div class="container">
        <header class="page-header">
            <h1 class="page-title"><?php _e('Plugin Integrations', 'custom-client-theme'); ?></h1>
            <p class="page-description">
                <?php _e('This page demonstrates how the required plugins integrate with your theme.', 'custom-client-theme'); ?>
            </p>
        </header>

        <!-- Brevo Email Marketing Integration -->
        <section class="integration-section brevo-integration card">
            <div class="integration-header">
                <h2 class="integration-title"><?php _e('Brevo Email Marketing', 'custom-client-theme'); ?></h2>
                <div class="plugin-status">
                    <?php if (function_exists('brevo_form') || class_exists('Brevo_Form')) : ?>
                        <span class="status-active"><?php _e('Active', 'custom-client-theme'); ?></span>
                    <?php else : ?>
                        <span class="status-inactive"><?php _e('Not Installed', 'custom-client-theme'); ?></span>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="integration-content">
                <div class="integration-description">
                    <h3><?php _e('Email Marketing Features:', 'custom-client-theme'); ?></h3>
                    <ul>
                        <li><?php _e('Newsletter subscription forms', 'custom-client-theme'); ?></li>
                        <li><?php _e('Email campaign management', 'custom-client-theme'); ?></li>
                        <li><?php _e('Contact list management', 'custom-client-theme'); ?></li>
                        <li><?php _e('Automated email sequences', 'custom-client-theme'); ?></li>
                    </ul>
                </div>
                
                <div class="integration-demo">
                    <?php if (function_exists('brevo_form') || class_exists('Brevo_Form')) : ?>
                        <h4><?php _e('Live Integration:', 'custom-client-theme'); ?></h4>
                        <?php
                        // When Brevo is active, the actual form will appear here
                        echo '<div class="brevo-form-container">';
                        custom_theme_brevo_integration();
                        echo '</div>';
                        ?>
                    <?php else : ?>
                        <div class="integration-placeholder">
                            <h4><?php _e('Demo Preview:', 'custom-client-theme'); ?></h4>
                            <div class="demo-form">
                                <form class="newsletter-form">
                                    <div class="form-group">
                                        <label for="demo-email"><?php _e('Subscribe to our newsletter:', 'custom-client-theme'); ?></label>
                                        <div class="form-input-group">
                                            <input type="email" id="demo-email" placeholder="<?php esc_attr_e('Enter your email', 'custom-client-theme'); ?>" disabled>
                                            <button type="button" class="btn btn-primary" disabled>
                                                <?php _e('Subscribe', 'custom-client-theme'); ?>
                                            </button>
                                        </div>
                                    </div>
                                </form>
                                <p class="demo-note">
                                    <em><?php _e('This is a demo preview. Install and configure Brevo plugin to enable functionality.', 'custom-client-theme'); ?></em>
                                </p>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </section>

        <!-- Agile Store Locator Integration -->
        <section class="integration-section store-locator-integration card">
            <div class="integration-header">
                <h2 class="integration-title"><?php _e('Agile Store Locator', 'custom-client-theme'); ?></h2>
                <div class="plugin-status">
                    <?php if (function_exists('asl_store_locator') || class_exists('AgileStoreLocator')) : ?>
                        <span class="status-active"><?php _e('Active', 'custom-client-theme'); ?></span>
                    <?php else : ?>
                        <span class="status-inactive"><?php _e('Not Installed', 'custom-client-theme'); ?></span>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="integration-content">
                <div class="integration-description">
                    <h3><?php _e('Store Locator Features:', 'custom-client-theme'); ?></h3>
                    <ul>
                        <li><?php _e('Interactive Google Maps integration', 'custom-client-theme'); ?></li>
                        <li><?php _e('Store/location search functionality', 'custom-client-theme'); ?></li>
                        <li><?php _e('Detailed location information', 'custom-client-theme'); ?></li>
                        <li><?php _e('Mobile-responsive design', 'custom-client-theme'); ?></li>
                        <li><?php _e('Custom styling to match theme', 'custom-client-theme'); ?></li>
                    </ul>
                </div>
                
                <div class="integration-demo">
                    <?php if (function_exists('asl_store_locator') || class_exists('AgileStoreLocator')) : ?>
                        <h4><?php _e('Live Store Locator:', 'custom-client-theme'); ?></h4>
                        <?php
                        // When Agile Store Locator is active, the actual locator will appear here
                        echo '<div class="store-locator-container">';
                        custom_theme_store_locator_integration();
                        echo '</div>';
                        ?>
                    <?php else : ?>
                        <div class="integration-placeholder">
                            <h4><?php _e('Demo Preview:', 'custom-client-theme'); ?></h4>
                            <div class="demo-locator">
                                <div class="locator-search">
                                    <input type="text" placeholder="<?php esc_attr_e('Enter location or zip code', 'custom-client-theme'); ?>" disabled>
                                    <button type="button" class="btn btn-primary" disabled>
                                        <?php _e('Find Stores', 'custom-client-theme'); ?>
                                    </button>
                                </div>
                                <div class="demo-map">
                                    <div class="map-placeholder">
                                        <span class="map-icon">📍</span>
                                        <p><?php _e('Interactive map will appear here', 'custom-client-theme'); ?></p>
                                    </div>
                                </div>
                                <p class="demo-note">
                                    <em><?php _e('This is a demo preview. Install and configure Agile Store Locator plugin to enable functionality.', 'custom-client-theme'); ?></em>
                                </p>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </section>

        <!-- Installation Instructions -->
        <section class="installation-instructions card">
            <h2><?php _e('Installation Instructions', 'custom-client-theme'); ?></h2>
            
            <div class="instruction-tabs">
                <button class="tab-button active" data-tab="brevo"><?php _e('Brevo Setup', 'custom-client-theme'); ?></button>
                <button class="tab-button" data-tab="store-locator"><?php _e('Store Locator Setup', 'custom-client-theme'); ?></button>
            </div>
            
            <div class="tab-content active" id="brevo-instructions">
                <h3><?php _e('Setting up Brevo Email Marketing:', 'custom-client-theme'); ?></h3>
                <ol>
                    <li><?php _e('Install the Brevo plugin from the WordPress plugin directory', 'custom-client-theme'); ?></li>
                    <li><?php _e('Create a Brevo account at brevo.com if you don\'t have one', 'custom-client-theme'); ?></li>
                    <li><?php _e('Get your API key from your Brevo dashboard', 'custom-client-theme'); ?></li>
                    <li><?php _e('Configure the plugin with your API credentials', 'custom-client-theme'); ?></li>
                    <li><?php _e('Create and customize your subscription forms', 'custom-client-theme'); ?></li>
                    <li><?php _e('The theme will automatically style the forms to match the design', 'custom-client-theme'); ?></li>
                </ol>
            </div>
            
            <div class="tab-content" id="store-locator-instructions">
                <h3><?php _e('Setting up Agile Store Locator:', 'custom-client-theme'); ?></h3>
                <ol>
                    <li><?php _e('Purchase and install the Agile Store Locator plugin', 'custom-client-theme'); ?></li>
                    <li><?php _e('Get a Google Maps API key from Google Cloud Console', 'custom-client-theme'); ?></li>
                    <li><?php _e('Configure the plugin with your Google Maps API key', 'custom-client-theme'); ?></li>
                    <li><?php _e('Add your store locations through the plugin admin', 'custom-client-theme'); ?></li>
                    <li><?php _e('Customize the map styling to match the cyberpunk theme', 'custom-client-theme'); ?></li>
                    <li><?php _e('Use shortcodes to display the store locator on any page', 'custom-client-theme'); ?></li>
                </ol>
            </div>
        </section>

        <?php if (current_user_can('manage_options')) : ?>
        <section class="admin-links card">
            <h2><?php _e('Quick Admin Links', 'custom-client-theme'); ?></h2>
            <div class="admin-buttons">
                <a href="<?php echo admin_url('plugin-install.php?tab=search&s=brevo'); ?>" class="btn btn-primary">
                    <?php _e('Install Brevo Plugin', 'custom-client-theme'); ?>
                </a>
                <a href="<?php echo admin_url('plugin-install.php?tab=search&s=agile+store+locator'); ?>" class="btn btn-primary">
                    <?php _e('Install Store Locator Plugin', 'custom-client-theme'); ?>
                </a>
                <a href="<?php echo admin_url('admin.php?page=theme-settings'); ?>" class="btn btn-secondary">
                    <?php _e('Theme Settings', 'custom-client-theme'); ?>
                </a>
            </div>
        </section>
        <?php endif; ?>
    </div>
</main>

<style>
/* Plugin Integration Page Styles */
.integration-section {
    margin-bottom: 3rem;
    padding: 2rem;
}

.integration-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
    padding-bottom: 1rem;
    border-bottom: 1px solid var(--border);
}

.integration-title {
    color: var(--primary);
    margin: 0;
}

.plugin-status {
    font-size: 0.9rem;
    font-weight: 500;
}

.status-active {
    color: #00ff00;
    padding: 0.25rem 0.5rem;
    background: rgba(0, 255, 0, 0.1);
    border: 1px solid #00ff00;
    border-radius: var(--radius);
    text-transform: uppercase;
    font-size: 0.8rem;
}

.status-inactive {
    color: #ff6b35;
    padding: 0.25rem 0.5rem;
    background: rgba(255, 107, 53, 0.1);
    border: 1px solid #ff6b35;
    border-radius: var(--radius);
    text-transform: uppercase;
    font-size: 0.8rem;
}

.integration-content {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 2rem;
    align-items: start;
}

.integration-description h3 {
    color: var(--accent);
    margin-bottom: 1rem;
}

.integration-description ul {
    padding-left: 1.5rem;
}

.integration-description li {
    margin-bottom: 0.5rem;
    color: var(--foreground);
}

.integration-placeholder,
.brevo-form-container,
.store-locator-container {
    background: var(--muted);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    padding: 1.5rem;
}

.demo-form,
.demo-locator {
    margin-top: 1rem;
}

.form-input-group {
    display: flex;
    gap: 0.5rem;
}

.form-input-group input {
    flex: 1;
    padding: 0.75rem;
    background: var(--input-background);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    color: var(--foreground);
}

.form-input-group input:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

.locator-search {
    display: flex;
    gap: 0.5rem;
    margin-bottom: 1rem;
}

.locator-search input {
    flex: 1;
    padding: 0.75rem;
    background: var(--input-background);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    color: var(--foreground);
}

.demo-map {
    margin-top: 1rem;
}

.map-placeholder {
    height: 200px;
    background: var(--secondary);
    border: 2px dashed var(--border);
    border-radius: var(--radius);
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    color: var(--muted-foreground);
}

.map-icon {
    font-size: 2rem;
    margin-bottom: 0.5rem;
}

.demo-note {
    margin-top: 1rem;
    font-size: 0.9rem;
    color: var(--muted-foreground);
    text-align: center;
}

.installation-instructions {
    padding: 2rem;
}

.instruction-tabs {
    display: flex;
    gap: 1rem;
    margin-bottom: 2rem;
    border-bottom: 1px solid var(--border);
}

.tab-button {
    padding: 0.75rem 1.5rem;
    background: transparent;
    border: none;
    color: var(--foreground);
    font-family: 'Orbitron', sans-serif;
    cursor: pointer;
    border-bottom: 2px solid transparent;
    transition: all 0.3s ease;
}

.tab-button:hover,
.tab-button.active {
    color: var(--primary);
    border-bottom-color: var(--primary);
}

.tab-content {
    display: none;
}

.tab-content.active {
    display: block;
}

.tab-content ol {
    padding-left: 1.5rem;
}

.tab-content li {
    margin-bottom: 1rem;
    line-height: 1.6;
}

.admin-links {
    padding: 2rem;
    text-align: center;
}

.admin-buttons {
    display: flex;
    gap: 1rem;
    justify-content: center;
    flex-wrap: wrap;
    margin-top: 1rem;
}

@media (max-width: 768px) {
    .integration-content {
        grid-template-columns: 1fr;
        gap: 1rem;
    }
    
    .integration-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.5rem;
    }
    
    .form-input-group,
    .locator-search {
        flex-direction: column;
    }
    
    .instruction-tabs {
        flex-direction: column;
        gap: 0;
    }
    
    .admin-buttons {
        flex-direction: column;
        align-items: center;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Tab functionality
    const tabButtons = document.querySelectorAll('.tab-button');
    const tabContents = document.querySelectorAll('.tab-content');
    
    tabButtons.forEach(button => {
        button.addEventListener('click', function() {
            const tabId = this.dataset.tab;
            
            // Remove active class from all buttons and contents
            tabButtons.forEach(btn => btn.classList.remove('active'));
            tabContents.forEach(content => content.classList.remove('active'));
            
            // Add active class to clicked button and corresponding content
            this.classList.add('active');
            document.getElementById(tabId + '-instructions').classList.add('active');
        });
    });
});
</script>

<?php get_footer(); ?>