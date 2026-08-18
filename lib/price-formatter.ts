/**
 * DailyGurus Price List - Precise Price Formatter
 * Preserves exact price semantics (₹650, ₹1,900 / ₹1,950, Nill, —)
 */

export function formatSinglePrice(val: string): string {
  val = val.trim();
  if (!val) return '';
  if (val.toLowerCase() === 'nill' || val.toLowerCase() === 'nil') return 'Nill';
  if (val.toLowerCase() === 'na' || val.toLowerCase() === 'n/a' || val === '-') return '—';

  // If numeric, format with Indian numbering comma system
  const num = parseFloat(val.replace(/,/g, ''));
  if (!isNaN(num) && /^[0-9]+(\.[0-9]+)?$/.test(val.replace(/,/g, ''))) {
    return '₹' + num.toLocaleString('en-IN');
  }

  // If already starts with currency symbol
  if (val.startsWith('₹') || val.startsWith('Rs')) return val;

  return '₹' + val;
}

export function formatPriceString(priceStr: string | null | undefined): string {
  if (!priceStr || !priceStr.trim()) {
    return '—';
  }

  const clean = priceStr.trim();
  if (clean.toLowerCase() === 'nill' || clean.toLowerCase() === 'nil') return 'Nill';
  if (clean.toLowerCase() === 'na' || clean.toLowerCase() === 'n/a' || clean === '-') return '—';

  // Handle slash-separated prices (e.g. "1900/1950", "40 35", "90/80")
  if (clean.includes('/')) {
    const parts = clean.split('/');
    return parts.map(p => formatSinglePrice(p)).join(' / ');
  }

  // Handle space separated dual prices like "40 35"
  if (/^\d+\s+\d+$/.test(clean)) {
    const parts = clean.split(/\s+/);
    return parts.map(p => formatSinglePrice(p)).join(' / ');
  }

  return formatSinglePrice(clean);
}

export function formatDateLong(dateStr: string): string {
  if (!dateStr) return '';
  try {
    const [y, m, d] = dateStr.split('-').map(Number);
    const date = new Date(Date.UTC(y, m - 1, d));
    return date.toLocaleDateString('en-GB', {
      day: 'numeric',
      month: 'long',
      year: 'numeric',
      timeZone: 'UTC'
    });
  } catch (e) {
    return dateStr;
  }
}

export function formatDateShort(dateStr: string): string {
  if (!dateStr) return '';
  try {
    const [y, m, d] = dateStr.split('-').map(Number);
    const date = new Date(Date.UTC(y, m - 1, d));
    return date.toLocaleDateString('en-GB', {
      day: '2-digit',
      month: '2-digit',
      year: 'numeric',
      timeZone: 'UTC'
    });
  } catch (e) {
    return dateStr;
  }
}
