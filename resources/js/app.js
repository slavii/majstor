import Alpine from 'alpinejs';
import flatpickr from 'flatpickr';
import { Bulgarian } from 'flatpickr/dist/l10n/bg.js';
import 'flatpickr/dist/flatpickr.min.css';

flatpickr.localize(Bulgarian);

Alpine.data('datepicker', () => ({
    init() {
        flatpickr(this.$refs.input, {
            dateFormat: 'Y-m-d',
            altInput: true,
            altFormat: 'd.m.Y',
            locale: {
                ...Bulgarian,
                firstDayOfWeek: 1,
            },
            allowInput: true,
            defaultDate: this.$refs.input.value || null,
        });
    }
}));

Alpine.data('checklist', () => ({
    items: [],
    newItem: '',
    init() {
        const raw = this.$el.dataset.items;
        if (raw) {
            try { this.items = JSON.parse(raw); } catch (e) { this.items = []; }
        }
    },
    add() {
        if (!this.newItem.trim()) return;
        this.items.push({ text: this.newItem.trim(), done: false });
        this.newItem = '';
    },
    remove(index) {
        this.items.splice(index, 1);
    },
    toggle(index) {
        this.items[index].done = !this.items[index].done;
    },
    get json() {
        return JSON.stringify(this.items);
    }
}));

window.Alpine = Alpine;
Alpine.start();
