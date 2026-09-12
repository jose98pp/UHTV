@extends('layouts.admin')

@section('title', 'Gestión de Noticias - UHTV Admin')
@section('page-title', 'Gestión de Noticias')

@push('styles')
<link href="{{ asset('css/news-cards.css') }}" rel="stylesheet">
<link href="{{ asset('css/news-views.css') }}" rel="stylesheet">
<link href="{{ asset('css/news-statistics.css') }}" rel="stylesheet">
<link href="{{ asset('css/enhanced-filters.css') }}" rel="stylesheet">
<link href="{{ asset('css/enhanced-pagination.css') }}" rel="stylesheet">
<link href="{{ asset('css/performance-optimizations.css') }}" rel="stylesheet">
@endpush

@section('content')
<div class="space-y-6">
    <!-- Header Actions -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Noticias</h1>
            <p class="text-gray-600">Administra todas las noticias de tu sitio web</p>
        </div>
        <div class="mt-4 sm:mt-0 flex space-x-3">
            <a href="{{ route('admin.noticias.create') }}" 
               class="inline-flex items-center px-4 py-2 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition">
                <i class="fas fa-plus mr-2"></i>
                Nueva Noticia
            </a>
        </div>
    </div>

    <!-- Statistics Bar -->
    <div class="stats-bar">
        <div class="stats-grid">
            <div class="stat-item total">
                <div class="stat-icon">
                    <i class="fas fa-newspaper"></i>
                </div>
                <div class="stat-number" data-count="{{ $statistics['total'] }}">{{ $statistics['total'] }}</div>
                <div class="stat-label">Total Noticias</div>
            </div>
            
            <div class="stat-item published">
                <div class="stat-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-number" data-count="{{ $statistics['published'] }}">{{ $statistics['published'] }}</div>
                <div class="stat-label">Publicadas</div>
                <div class="stat-percentage">{{ $statistics['published_percentage'] }}% del total</div>
            </div>
            
            <div class="stat-item drafts">
                <div class="stat-icon">
                    <i class="fas fa-edit"></i>
                </div>
                <div class="stat-number" data-count="{{ $statistics['drafts'] }}">{{ $statistics['drafts'] }}</div>
                <div class="stat-label">Borradores</div>
            </div>
            
            <div class="stat-item categories">
                <div class="stat-icon">
                    <i class="fas fa-tags"></i>
                </div>
                <div class="stat-number" data-count="{{ $statistics['categories'] }}">{{ $statistics['categories'] }}</div>
                <div class="stat-label">Categorías</div>
            </div>
            
            <div class="stat-item recent">
                <div class="stat-icon">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stat-number" data-count="{{ $statistics['recent'] }}">{{ $statistics['recent'] }}</div>
                <div class="stat-label">Últimos 7 días</div>
            </div>
        </div>
    </div>

    <!-- Enhanced Filters -->
    <div class="enhanced-filters" id="filters-container">
        <div class="filters-header">
            <h3 class="filters-title">
                <i class="fas fa-filter"></i>
                Filtros y Búsqueda
            </h3>
            <div class="view-toggle-wrapper">
                <div class="view-toggle">
                    <button type="button" 
                            id="grid-view-btn" 
                            class="view-toggle-btn active"
                            aria-pressed="true"
                            title="Vista de cuadrícula (Ctrl+G)">
                        <i class="fas fa-th"></i>
                        Cuadrícula
                    </button>
                    <button type="button" 
                            id="list-view-btn" 
                            class="view-toggle-btn"
                            aria-pressed="false"
                            title="Vista de lista (Ctrl+L)">
                        <i class="fas fa-list"></i>
                        Lista
                    </button>
                </div>
            </div>
        </div>

        <form method="GET" class="filters-form" id="filters-form">
            <!-- Search Input -->
            <div class="filter-group">
                <label class="filter-label" for="search-input">Buscar noticias</label>
                <div class="search-input-wrapper {{ request('search') ? 'has-value' : '' }}">
                    <i class="fas fa-search search-icon"></i>
                    <input type="text" 
                           id="search-input"
                           name="search" 
                           value="{{ request('search') }}"
                           placeholder="Buscar por título o contenido..." 
                           class="filter-input">
                    <button type="button" class="clear-search" onclick="clearSearch()">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>

            <!-- Category Filter -->
            <div class="filter-group">
                <label class="filter-label" for="category-select">Categoría</label>
                <select name="category" id="category-select" class="filter-input filter-select">
                    <option value="">Todas las categorías</option>
                    @foreach($categorias ?? [] as $categoria)
                        <option value="{{ $categoria->id }}" {{ request('category') == $categoria->id ? 'selected' : '' }}>
                            {{ $categoria->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Status Filter -->
            <div class="filter-group">
                <label class="filter-label" for="status-select">Estado</label>
                <select name="status" id="status-select" class="filter-input filter-select">
                    <option value="">Todos los estados</option>
                    <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Publicadas</option>
                    <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Borradores</option>
                </select>
            </div>

            <!-- Sort Filter -->
            <div class="filter-group">
                <label class="filter-label" for="sort-select">Ordenar por</label>
                <select name="sort" id="sort-select" class="filter-input filter-select">
                    <option value="latest" {{ request('sort') === 'latest' ? 'selected' : '' }}>Más recientes</option>
                    <option value="oldest" {{ request('sort') === 'oldest' ? 'selected' : '' }}>Más antiguas</option>
                    <option value="views_desc" {{ request('sort') === 'views_desc' ? 'selected' : '' }}>Más vistas (Popularidad)</option>
                    <option value="views_asc" {{ request('sort') === 'views_asc' ? 'selected' : '' }}>Menos vistas</option>
                    <option value="title_asc" {{ request('sort') === 'title_asc' ? 'selected' : '' }}>Título (A - Z)</option>
                </select>
            </div>

            <!-- Filter Buttons -->
            <div class="filter-buttons">
                <button type="submit" class="filter-btn primary">
                    <i class="fas fa-search"></i>
                    Filtrar
                </button>
            </div>

            <!-- Clear Filters Button -->
            <div class="filter-buttons">
                <button type="button" 
                        class="filter-btn clear-filters-btn {{ (request('search') || request('category') || request('status')) ? 'has-filters' : '' }}" 
                        onclick="clearAllFilters()"
                        title="Limpiar todos los filtros">
                    <i class="fas fa-times-circle"></i>
                    Limpiar
                    @if(request('search') || request('category') || request('status'))
                        <span class="filter-count">
                            {{ collect([request('search'), request('category'), request('status')])->filter()->count() }}
                        </span>
                    @endif
                </button>
            </div>
        </form>

        <!-- Active Filters Display -->
        @if(request('search') || request('category') || request('status'))
            <div class="active-filters" id="active-filters">
                <div class="active-filters-title">
                    <i class="fas fa-filter"></i>
                    Filtros activos
                    <span class="filter-count">
                        {{ collect([request('search'), request('category'), request('status')])->filter()->count() }}
                    </span>
                </div>
                <div class="filter-tags">
                    @if(request('search'))
                        <span class="filter-tag">
                            <i class="fas fa-search mr-1"></i>
                            "{{ Str::limit(request('search'), 20) }}"
                            <button type="button" class="remove-tag" onclick="removeFilter('search')" title="Quitar filtro de búsqueda">
                                <i class="fas fa-times"></i>
                            </button>
                        </span>
                    @endif
                    
                    @if(request('category'))
                        @php
                            $selectedCategory = $categorias->find(request('category'));
                        @endphp
                        <span class="filter-tag">
                            <i class="fas fa-tag mr-1"></i>
                            {{ $selectedCategory->name ?? 'Desconocida' }}
                            <button type="button" class="remove-tag" onclick="removeFilter('category')" title="Quitar filtro de categoría">
                                <i class="fas fa-times"></i>
                            </button>
                        </span>
                    @endif
                    
                    @if(request('status') !== null && request('status') !== '')
                        <span class="filter-tag">
                            <i class="fas fa-{{ request('status') === '1' ? 'check-circle' : 'edit' }} mr-1"></i>
                            {{ request('status') === '1' ? 'Publicadas' : 'Borradores' }}
                            <button type="button" class="remove-tag" onclick="removeFilter('status')" title="Quitar filtro de estado">
                                <i class="fas fa-times"></i>
                            </button>
                        </span>
                    @endif
                    
                    <!-- Clear All Filters Button -->
                    <button type="button" class="clear-all-filters" onclick="clearAllFilters()" title="Limpiar todos los filtros">
                        <i class="fas fa-times-circle"></i>
                        Limpiar todo
                    </button>
                </div>
            </div>
        @endif
    </div>

    <!-- Bulk Actions Toolbar (Sticky / Floating when items are selected) -->
    <div id="bulk-actions-toolbar" class="bg-indigo-900 text-white p-3 rounded-xl shadow-lg flex flex-wrap items-center justify-between gap-3 transition-all duration-300 mb-4" style="display: none;">
        <div class="flex items-center gap-3">
            <input type="checkbox" id="select-all-checkbox" class="form-check-input w-5 h-5 rounded cursor-pointer text-blue-500" onchange="toggleSelectAll(this)">
            <label for="select-all-checkbox" class="font-bold text-sm cursor-pointer mb-0">Seleccionar todas</label>
            <span class="bg-white/20 px-2.5 py-0.5 rounded-full text-xs font-semibold" id="selected-count-badge">0 seleccionadas</span>
        </div>
        <div class="flex items-center gap-2">
            <button type="button" class="btn btn-sm btn-success rounded-lg font-semibold" onclick="executeBulkAction('publish')">
                <i class="fas fa-check-circle me-1"></i> Publicar
            </button>
            <button type="button" class="btn btn-sm btn-warning text-dark rounded-lg font-semibold" onclick="executeBulkAction('unpublish')">
                <i class="fas fa-pause-circle me-1"></i> Pasar a Borrador
            </button>
            <button type="button" class="btn btn-sm btn-danger rounded-lg font-semibold" onclick="executeBulkAction('delete')">
                <i class="fas fa-trash-alt me-1"></i> Eliminar
            </button>
            <button type="button" class="btn btn-sm btn-outline-light rounded-lg" onclick="clearSelection()" title="Cancelar selección">
                <i class="fas fa-times"></i>
            </button>
        </div>
    </div>

    <!-- News Cards -->
    <div class="news-container grid-view" id="news-container">
        @include('admin.noticias.partials.news-cards', ['news' => $news])
        
        <!-- Pagination Container -->
        <div id="pagination-container">
            @include('admin.noticias.partials.pagination', ['news' => $news])
        </div>
    </div>
</div>

<style>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.line-clamp-3 {
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

/* News Cards Styles */
.news-card {
    transform-style: preserve-3d;
    backface-visibility: hidden;
}

.news-card:hover {
    transform: translateY(-8px) rotateX(5deg);
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
}

.news-card-image {
    position: relative;
    overflow: hidden;
}

.news-card-image img {
    transition: transform 0.3s ease;
}

.news-card:hover .news-card-image img {
    transform: scale(1.05);
}

.actions-dropdown {
    position: relative;
}

.actions-menu {
    min-width: 12rem;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
    border: 1px solid #e5e7eb;
    animation: fadeInDown 0.2s ease-out;
}

@keyframes fadeInDown {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Card entrance animations */
.news-card {
    animation: slideInUp 0.6s ease-out;
    animation-fill-mode: both;
}

.news-card:nth-child(1) { animation-delay: 0.1s; }
.news-card:nth-child(2) { animation-delay: 0.2s; }
.news-card:nth-child(3) { animation-delay: 0.3s; }
.news-card:nth-child(4) { animation-delay: 0.4s; }
.news-card:nth-child(5) { animation-delay: 0.5s; }
.news-card:nth-child(6) { animation-delay: 0.6s; }
.news-card:nth-child(7) { animation-delay: 0.7s; }
.news-card:nth-child(8) { animation-delay: 0.8s; }

@keyframes slideInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .news-cards-grid {
        grid-template-columns: 1fr;
    }
    
    .news-card:hover {
        transform: translateY(-4px);
    }
}

@media (min-width: 769px) and (max-width: 1024px) {
    .news-cards-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}
</style>

@push('scripts')
<script src="{{ asset('js/simple-pagination.js') }}"></script>
<script src="{{ asset('js/news-views.js') }}"></script>
<script src="{{ asset('js/dynamic-filters.js') }}"></script>
<script src="{{ asset('js/performance-optimization.js') }}"></script>
<script>
// Statistics counter animation
function animateCounters() {
    const counters = document.querySelectorAll('.stat-number[data-count]');
    
    counters.forEach(counter => {
        const target = parseInt(counter.getAttribute('data-count'));
        const duration = 1500; // 1.5 seconds
        const increment = target / (duration / 16); // 60fps
        let current = 0;
        
        const timer = setInterval(() => {
            current += increment;
            if (current >= target) {
                counter.textContent = target;
                clearInterval(timer);
            } else {
                counter.textContent = Math.floor(current);
            }
        }, 16);
    });
}

// Initialize statistics animations when page loads
document.addEventListener('DOMContentLoaded', function() {
    // Delay animation slightly for better visual effect
    setTimeout(animateCounters, 300);
    
    // Initialize enhanced filters
    initializeEnhancedFilters();
});

// Enhanced Filters Functionality
function initializeEnhancedFilters() {
    const searchInput = document.getElementById('search-input');
    const searchWrapper = searchInput.parentElement;
    
    // Real-time search input handling
    searchInput.addEventListener('input', function() {
        if (this.value.length > 0) {
            searchWrapper.classList.add('has-value');
        } else {
            searchWrapper.classList.remove('has-value');
        }
    });
    
    // Auto-submit on select change (optional)
    const selects = document.querySelectorAll('#category-select, #status-select');
    selects.forEach(select => {
        select.addEventListener('change', function() {
            // Optional: Auto-submit form on select change
            // document.getElementById('filters-form').submit();
        });
    });
}

// Clear search function
function clearSearch() {
    const searchInput = document.getElementById('search-input');
    const searchWrapper = searchInput.parentElement;
    
    searchInput.value = '';
    searchWrapper.classList.remove('has-value');
    searchInput.focus();
}

// Clear all filters function
function clearAllFilters() {
    const form = document.getElementById('filters-form');
    const inputs = form.querySelectorAll('input, select');
    
    inputs.forEach(input => {
        if (input.type === 'text') {
            input.value = '';
        } else if (input.tagName === 'SELECT') {
            input.selectedIndex = 0;
        }
    });
    
    // Remove has-value class from search wrapper
    const searchWrapper = document.querySelector('.search-input-wrapper');
    searchWrapper.classList.remove('has-value');
    
    // Submit form to clear filters
    form.submit();
}

// Remove individual filter function
function removeFilter(filterName) {
    const form = document.getElementById('filters-form');
    const input = form.querySelector(`[name="${filterName}"]`);
    
    if (input) {
        if (input.type === 'text') {
            input.value = '';
        } else if (input.tagName === 'SELECT') {
            input.selectedIndex = 0;
        }
        
        // Update search wrapper class if needed
        if (filterName === 'search') {
            const searchWrapper = document.querySelector('.search-input-wrapper');
            searchWrapper.classList.remove('has-value');
        }
        
        // Submit form
        form.submit();
    }
}

// Keyboard shortcuts for filters
document.addEventListener('keydown', function(event) {
    // Ctrl/Cmd + F to focus search
    if ((event.ctrlKey || event.metaKey) && event.key === 'f') {
        event.preventDefault();
        document.getElementById('search-input').focus();
    }
    
    // Escape to clear search when focused
    if (event.key === 'Escape' && document.activeElement === document.getElementById('search-input')) {
        clearSearch();
    }
});
</script>
<script>
// Enhanced dropdown functionality with animations
function toggleDropdown(noticiaId) {
    // Close all other dropdowns with animation
    document.querySelectorAll('.actions-menu').forEach(menu => {
        if (menu.id !== `dropdown-${noticiaId}`) {
            menu.classList.add('hidden');
        }
    });
    
    // Toggle current dropdown with enhanced animation
    const dropdown = document.getElementById(`dropdown-${noticiaId}`);
    dropdown.classList.toggle('hidden');
    
    // Add ripple effect to trigger button
    const trigger = dropdown.previousElementSibling;
    createRipple(trigger);
}

// Create ripple effect for button interactions
function createRipple(element) {
    const ripple = document.createElement('span');
    const rect = element.getBoundingClientRect();
    const size = Math.max(rect.width, rect.height);
    
    ripple.style.width = ripple.style.height = size + 'px';
    ripple.style.left = '50%';
    ripple.style.top = '50%';
    ripple.style.transform = 'translate(-50%, -50%)';
    ripple.classList.add('ripple');
    
    element.style.position = 'relative';
    element.style.overflow = 'hidden';
    element.appendChild(ripple);
    
    setTimeout(() => {
        ripple.remove();
    }, 600);
}

// Enhanced card interactions
document.addEventListener('DOMContentLoaded', function() {
    // Add intersection observer for entrance animations
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.animationPlayState = 'running';
            }
        });
    }, observerOptions);
    
    // Observe all news cards
    document.querySelectorAll('.news-card').forEach(card => {
        observer.observe(card);
        card.style.animationPlayState = 'paused';
    });
    
    // Add hover sound effect (optional)
    document.querySelectorAll('.news-card').forEach(card => {
        card.addEventListener('mouseenter', function() {
            // Add subtle scale animation to image
            const img = this.querySelector('.news-card-image img');
            if (img) {
                img.style.transform = 'scale(1.08) rotate(1deg)';
            }
        });
        
        card.addEventListener('mouseleave', function() {
            const img = this.querySelector('.news-card-image img');
            if (img) {
                img.style.transform = 'scale(1) rotate(0deg)';
            }
        });
    });
    
    // Enhanced keyboard navigation
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            document.querySelectorAll('.actions-menu').forEach(menu => {
                menu.classList.add('hidden');
            });
        }
        
        // Arrow key navigation for cards
        if (event.key === 'ArrowRight' || event.key === 'ArrowLeft') {
            const focusedCard = document.activeElement.closest('.news-card');
            if (focusedCard) {
                const cards = Array.from(document.querySelectorAll('.news-card'));
                const currentIndex = cards.indexOf(focusedCard);
                let nextIndex;
                
                if (event.key === 'ArrowRight') {
                    nextIndex = (currentIndex + 1) % cards.length;
                } else {
                    nextIndex = (currentIndex - 1 + cards.length) % cards.length;
                }
                
                cards[nextIndex].focus();
                event.preventDefault();
            }
        }
    });
});

// Close dropdowns when clicking outside
document.addEventListener('click', function(event) {
    if (!event.target.closest('.actions-dropdown')) {
        document.querySelectorAll('.actions-menu').forEach(menu => {
            menu.classList.add('hidden');
        });
    }
});

// Add loading animation for form submissions
document.querySelectorAll('form').forEach(form => {
    form.addEventListener('submit', function() {
        const card = this.closest('.news-card');
        if (card) {
            card.classList.add('loading');
        }
    });
});

// Smooth scroll to top functionality
function scrollToTop() {
    window.scrollTo({
        top: 0,
        behavior: 'smooth'
    });
}

// Add scroll-to-top button if page is long
if (document.querySelectorAll('.news-card').length > 8) {
    const scrollBtn = document.createElement('button');
    scrollBtn.innerHTML = '<i class="fas fa-arrow-up"></i>';
    scrollBtn.className = 'btn btn-primary position-fixed';
    scrollBtn.style.cssText = `
        bottom: 20px;
        right: 20px;
        z-index: 1000;
        border-radius: 50%;
        width: 50px;
        height: 50px;
        display: none;
        box-shadow: 0 4px 15px rgba(0,0,0,0.2);
    `;
    scrollBtn.onclick = scrollToTop;
    document.body.appendChild(scrollBtn);
    
    window.addEventListener('scroll', function() {
        if (window.pageYOffset > 300) {
            scrollBtn.style.display = 'block';
        } else {
            scrollBtn.style.display = 'none';
        }
    });
}

// Bulk Actions & Quick Toggle Functions
function onNewsCheckboxChange() {
    const checkboxes = document.querySelectorAll('.news-checkbox:checked');
    const allCheckboxes = document.querySelectorAll('.news-checkbox');
    const toolbar = document.getElementById('bulk-actions-toolbar');
    const countBadge = document.getElementById('selected-count-badge');
    const masterCheckbox = document.getElementById('select-all-checkbox');

    const count = checkboxes.length;
    if (count > 0) {
        toolbar.style.display = 'flex';
        countBadge.innerText = `${count} seleccionada${count > 1 ? 's' : ''}`;
    } else {
        toolbar.style.display = 'none';
    }

    if (masterCheckbox && allCheckboxes.length > 0) {
        masterCheckbox.checked = (count === allCheckboxes.length);
    }
}

function toggleSelectAll(master) {
    const checkboxes = document.querySelectorAll('.news-checkbox');
    checkboxes.forEach(cb => cb.checked = master.checked);
    onNewsCheckboxChange();
}

function clearSelection() {
    const checkboxes = document.querySelectorAll('.news-checkbox');
    checkboxes.forEach(cb => cb.checked = false);
    const master = document.getElementById('select-all-checkbox');
    if (master) master.checked = false;
    onNewsCheckboxChange();
}

function executeBulkAction(action) {
    const checked = document.querySelectorAll('.news-checkbox:checked');
    const ids = Array.from(checked).map(cb => parseInt(cb.value));

    if (ids.length === 0) {
        alert('Por favor selecciona al menos una noticia.');
        return;
    }

    let confirmMsg = `¿Estás seguro de procesar ${ids.length} noticias?`;
    if (action === 'delete') {
        confirmMsg = `¡ATENCIÓN! ¿Estás seguro de eliminar permanentemente ${ids.length} noticias seleccionadas?`;
    } else if (action === 'publish') {
        confirmMsg = `¿Deseas publicar ${ids.length} noticias seleccionadas?`;
    } else if (action === 'unpublish') {
        confirmMsg = `¿Deseas despublicar ${ids.length} noticias seleccionadas y moverlas a borradores?`;
    }

    if (!confirm(confirmMsg)) return;

    const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    fetch('{{ route("admin.noticias.bulk-action") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': token
        },
        body: JSON.stringify({ action: action, ids: ids })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            window.location.reload();
        } else {
            alert(data.message || 'Ocurrió un error al ejecutar la acción por lote.');
        }
    })
    .catch(err => {
        console.error(err);
        alert('Error de conexión al procesar la solicitud.');
    });
}

function quickToggleStatus(id, btnElement) {
    const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    fetch(`{{ url('admin/noticias') }}/${id}/toggle-status`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': token
        }
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            if (btnElement) {
                const label = btnElement.querySelector('.status-label');
                const icon = btnElement.querySelector('i');
                if (data.publicada) {
                    btnElement.className = 'btn btn-sm px-2.5 py-1 text-xs fw-bold rounded-pill shadow-sm border-0 transition-all duration-200 btn-success text-white';
                    if (label) label.innerText = 'Publicada';
                    if (icon) icon.className = 'fas fa-check me-1';
                } else {
                    btnElement.className = 'btn btn-sm px-2.5 py-1 text-xs fw-bold rounded-pill shadow-sm border-0 transition-all duration-200 btn-warning text-dark';
                    if (label) label.innerText = 'Borrador';
                    if (icon) icon.className = 'fas fa-clock me-1';
                }
            } else {
                window.location.reload();
            }
        } else {
            alert('No se pudo cambiar el estado de la noticia.');
        }
    })
    .catch(err => {
        console.error(err);
        alert('Error al conectar con el servidor.');
    });
}
</script>

<style>
/* Additional CSS for enhanced animations */
.ripple {
    position: absolute;
    border-radius: 50%;
    background: rgba(79, 70, 229, 0.3);
    transform: scale(0);
    animation: ripple-animation 0.6s linear;
    pointer-events: none;
}

@keyframes ripple-animation {
    to {
        transform: scale(4);
        opacity: 0;
    }
}

/* Focus styles for accessibility */
.news-card:focus {
    outline: 2px solid #4f46e5;
    outline-offset: 2px;
}

/* Loading state for cards */
.news-card.loading {
    opacity: 0.7;
    pointer-events: none;
}

.news-card.loading::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.8), transparent);
    animation: loading-shimmer 1.5s infinite;
}

@keyframes loading-shimmer {
    0% { transform: translateX(-100%); }
    100% { transform: translateX(100%); }
}
</style>
@endpush
@endsection
