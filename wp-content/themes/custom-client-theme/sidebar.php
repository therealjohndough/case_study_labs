<?php
/**
 * Sidebar Template
 *
 * @package CustomClientTheme
 */

// Don't show sidebar if no widgets are active
if (!is_active_sidebar('sidebar-1')) {
    return;
}
?>

<aside id="secondary" class="widget-area sidebar">
    <div class="sidebar-inner">
        <?php dynamic_sidebar('sidebar-1'); ?>
    </div>
</aside>

<style>
.sidebar {
    background: var(--card);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    padding: 2rem;
    margin-top: 2rem;
}

.sidebar .widget {
    margin-bottom: 2rem;
    padding-bottom: 2rem;
    border-bottom: 1px solid var(--border);
}

.sidebar .widget:last-child {
    border-bottom: none;
    margin-bottom: 0;
    padding-bottom: 0;
}

.sidebar .widget-title {
    color: var(--primary);
    font-size: 1.2rem;
    margin-bottom: 1rem;
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.sidebar .widget ul {
    list-style: none;
    padding: 0;
}

.sidebar .widget li {
    margin-bottom: 0.5rem;
    padding-left: 1rem;
    position: relative;
}

.sidebar .widget li::before {
    content: '▶';
    color: var(--primary);
    position: absolute;
    left: 0;
    font-size: 0.8rem;
    transform: rotate(0deg);
    transition: transform 0.3s ease;
}

.sidebar .widget li:hover::before {
    transform: rotate(90deg);
}

.sidebar .widget a {
    color: var(--foreground);
    text-decoration: none;
    transition: color 0.3s ease;
}

.sidebar .widget a:hover {
    color: var(--primary);
}

.sidebar .search-form {
    margin-bottom: 1rem;
}

@media (max-width: 1024px) {
    .sidebar {
        margin-top: 3rem;
    }
}
</style>