// CatálogoZapatillas - Frontend JavaScript

document.addEventListener('DOMContentLoaded', function() {
    // Mobile menu toggle
    const menuToggle = document.getElementById('menu-toggle');
    const mobileMenu = document.getElementById('mobile-menu');

    if (menuToggle && mobileMenu) {
        menuToggle.addEventListener('click', function() {
            mobileMenu.classList.toggle('hidden');
        });
    }

    // Admin sidebar toggle
    const sidebarToggle = document.getElementById('sidebar-toggle');
    const sidebar = document.querySelector('aside');

    if (sidebarToggle && sidebar) {
        sidebarToggle.addEventListener('click', function() {
            sidebar.classList.toggle('hidden');
            sidebar.classList.toggle('fixed');
            sidebar.classList.toggle('inset-0');
            sidebar.classList.toggle('z-50');
        });
    }

    // Image gallery thumbnails
    document.querySelectorAll('.thumb-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const mainImage = document.getElementById('main-image');
            if (mainImage) {
                mainImage.src = this.dataset.src;
                document.querySelectorAll('.thumb-btn').forEach(b =>
                    b.classList.remove('border-indigo-500')
                );
                this.classList.add('border-indigo-500');
            }
        });
    });

    // Product toggle (admin)
    document.querySelectorAll('.toggle-product').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.dataset.id;
            fetch('/admin/productos/toggle/' + id, { method: 'POST' })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        this.textContent = data.is_active ? 'Activo' : 'Inactivo';
                        this.className = 'toggle-product px-2 py-1 text-xs font-medium rounded-full ' +
                            (data.is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500');
                    }
                })
                .catch(() => {
                    console.error('Error al cambiar estado del producto');
                });
        });
    });

    // Live search with debounce
    const searchInput = document.getElementById('search-input');
    let searchTimeout;

    if (searchInput) {
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                const params = new URLSearchParams(window.location.search);
                if (this.value) {
                    params.set('search', this.value);
                } else {
                    params.delete('search');
                }
                params.set('page', '1');
                window.location.search = params.toString();
            }, 500);
        });
    }

    // Sort select
    const sortSelect = document.getElementById('sort-select');
    if (sortSelect) {
        sortSelect.addEventListener('change', function() {
            const params = new URLSearchParams(window.location.search);
            params.set('sort', this.value);
            params.set('page', '1');
            window.location.search = params.toString();
        });
    }
});
