import './bootstrap';

document.addEventListener('alpine:init', () => {
    Alpine.store('sidebar', {
        collapsed: localStorage.getItem('sidebarCollapsed') === 'true',
        
        toggle() {
            this.collapsed = !this.collapsed;
            localStorage.setItem('sidebarCollapsed', this.collapsed);
        }
    });

    Alpine.data('sidebarMenu', (initialState = {}) => ({
        openMenus: {
            master: false,
            student: false,
            book: false,
            transaction: false,
            report: false,
            ...initialState
        },
        
        toggle(menu) {
            this.openMenus[menu] = !this.openMenus[menu];
        },
        
        isOpen(menu) {
            return this.openMenus[menu];
        }
    }));
});

