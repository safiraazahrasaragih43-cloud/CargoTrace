import './bootstrap';
import L from 'leaflet';
import 'leaflet/dist/leaflet.css';
import Chart from 'chart.js/auto';

window.L = L;
window.Chart = Chart;

/**
 * Escape untrusted strings before rendering into DOM HTML sinks or Leaflet popups.
 */
window.escapeHtml = function escapeHtml(value) {
    if (value === null || value === undefined) return '';
    return String(value).replace(/[&<>"'`]/g, (ch) => ({
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#39;',
        '`': '&#96;',
    })[ch]);
};
