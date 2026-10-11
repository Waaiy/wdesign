/* =========================================================================
 * Waaiy Tasarım Stüdyosu
 * - Header / blog / sektör varyasyonları
 * - RGB renk seçicileri + otomatik palet üretimi
 * - localStorage kalıcılığı, mobil menü, reveal animasyonu, içindekiler
 * ========================================================================= */
(function () {
    'use strict';

    const CONFIG = window.WAAIY_DESIGN || null;
    const STORAGE_KEY = 'waaiy-design';
    const root = document.documentElement;

    /* ---------------------------------------------------------------- Renk */
    function normalizeHex(hex) {
        hex = String(hex || '').trim().replace('#', '');
        if (hex.length === 3) hex = hex.split('').map((c) => c + c).join('');
        if (!/^[0-9a-fA-F]{6}$/.test(hex)) return null;
        return '#' + hex.toLowerCase();
    }
    function hexToRgb(hex) {
        hex = normalizeHex(hex) || '#000000';
        hex = hex.slice(1);
        return [parseInt(hex.slice(0, 2), 16), parseInt(hex.slice(2, 4), 16), parseInt(hex.slice(4, 6), 16)];
    }
    function rgbToHex(r, g, b) {
        const h = (n) => Math.max(0, Math.min(255, Math.round(n))).toString(16).padStart(2, '0');
        return '#' + h(r) + h(g) + h(b);
    }
    function mix(hex, target, amount) {
        const a = hexToRgb(hex), b = hexToRgb(target);
        return rgbToHex(
            a[0] + (b[0] - a[0]) * amount,
            a[1] + (b[1] - a[1]) * amount,
            a[2] + (b[2] - a[2]) * amount
        );
    }
    function palette(hex, name) {
        const white = '#ffffff', black = '#000000';
        const stops = {
            50: [white, 0.92], 100: [white, 0.84], 200: [white, 0.68],
            300: [white, 0.46], 400: [white, 0.24], 500: [white, 0.10],
            600: null,
            700: [black, 0.14], 800: [black, 0.28], 900: [black, 0.42],
        };
        const base = normalizeHex(hex) || '#000000';
        const out = {};
        for (const k in stops) {
            out['--' + name + '-' + k] = stops[k] ? mix(base, stops[k][0], stops[k][1]) : base;
        }
        return out;
    }

    /* --------------------------------------------------------------- State */
    function defaultState() {
        const s = CONFIG.sectors[CONFIG.defaults.sector];
        return {
            header: CONFIG.defaults.header,
            blogLayout: CONFIG.defaults.blogLayout,
            sector: CONFIG.defaults.sector,
            primary: s.primary,
            accent: s.accent,
            radius: s.radius,
        };
    }
    function loadState() {
        try { return JSON.parse(localStorage.getItem(STORAGE_KEY)); } catch (e) { return null; }
    }
    function saveState(state) {
        try { localStorage.setItem(STORAGE_KEY, JSON.stringify(state)); } catch (e) {}
    }
    function computeVars(state) {
        const s = CONFIG.sectors[state.sector] || CONFIG.sectors[CONFIG.defaults.sector];
        const vars = Object.assign(
            {},
            palette(state.primary, 'brand'),
            palette(state.accent, 'accent')
        );
        vars['--brand-rgb'] = hexToRgb(state.primary).join(', ');
        vars['--accent-rgb'] = hexToRgb(state.accent).join(', ');
        vars['--bg'] = s.bg;
        vars['--surface'] = s.surface;
        vars['--text'] = s.text;
        vars['--muted'] = s.muted;
        vars['--dark'] = s.dark;
        vars['--radius'] = (state.radius != null ? state.radius : s.radius) + 'px';
        vars['--font-heading'] = s.font;
        return vars;
    }

    /* -------------------------------------------------------------- Apply */
    const ORIGINALS = {};

    function readText(el) {
        for (let i = el.childNodes.length - 1; i >= 0; i--) {
            const n = el.childNodes[i];
            if (n.nodeType === 3 && n.textContent.trim() !== '') return n.textContent.trim();
        }
        return el.textContent.trim();
    }

    // Yalnızca son metin düğümünü değiştirir; ikon/span çocukları korunur.
    function setTextPreserve(el, text) {
        for (let i = el.childNodes.length - 1; i >= 0; i--) {
            const n = el.childNodes[i];
            if (n.nodeType === 3 && n.textContent.trim() !== '') { n.textContent = text; return; }
        }
        el.appendChild(document.createTextNode(text));
    }

    function captureOriginals() {
        document.querySelectorAll('[data-sector-text]').forEach((el) => {
            ORIGINALS[el.dataset.sectorText] = readText(el);
        });
    }

    function applySectorText(sectorKey) {
        const isDefault = String(sectorKey) === String(CONFIG.defaults.sector);
        const s = CONFIG.sectors[sectorKey];
        document.querySelectorAll('[data-sector-text]').forEach((el) => {
            const key = el.dataset.sectorText;
            let val = null;
            if (!isDefault && s) {
                val = key.split('.').reduce((o, k) => (o == null ? null : o[k]), s);
            }
            // Varsayılan sektörde sunucudan gelen (translation) metin korunur.
            if (typeof val !== 'string') val = ORIGINALS[key];
            if (typeof val === 'string') setTextPreserve(el, val);
        });
    }

    function syncControls(state) {
        document.querySelectorAll('[data-seg]').forEach((seg) => {
            const type = seg.dataset.seg;
            seg.querySelectorAll('[data-value]').forEach((btn) => {
                const v = btn.dataset.value;
                const active =
                    (type === 'header' && String(state.header) === v) ||
                    (type === 'blogLayout' && String(state.blogLayout) === v) ||
                    (type === 'sector' && state.sector === v);
                btn.classList.toggle('is-active', active);
            });
        });

        document.querySelectorAll('[data-hint]').forEach((el) => {
            const type = el.dataset.hint;
            if (type === 'header' && CONFIG.headers[state.header]) el.textContent = CONFIG.headers[state.header].description;
            if (type === 'blogLayout' && CONFIG.blogLayouts[state.blogLayout]) el.textContent = CONFIG.blogLayouts[state.blogLayout].description;
            if (type === 'sector' && CONFIG.sectors[state.sector]) {
                const s = CONFIG.sectors[state.sector];
                el.textContent = s.label + ' · ' + s.tagline;
            }
        });

        syncPick('primary', state.primary);
        syncPick('accent', state.accent);

        const range = document.querySelector('[data-radius]');
        if (range) range.value = state.radius;
        const out = document.querySelector('[data-radius-out]');
        if (out) out.textContent = state.radius + 'px';
    }

    function apply(state, persist) {
        const vars = computeVars(state);
        state.vars = vars;
        for (const k in vars) root.style.setProperty(k, vars[k]);
        root.dataset.header = state.header;
        root.dataset.blog = state.blogLayout;
        root.dataset.sector = state.sector;

        applySectorText(state.sector);
        syncControls(state);
        if (persist !== false) saveState(state);
    }

    /* ---------------------------------------------------------- RGB picker */
    function syncPick(name, hex) {
        const box = document.querySelector('.pick[data-color="' + name + '"]');
        if (!box) return;
        hex = normalizeHex(hex) || '#000000';
        const rgb = hexToRgb(hex);
        const native = box.querySelector('[data-native]');
        const hexInput = box.querySelector('[data-hex]');
        const preview = box.querySelector('[data-preview]');
        if (native) native.value = hex;
        if (hexInput && document.activeElement !== hexInput) hexInput.value = hex;
        if (preview) preview.style.background = hex;
        ['r', 'g', 'b'].forEach((ch, i) => {
            const slider = box.querySelector('[data-channel="' + ch + '"]');
            const output = box.querySelector('[data-out="' + ch + '"]');
            if (slider) slider.value = rgb[i];
            if (output) output.textContent = rgb[i];
        });
    }

    function bindPicks(state) {
        document.querySelectorAll('.pick[data-color]').forEach((box) => {
            const name = box.dataset.color;
            const native = box.querySelector('[data-native]');
            const hexInput = box.querySelector('[data-hex]');
            const sliders = ['r', 'g', 'b'].map((ch) => box.querySelector('[data-channel="' + ch + '"]'));

            const commit = (hex, fromHexField) => {
                const norm = normalizeHex(hex);
                if (!norm) return;
                state[name] = norm;
                apply(state);
                if (!fromHexField && hexInput) hexInput.value = norm;
            };

            if (native) native.addEventListener('input', (e) => commit(e.target.value));
            if (hexInput) {
                hexInput.addEventListener('input', (e) => {
                    if (normalizeHex(e.target.value)) commit(e.target.value, true);
                });
                hexInput.addEventListener('blur', (e) => { e.target.value = state[name]; });
            }
            sliders.forEach((slider) => {
                if (slider) slider.addEventListener('input', () => {
                    const rgb = sliders.map((s) => (s ? Number(s.value) : 0));
                    commit(rgbToHex(rgb[0], rgb[1], rgb[2]));
                });
            });
        });
    }

    /* --------------------------------------------------- Customizer panel */
    function bindCustomizer(state) {
        const open = () => { document.body.classList.add('cz-open'); const t = document.querySelector('[data-customizer-toggle]'); if (t) t.setAttribute('aria-expanded', 'true'); };
        const close = () => { document.body.classList.remove('cz-open'); const t = document.querySelector('[data-customizer-toggle]'); if (t) t.setAttribute('aria-expanded', 'false'); };

        document.querySelectorAll('[data-customizer-toggle]').forEach((b) => b.addEventListener('click', open));
        document.querySelectorAll('[data-customizer-close]').forEach((b) => b.addEventListener('click', close));
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') { close(); document.body.classList.remove('drawer-open'); }
        });

        document.querySelectorAll('[data-seg]').forEach((seg) => {
            const type = seg.dataset.seg;
            seg.querySelectorAll('[data-value]').forEach((btn) => {
                btn.addEventListener('click', () => {
                    const v = btn.dataset.value;
                    if (type === 'header') { state.header = Number(v); apply(state); }
                    else if (type === 'blogLayout') { state.blogLayout = Number(v); apply(state); }
                    else if (type === 'sector' && CONFIG.sectors[v]) {
                        const s = CONFIG.sectors[v];
                        state.sector = v;
                        state.primary = s.primary;
                        state.accent = s.accent;
                        state.radius = s.radius;
                        apply(state);
                    }
                });
            });
        });

        const range = document.querySelector('[data-radius]');
        if (range) range.addEventListener('input', () => {
            state.radius = Number(range.value);
            apply(state);
        });

        const reset = document.querySelector('[data-customizer-reset]');
        if (reset) reset.addEventListener('click', () => {
            try { localStorage.removeItem(STORAGE_KEY); } catch (e) {}
            const fresh = defaultState();
            Object.assign(state, fresh);
            apply(state);
        });
    }

    /* ------------------------------------------------------- Mobile menü */
    function bindMenu() {
        document.querySelectorAll('[data-menu-toggle]').forEach((b) =>
            b.addEventListener('click', () => document.body.classList.add('drawer-open')));
        document.querySelectorAll('[data-menu-close]').forEach((b) =>
            b.addEventListener('click', () => document.body.classList.remove('drawer-open')));
        document.querySelectorAll('.mobile-nav__link, .mobile-drawer .btn').forEach((a) =>
            a.addEventListener('click', () => document.body.classList.remove('drawer-open')));
    }

    /* -------------------------------------------------------------- Reveal */
    function bindReveal() {
        const items = document.querySelectorAll('.reveal');
        if (!items.length) return;
        if (!('IntersectionObserver' in window)) {
            items.forEach((el) => el.classList.add('is-visible'));
            return;
        }
        const io = new IntersectionObserver((entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) { entry.target.classList.add('is-visible'); io.unobserve(entry.target); }
            });
        }, { rootMargin: '0px 0px -10% 0px', threshold: 0.08 });
        items.forEach((el) => io.observe(el));
    }

    /* ----------------------------------------------------------- İçindekiler */
    function buildToc() {
        const toc = document.querySelector('[data-toc]');
        const content = document.querySelector('[data-article-content]');
        if (!toc || !content) return;
        const headings = content.querySelectorAll('h2, h3');
        if (!headings.length) return;
        const list = toc.querySelector('.toc__list');
        const slug = (t, i) => {
            const base = t.toLowerCase().replace(/[^a-z0-9ğüşıöç]+/gi, '-').replace(/^-+|-+$/g, '');
            return (base || 'baslik') + '-' + i;
        };
        headings.forEach((h, i) => {
            if (!h.id) h.id = slug(h.textContent.trim(), i);
            const li = document.createElement('li');
            const a = document.createElement('a');
            a.href = '#' + h.id;
            a.textContent = h.textContent.trim();
            if (h.tagName === 'H3') li.className = 'lvl-3';
            li.appendChild(a);
            list.appendChild(li);
        });
        toc.hidden = false;
    }

    /* ------------------------------------------------------------ Ürün */
    function bindGallery() {
        const main = document.getElementById('pd-main');
        const thumbs = document.querySelectorAll('[data-gallery-thumbs] .pd__thumb');
        if (!main || !thumbs.length) return;
        thumbs.forEach((thumb) => thumb.addEventListener('click', () => {
            if (thumb.dataset.src) main.src = thumb.dataset.src;
            thumbs.forEach((t) => t.classList.remove('is-active'));
            thumb.classList.add('is-active');
        }));
    }

    function bindQty() {
        document.querySelectorAll('[data-qty]').forEach((box) => {
            const input = box.querySelector('[data-qty-input]');
            if (!input) return;
            const clamp = (n) => Math.max(1, Math.min(99, n));
            const dec = box.querySelector('[data-qty-dec]');
            const inc = box.querySelector('[data-qty-inc]');
            if (dec) dec.addEventListener('click', () => { input.value = clamp((parseInt(input.value, 10) || 1) - 1); });
            if (inc) inc.addEventListener('click', () => { input.value = clamp((parseInt(input.value, 10) || 1) + 1); });
        });
    }

    function bindVariants() {
        document.querySelectorAll('[data-variant]').forEach((group) => {
            group.querySelectorAll('[data-option]').forEach((btn) => btn.addEventListener('click', () => {
                group.querySelectorAll('[data-option]').forEach((b) => b.classList.remove('is-active'));
                btn.classList.add('is-active');
            }));
        });
    }

    function bindTabs() {
        document.querySelectorAll('[data-tabs]').forEach((tabs) => {
            const btns = tabs.querySelectorAll('[data-tab-btn]');
            const panels = tabs.querySelectorAll('[data-tab-panel]');
            btns.forEach((btn) => btn.addEventListener('click', () => {
                const id = btn.dataset.tabBtn;
                btns.forEach((b) => b.classList.toggle('is-active', b === btn));
                panels.forEach((p) => p.classList.toggle('is-active', p.dataset.tabPanel === id));
            }));
        });
    }

    function bindProductActions() {
        document.querySelectorAll('.pd__add').forEach((btn) => btn.addEventListener('click', () => {
            const original = btn.textContent;
            btn.textContent = 'Eklendi ✓';
            btn.disabled = true;
            setTimeout(() => { btn.textContent = original; btn.disabled = false; }, 1400);
        }));
        document.querySelectorAll('.pd__fav').forEach((btn) => btn.addEventListener('click', () => {
            btn.classList.toggle('is-active');
        }));
    }

    /* --------------------------------------------------------------- Init */
    function init() {
        if (CONFIG) {
            captureOriginals();
            let state = Object.assign(defaultState(), loadState() || {});
            bindPicks(state);
            bindCustomizer(state);
            apply(state, false);
        }
        bindMenu();
        bindReveal();
        buildToc();
        bindGallery();
        bindQty();
        bindVariants();
        bindTabs();
        bindProductActions();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
