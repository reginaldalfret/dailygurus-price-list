/**
 * DailyGurus Admin Panel - JavaScript
 */

document.addEventListener('DOMContentLoaded', () => {
    initAdminSidebar();
    initPriceTableFilter();
    initBulkParser();
});

/**
 * 1. Mobile Sidebar Toggle
 */
function initAdminSidebar() {
    const toggleBtn = document.getElementById('sidebar-toggle');
    const sidebar = document.getElementById('admin-sidebar');

    if (toggleBtn && sidebar) {
        toggleBtn.addEventListener('click', () => {
            sidebar.classList.toggle('open');
        });

        document.addEventListener('click', (e) => {
            if (window.innerWidth <= 991 && 
                sidebar.classList.contains('open') && 
                !sidebar.contains(e.target) && 
                !toggleBtn.contains(e.target)) {
                sidebar.classList.remove('open');
            }
        });
    }
}

/**
 * 2. Price Table Live Search Filter
 */
function initPriceTableFilter() {
    const searchInput = document.getElementById('admin-price-filter');
    if (!searchInput) return;

    const rows = document.querySelectorAll('.admin-price-row');

    searchInput.addEventListener('input', () => {
        const query = searchInput.value.trim().toLowerCase();

        rows.forEach(row => {
            const name = row.querySelector('.product-name-col')?.textContent.toLowerCase() || '';
            const cat = row.querySelector('.product-cat-col')?.textContent.toLowerCase() || '';

            if (name.includes(query) || cat.includes(query)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });
}

/**
 * 3. Smart Bulk Price Text Parser Class
 */
class BulkPriceParser {
    constructor(catalog, inputId, tableBodyId, statsId) {
        this.catalog = catalog || [];
        this.inputEl = document.getElementById(inputId);
        this.tbodyEl = document.getElementById(tableBodyId);
        this.statsEl = document.getElementById(statsId);

        if (this.inputEl) {
            this.inputEl.addEventListener('input', () => this.parseText());
            this.inputEl.addEventListener('paste', () => setTimeout(() => this.parseText(), 50));
        }

        // Initial parse if there is pre-filled content
        if (this.inputEl && this.inputEl.value.trim()) {
            this.parseText();
        }
    }

    parseText() {
        if (!this.inputEl || !this.tbodyEl) return;
        const text = this.inputEl.value.trim();

        if (!text) {
            this.tbodyEl.innerHTML = `
                <tr>
                    <td colspan="5" class="text-center" style="padding: 32px; color: var(--text-muted);">
                        <em>Paste wholesale list lines on the left to see live preview matching...</em>
                    </td>
                </tr>
            `;
            if (this.statsEl) this.statsEl.innerHTML = '';
            return;
        }

        const lines = text.split(/\r?\n/);
        const results = [];
        let matchedCount = 0;

        lines.forEach(line => {
            const cleanLine = line.trim();
            if (!cleanLine) return;

            let itemName = '';
            let priceVal = '';
            let unitVal = '';

            // 1. Colon or Dash/Hyphen separator (e.g. "Tomato big crates: 650", "Tomato local big crates - 550")
            const colonMatch = cleanLine.match(/^([^:\-=]+)[:\-=](.+)$/);
            if (colonMatch) {
                itemName = colonMatch[1].trim();
                priceVal = colonMatch[2].trim();
            } else {
                // 2. Space separated last token numeric/slash (e.g. "Cabbage local 600/500")
                const lastNumMatch = cleanLine.match(/^(.+?)\s+([0-9\/\.\-]+(?:\s*\(.*?\))?)$/);
                if (lastNumMatch) {
                    itemName = lastNumMatch[1].trim();
                    priceVal = lastNumMatch[2].trim();
                } else {
                    itemName = cleanLine;
                    priceVal = '';
                }
            }

            // Extract unit in brackets if present (e.g. "550/600 (box)")
            const bracketUnit = priceVal.match(/\((.*?)\)/);
            if (bracketUnit) {
                unitVal = bracketUnit[1].trim();
            }

            const match = this.findBestMatch(itemName);
            if (match) {
                matchedCount++;
                if (!unitVal) {
                    unitVal = match.default_unit || '';
                }
            }

            results.push({
                rawLine: cleanLine,
                extractedName: itemName,
                matched: match,
                price: priceVal,
                unit: unitVal
            });
        });

        this.renderTable(results);

        if (this.statsEl) {
            const matchPct = results.length ? Math.round((matchedCount / results.length) * 100) : 0;
            this.statsEl.innerHTML = `
                <div style="display: flex; gap: 12px; margin-top: 10px; font-size: 0.82rem; font-weight: 600; color: var(--text-main);">
                    <span>Total Lines: <strong>${results.length}</strong></span>
                    <span style="color: #059669;">Matched: <strong>${matchedCount} (${matchPct}%)</strong></span>
                    ${results.length - matchedCount > 0 ? `<span style="color: #d97706;">New/Unmatched: <strong>${results.length - matchedCount}</strong></span>` : ''}
                </div>
            `;
        }
    }

    findBestMatch(rawName) {
        if (!this.catalog || !this.catalog.length) return null;

        const clean = this.normalize(rawName);
        if (!clean) return null;

        // 1. Exact normalized match
        for (const p of this.catalog) {
            if (this.normalize(p.name) === clean) {
                return p;
            }
        }

        // 2. Starts with / Prefix match
        for (const p of this.catalog) {
            const pClean = this.normalize(p.name);
            if (pClean.startsWith(clean) || clean.startsWith(pClean)) {
                return p;
            }
        }

        // 3. Substring inclusion
        for (const p of this.catalog) {
            const pClean = this.normalize(p.name);
            if (pClean.includes(clean) || clean.includes(pClean)) {
                return p;
            }
        }

        // 4. Token overlap match (e.g. "Tomato big crates" matches "Tomato big crates (premium)")
        const words = clean.split(' ').filter(w => w.length > 1);
        let bestProd = null;
        let maxOverlap = 0;

        for (const p of this.catalog) {
            const pWords = this.normalize(p.name).split(' ');
            let overlap = 0;
            for (const w of words) {
                if (pWords.includes(w)) overlap++;
            }
            if (overlap > maxOverlap && overlap >= 2) {
                maxOverlap = overlap;
                bestProd = p;
            }
        }

        return bestProd;
    }

    normalize(str) {
        return (str || '')
            .toLowerCase()
            .replace(/[\(\)\[\]\-\_\:\,\.\/\+]/g, ' ')
            .replace(/\s+/g, ' ')
            .trim();
    }

    renderTable(results) {
        if (!this.tbodyEl) return;
        this.tbodyEl.innerHTML = '';

        results.forEach((item, idx) => {
            const tr = document.createElement('tr');
            const isMatched = !!item.matched;
            const prodId = isMatched ? item.matched.id : 0;
            const prodName = isMatched ? item.matched.name : '';
            const catInfo = isMatched ? `${item.matched.category_name || ''}` : 'New Product (Will be created)';

            tr.innerHTML = `
                <td style="text-align: center; vertical-align: middle;">
                    <input type="checkbox" name="import_items[${idx}][include]" value="1" checked>
                </td>
                <td style="vertical-align: middle;">
                    <strong>${this.escape(item.extractedName)}</strong>
                    <input type="hidden" name="import_items[${idx}][raw_name]" value="${this.escape(item.extractedName)}">
                </td>
                <td style="vertical-align: middle;">
                    ${isMatched 
                        ? `<div style="font-weight: 600; color: #047857;">${this.escape(prodName)}</div>
                           <div style="font-size: 0.75rem; color: #64748b;">${this.escape(catInfo)}</div>
                           <input type="hidden" name="import_items[${idx}][product_id]" value="${prodId}">`
                        : `<div style="font-size: 0.85rem; color: #d97706; font-weight: 600;">➕ Auto-create as new item</div>
                           <input type="hidden" name="import_items[${idx}][product_id]" value="0">`
                    }
                </td>
                <td style="vertical-align: middle;">
                    <input type="text" name="import_items[${idx}][price]" value="${this.escape(item.price)}" class="form-control form-control-sm" style="max-width: 140px; font-weight: 700;" required>
                </td>
                <td style="vertical-align: middle;">
                    <input type="text" name="import_items[${idx}][unit]" value="${this.escape(item.unit)}" class="form-control form-control-sm" style="max-width: 120px;" placeholder="e.g. kg, crate">
                </td>
            `;

            this.tbodyEl.appendChild(tr);
        });
    }

    escape(str) {
        if (!str) return '';
        return String(str)
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }
}
