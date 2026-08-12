import Alpine from 'alpinejs';
import persist from '@alpinejs/persist';
import focus from '@alpinejs/focus';

window.Alpine = Alpine;

Alpine.plugin(persist);
Alpine.plugin(focus);

Alpine.data('darkMode', () => ({
    mode: Alpine.$persist('system').as('cms-theme'),
    get isDark() {
        return document.documentElement.classList.contains('dark');
    },
    init() {
        this.apply();
    },
    apply() {
        const dark =
            this.mode === 'dark' ||
            (this.mode === 'system' &&
                window.matchMedia('(prefers-color-scheme: dark)').matches);

        document.documentElement.classList.toggle('dark', dark);
    },
    set(mode) {
        this.mode = mode;
        this.apply();
    },
}));

Alpine.data('sidebar', () => ({
    open: false,
    close() {
        this.open = false;
    },
}));

Alpine.data('toasts', () => ({
    toasts: [],
    init() {
        const flash = this.$root.dataset.flash;

        if (flash) {
            try {
                const items = JSON.parse(flash);
                items.forEach((item) => this.push(item));
            } catch (e) {
                /* ignore malformed flash */
            }
        }
    },
    push({ type = 'success', message = '' }) {
        const id = Date.now() + Math.random();
        this.toasts.push({ id, type, message });
        setTimeout(() => this.remove(id), 5000);
    },
    remove(id) {
        this.toasts = this.toasts.filter((t) => t.id !== id);
    },
    icon(type) {
        const icons = {
            success: 'check-circle',
            error: 'x-circle',
            warning: 'alert-triangle',
            info: 'information-circle',
        };
        return icons[type] || 'information-circle';
    },
}));

Alpine.data('confirmDialog', () => ({
    open: false,
    title: 'Are you sure?',
    message: 'This action cannot be undone.',
    confirmText: 'Confirm',
    onConfirm: null,
    show(title, message, confirmText, onConfirm) {
        this.title = title || this.title;
        this.message = message || this.message;
        this.confirmText = confirmText || this.confirmText;
        this.onConfirm = onConfirm;
        this.open = true;
    },
    confirm() {
        if (typeof this.onConfirm === 'function') {
            this.onConfirm();
        }
        this.open = false;
    },
}));

Alpine.data('confirmForm', () => ({
    activeForm: null,
    init() {
        window.addEventListener('confirm-submit', (e) => {
            const form = e.target.closest('form');
            if (form) {
                this.activeForm = form;
            }
        });
    },
    proceed() {
        if (this.activeForm) {
            this.activeForm.submit();
        }
        this.activeForm = null;
    },
}));

Alpine.data('fileInput', () => ({
    fileName: '',
    previewUrl: '',
    update(event) {
        const file = event.target.files?.[0];
        if (!file) return;
        this.fileName = file.name;
        if (file.type.startsWith('image/')) {
            this.previewUrl = URL.createObjectURL(file);
        }
    },
}));

Alpine.data('searchable', () => ({
    query: '',
    get results() {
        const q = this.query.toLowerCase();
        return this.items.filter((item) =>
            this.by(item).toLowerCase().includes(q),
        );
    },
}));

window.searchSelect = (items, selected) => ({
    query: '',
    open: false,
    items,
    selected,
    get filtered() {
        const q = this.query.trim().toLowerCase();
        return q === ''
            ? this.items
            : this.items.filter((o) => `${o.label} ${o.value}`.toLowerCase().includes(q));
    },
    select(option) {
        this.selected = option;
        this.open = false;
        this.query = '';
    },
});

Alpine.data('tabs', () => ({
    active: '',
    set(name) {
        this.active = name;
    },
}));

Alpine.start();
