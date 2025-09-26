(function(){
    // Theme Toggle
    const themeToggle = document.getElementById('themeToggle');
    const body = document.body;
    const icon = themeToggle ? themeToggle.querySelector('i') : null;

    if (themeToggle) {
        const savedTheme = localStorage.getItem('skyworld-theme');
        const prefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;

        if (savedTheme === 'dark' || (!savedTheme && prefersDark)) {
            body.classList.add('dark-mode');
            if (icon) icon.className = 'fas fa-sun';
        }

        themeToggle.addEventListener('click', () => {
            body.classList.toggle('dark-mode');
            if (body.classList.contains('dark-mode')) {
                localStorage.setItem('skyworld-theme', 'dark');
                if (icon) icon.className = 'fas fa-sun';
            } else {
                localStorage.setItem('skyworld-theme', 'light');
                if (icon) icon.className = 'fas fa-moon';
            }
        });
    }

    // Filter Functionality
    const filterButtons = document.querySelectorAll('.filter-btn');
    const productCards = document.querySelectorAll('.product-card');

    filterButtons.forEach(button => {
        button.addEventListener('click', () => {
            // Update active button
            filterButtons.forEach(btn => btn.classList.remove('active'));
            button.classList.add('active');

            const filter = button.getAttribute('data-filter');

            productCards.forEach(card => {
                if (filter === '*') {
                    card.style.display = 'block';
                } else {
                    const className = filter.replace('.', '');
                    if (card.classList.contains(className)) {
                        card.style.display = 'block';
                    } else {
                        card.style.display = 'none';
                    }
                }
            });
        });
    });

    // Sort Functionality
    const sortSelect = document.getElementById('sort-by-select');
    const productGrid = document.getElementById('product-grid');

    if (sortSelect && productGrid) {
        sortSelect.addEventListener('change', () => {
            const sortBy = sortSelect.value;
            const cardsArray = Array.from(productCards);

            cardsArray.sort((a, b) => {
                if (sortBy === 'name') {
                    const nameA = a.getAttribute('data-name').toLowerCase();
                    const nameB = b.getAttribute('data-name').toLowerCase();
                    return nameA.localeCompare(nameB);
                } else if (sortBy === 'thc') {
                    const thcA = parseFloat(a.getAttribute('data-thc')) || 0;
                    const thcB = parseFloat(b.getAttribute('data-thc')) || 0;
                    return thcB - thcA; // Descending order (highest first)
                }
                return 0;
            });

            // Clear and re-append sorted cards
            productGrid.innerHTML = '';
            cardsArray.forEach(card => {
                productGrid.appendChild(card);
            });
        });
    }

    // Initialize with all products visible on load
    window.addEventListener('load', () => {
        productCards.forEach(card => {
            card.style.display = 'block';
        });
    });
})();
