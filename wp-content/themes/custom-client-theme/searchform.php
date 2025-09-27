<?php
/**
 * Search Form Template
 *
 * @package CustomClientTheme
 */
?>

<form role="search" method="get" class="search-form" action="<?php echo esc_url(home_url('/')); ?>">
    <div class="search-form-inner">
        <label for="s" class="screen-reader-text"><?php _e('Search for:', 'custom-client-theme'); ?></label>
        <input type="search" 
               id="s" 
               name="s" 
               class="search-field" 
               placeholder="<?php esc_attr_e('Search...', 'custom-client-theme'); ?>" 
               value="<?php echo get_search_query(); ?>" 
               required>
        <button type="submit" class="search-submit btn btn-primary">
            <span class="search-icon">🔍</span>
            <span class="screen-reader-text"><?php _e('Search', 'custom-client-theme'); ?></span>
        </button>
    </div>
</form>

<style>
.search-form {
    width: 100%;
    max-width: 400px;
}

.search-form-inner {
    position: relative;
    display: flex;
    border: 1px solid var(--border);
    border-radius: var(--radius);
    overflow: hidden;
    transition: border-color 0.3s ease;
}

.search-form:focus-within .search-form-inner {
    border-color: var(--primary);
    box-shadow: 0 0 0 2px rgba(241, 91, 39, 0.2);
}

.search-field {
    flex: 1;
    padding: 0.75rem 1rem;
    border: none;
    background: var(--input-background);
    color: var(--foreground);
    font-family: 'Orbitron', sans-serif;
    font-size: 1rem;
    outline: none;
}

.search-field::placeholder {
    color: var(--muted-foreground);
    opacity: 0.7;
}

.search-submit {
    border: none;
    border-left: 1px solid var(--border);
    border-radius: 0;
    padding: 0.75rem 1rem;
    background: var(--primary);
    color: var(--primary-foreground);
    cursor: pointer;
    transition: background-color 0.3s ease;
    min-width: 50px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.search-submit:hover {
    background: var(--accent);
}

.search-icon {
    font-size: 1rem;
}

.screen-reader-text {
    position: absolute !important;
    clip: rect(1px, 1px, 1px, 1px);
    width: 1px;
    height: 1px;
    overflow: hidden;
}

@media (max-width: 768px) {
    .search-form {
        max-width: 100%;
    }
    
    .search-field {
        font-size: 16px; /* Prevent zoom on iOS */
    }
}
</style>