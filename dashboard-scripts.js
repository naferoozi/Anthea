// Ready-Copy Admin Dashboard JavaScript

document.addEventListener('DOMContentLoaded', function() {
    // Initialize dashboard
    initializeDashboard();
    
    // Initialize charts
    initializeCharts();
    
    // Initialize event listeners
    initializeEventListeners();
    
    // Initialize tooltips and popovers
    initializeBootstrapComponents();
});

function initializeDashboard() {
    console.log('Ready-Copy Admin Dashboard initialized');
    
    // Add fade-in animation to main content
    const mainContent = document.querySelector('.main-content');
    if (mainContent) {
        mainContent.classList.add('fade-in');
    }
    
    // Update last activity time
    updateLastActivity();
    
    // Start real-time updates
    startRealTimeUpdates();
}

function initializeCharts() {
    // Content Creation Chart
    const contentChartCtx = document.getElementById('contentChart');
    if (contentChartCtx) {
        new Chart(contentChartCtx, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                datasets: [{
                    label: 'Content Created',
                    data: [65, 78, 90, 81, 95, 105, 120, 110, 125, 140, 155, 170],
                    borderColor: '#0d6efd',
                    backgroundColor: 'rgba(13, 110, 253, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.1)'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                },
                elements: {
                    point: {
                        radius: 4,
                        hoverRadius: 6
                    }
                }
            }
        });
    }
    
    // Content Types Pie Chart
    const pieChartCtx = document.getElementById('pieChart');
    if (pieChartCtx) {
        new Chart(pieChartCtx, {
            type: 'doughnut',
            data: {
                labels: ['Email Templates', 'Blog Posts', 'Social Media', 'Newsletters'],
                datasets: [{
                    data: [35, 25, 25, 15],
                    backgroundColor: [
                        '#0d6efd',
                        '#198754',
                        '#ffc107',
                        '#dc3545'
                    ],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 20,
                            usePointStyle: true
                        }
                    }
                }
            }
        });
    }
}

function initializeEventListeners() {
    // Sidebar navigation
    const sidebarLinks = document.querySelectorAll('.sidebar .nav-link');
    sidebarLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            // Remove active class from all links
            sidebarLinks.forEach(l => l.classList.remove('active'));
            // Add active class to clicked link
            this.classList.add('active');
            
            // Update page title
            const linkText = this.textContent.trim();
            document.title = `${linkText} - Ready-Copy Admin`;
        });
    });
    
    // Mobile sidebar toggle
    const sidebarToggle = document.querySelector('.navbar-toggler');
    const sidebar = document.querySelector('.sidebar');
    
    if (sidebarToggle && sidebar) {
        sidebarToggle.addEventListener('click', function() {
            sidebar.classList.toggle('show');
        });
    }
    
    // Modal form submissions
    initializeModalForms();
    
    // Search functionality
    initializeSearch();
    
    // Notification handling
    initializeNotifications();
}

function initializeModalForms() {
    // New Content Form
    const newContentForm = document.querySelector('#newContentModal form');
    if (newContentForm) {
        const submitBtn = document.querySelector('#newContentModal .btn-primary');
        submitBtn.addEventListener('click', function(e) {
            e.preventDefault();
            
            const formData = new FormData(newContentForm);
            const title = document.getElementById('contentTitle').value;
            const type = document.getElementById('contentType').value;
            const description = document.getElementById('contentDescription').value;
            
            if (title && type) {
                // Simulate form submission
                submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Creating...';
                submitBtn.disabled = true;
                
                setTimeout(() => {
                    // Reset form
                    newContentForm.reset();
                    
                    // Close modal
                    const modal = bootstrap.Modal.getInstance(document.getElementById('newContentModal'));
                    modal.hide();
                    
                    // Show success message
                    showNotification('Content created successfully!', 'success');
                    
                    // Reset button
                    submitBtn.innerHTML = 'Create Content';
                    submitBtn.disabled = false;
                    
                    // Update stats (simulate)
                    updateContentStats();
                }, 1500);
            } else {
                showNotification('Please fill in all required fields.', 'error');
            }
        });
    }
    
    // New User Form
    const newUserForm = document.querySelector('#newUserModal form');
    if (newUserForm) {
        const submitBtn = document.querySelector('#newUserModal .btn-primary');
        submitBtn.addEventListener('click', function(e) {
            e.preventDefault();
            
            const name = document.getElementById('userName').value;
            const email = document.getElementById('userEmail').value;
            const role = document.getElementById('userRole').value;
            
            if (name && email && role) {
                // Simulate form submission
                submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Adding...';
                submitBtn.disabled = true;
                
                setTimeout(() => {
                    // Reset form
                    newUserForm.reset();
                    
                    // Close modal
                    const modal = bootstrap.Modal.getInstance(document.getElementById('newUserModal'));
                    modal.hide();
                    
                    // Show success message
                    showNotification('User added successfully!', 'success');
                    
                    // Reset button
                    submitBtn.innerHTML = 'Add User';
                    submitBtn.disabled = false;
                    
                    // Update stats (simulate)
                    updateUserStats();
                }, 1500);
            } else {
                showNotification('Please fill in all required fields.', 'error');
            }
        });
    }
}

function initializeSearch() {
    // Add search functionality (placeholder)
    const searchInput = document.createElement('input');
    searchInput.type = 'text';
    searchInput.className = 'form-control form-control-sm';
    searchInput.placeholder = 'Search...';
    
    // Add to navbar if needed
    // This is a placeholder for future search implementation
}

function initializeNotifications() {
    // Notification dropdown functionality
    const notificationDropdown = document.querySelector('.dropdown-toggle[data-bs-toggle="dropdown"]');
    if (notificationDropdown) {
        notificationDropdown.addEventListener('click', function() {
            // Mark notifications as read
            const badge = this.querySelector('.badge');
            if (badge) {
                setTimeout(() => {
                    badge.style.display = 'none';
                }, 2000);
            }
        });
    }
}

function initializeBootstrapComponents() {
    // Initialize tooltips
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function(tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    // Initialize popovers
    const popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
    popoverTriggerList.map(function(popoverTriggerEl) {
        return new bootstrap.Popover(popoverTriggerEl);
    });
}

function showNotification(message, type = 'info') {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `alert alert-${type === 'error' ? 'danger' : type} alert-dismissible fade show position-fixed`;
    notification.style.cssText = 'top: 80px; right: 20px; z-index: 1050; min-width: 300px;';
    
    notification.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.body.appendChild(notification);
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        if (notification.parentNode) {
            notification.remove();
        }
    }, 5000);
}

function updateLastActivity() {
    const now = new Date();
    const timeString = now.toLocaleTimeString();
    
    // Update any "last activity" elements
    const lastActivityElements = document.querySelectorAll('.last-activity');
    lastActivityElements.forEach(element => {
        element.textContent = `Last updated: ${timeString}`;
    });
}

function startRealTimeUpdates() {
    // Simulate real-time updates every 30 seconds
    setInterval(() => {
        updateLastActivity();
        
        // Simulate random stat updates
        if (Math.random() > 0.7) {
            updateRandomStats();
        }
    }, 30000);
}

function updateContentStats() {
    const contentStat = document.querySelector('.border-left-primary .h5');
    if (contentStat) {
        const currentValue = parseInt(contentStat.textContent.replace(',', ''));
        contentStat.textContent = (currentValue + 1).toLocaleString();
    }
}

function updateUserStats() {
    const userStat = document.querySelector('.border-left-success .h5');
    if (userStat) {
        const currentValue = parseInt(userStat.textContent);
        userStat.textContent = currentValue + 1;
    }
}

function updateRandomStats() {
    // Randomly update one of the stats
    const stats = document.querySelectorAll('.card .h5');
    if (stats.length > 0) {
        const randomStat = stats[Math.floor(Math.random() * stats.length)];
        const currentValue = parseInt(randomStat.textContent.replace(/[,\s]/g, ''));
        const change = Math.floor(Math.random() * 5) - 2; // -2 to +2
        const newValue = Math.max(0, currentValue + change);
        randomStat.textContent = newValue.toLocaleString();
        
        // Add a subtle animation
        randomStat.style.transform = 'scale(1.1)';
        setTimeout(() => {
            randomStat.style.transform = 'scale(1)';
        }, 200);
    }
}

// Export functions for external use
window.ReadyCopyDashboard = {
    showNotification,
    updateContentStats,
    updateUserStats
};

// Handle page visibility changes
document.addEventListener('visibilitychange', function() {
    if (document.visibilityState === 'visible') {
        // Page became visible, refresh data
        updateLastActivity();
    }
});

// Handle window resize for responsive behavior
window.addEventListener('resize', function() {
    // Redraw charts if needed
    Chart.helpers.each(Chart.instances, function(instance) {
        instance.resize();
    });
});

// Keyboard shortcuts
document.addEventListener('keydown', function(e) {
    // Ctrl/Cmd + K for search
    if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
        e.preventDefault();
        // Focus search input if it exists
        const searchInput = document.querySelector('input[type="search"], input[placeholder*="search" i]');
        if (searchInput) {
            searchInput.focus();
        }
    }
    
    // Escape to close modals
    if (e.key === 'Escape') {
        const openModals = document.querySelectorAll('.modal.show');
        openModals.forEach(modal => {
            const modalInstance = bootstrap.Modal.getInstance(modal);
            if (modalInstance) {
                modalInstance.hide();
            }
        });
    }
});

// Add loading states for async operations
function addLoadingState(element) {
    element.classList.add('loading');
    const spinner = document.createElement('div');
    spinner.className = 'spinner-border spinner-border-sm';
    spinner.setAttribute('role', 'status');
    element.appendChild(spinner);
}

function removeLoadingState(element) {
    element.classList.remove('loading');
    const spinner = element.querySelector('.spinner-border');
    if (spinner) {
        spinner.remove();
    }
}

// Utility functions
function formatNumber(num) {
    return num.toLocaleString();
}

function formatDate(date) {
    return new Intl.DateTimeFormat('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    }).format(date);
}

function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// Theme toggle (optional feature)
function toggleTheme() {
    const body = document.body;
    const isDark = body.classList.contains('dark-theme');
    
    if (isDark) {
        body.classList.remove('dark-theme');
        localStorage.setItem('theme', 'light');
    } else {
        body.classList.add('dark-theme');
        localStorage.setItem('theme', 'dark');
    }
}

// Load saved theme
const savedTheme = localStorage.getItem('theme');
if (savedTheme === 'dark') {
    document.body.classList.add('dark-theme');
}