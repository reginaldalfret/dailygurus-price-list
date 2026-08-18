import { Product } from './types';

export interface ParsedPriceItem {
  rawLine: string;
  extractedName: string;
  price: string;
  unit: string;
  priceNotes?: string;
  matchedProduct: Product | null;
  confidence: 'exact' | 'high' | 'medium' | 'low' | 'none';
  similarity: number;
  isNewProduct: boolean;
}

export interface ParseResult {
  items: ParsedPriceItem[];
  totalLines: number;
  matchedCount: number;
  unmatchedCount: number;
}

/**
 * Clean and normalize a string for fuzzy comparisons
 */
export function normalizeString(str: string): string {
  if (!str) return '';
  return str
    .toLowerCase()
    .replace(/[^\w\s\u0B80-\u0BFF]/g, ' ') // Keep alphanumeric, Tamil unicode characters
    .replace(/\s+/g, ' ')
    .trim();
}

/**
 * Calculate Bigram / Dice similarity coefficient between two strings (0.0 to 1.0)
 */
export function calculateDiceSimilarity(s1: string, s2: string): number {
  const norm1 = normalizeString(s1);
  const norm2 = normalizeString(s2);

  if (!norm1 || !norm2) return 0;
  if (norm1 === norm2) return 1.0;
  if (norm1.length < 2 || norm2.length < 2) {
    return norm1 === norm2 ? 1.0 : 0;
  }

  const getBigrams = (str: string): Map<string, number> => {
    const bigrams = new Map<string, number>();
    for (let i = 0; i < str.length - 1; i++) {
      const bigram = str.substring(i, i + 2);
      bigrams.set(bigram, (bigrams.get(bigram) || 0) + 1);
    }
    return bigrams;
  };

  const bigrams1 = getBigrams(norm1);
  const bigrams2 = getBigrams(norm2);

  let intersection = 0;
  for (const [bigram, count1] of bigrams1.entries()) {
    if (bigrams2.has(bigram)) {
      intersection += Math.min(count1, bigrams2.get(bigram)!);
    }
  }

  const total = (norm1.length - 1) + (norm2.length - 1);
  return total > 0 ? (2.0 * intersection) / total : 0;
}

/**
 * Find the closest matching product from catalog
 */
export function matchProduct(extractedName: string, catalog: Product[]): {
  product: Product | null;
  confidence: 'exact' | 'high' | 'medium' | 'low' | 'none';
  similarity: number;
} {
  if (!extractedName || !catalog || catalog.length === 0) {
    return { product: null, confidence: 'none', similarity: 0 };
  }

  const cleanQuery = normalizeString(extractedName);
  if (!cleanQuery) {
    return { product: null, confidence: 'none', similarity: 0 };
  }

  // 1. Exact normalized match (English or Tamil name)
  for (const p of catalog) {
    const pNameNorm = normalizeString(p.name);
    const pTamilNorm = normalizeString(p.tamil_name || '');
    if (pNameNorm === cleanQuery || (pTamilNorm && pTamilNorm === cleanQuery)) {
      return { product: p, confidence: 'exact', similarity: 1.0 };
    }
  }

  // 2. Prefix / Starts With match
  for (const p of catalog) {
    const pNameNorm = normalizeString(p.name);
    if (pNameNorm.startsWith(cleanQuery) || cleanQuery.startsWith(pNameNorm)) {
      return { product: p, confidence: 'high', similarity: 0.9 };
    }
  }

  // 3. Substring inclusion match
  for (const p of catalog) {
    const pNameNorm = normalizeString(p.name);
    if (pNameNorm.includes(cleanQuery) || cleanQuery.includes(pNameNorm)) {
      return { product: p, confidence: 'high', similarity: 0.85 };
    }
  }

  // 4. Token / Word overlap matching with Dice similarity
  const queryTokens = new Set(cleanQuery.split(' ').filter(w => w.length > 1));
  let bestMatch: Product | null = null;
  let highestScore = 0;

  for (const p of catalog) {
    const pNameNorm = normalizeString(p.name);
    const pTokens = pNameNorm.split(' ').filter(w => w.length > 1);

    let overlapCount = 0;
    for (const t of pTokens) {
      if (queryTokens.has(t)) {
        overlapCount++;
      }
    }

    const tokenOverlapScore = pTokens.length > 0 ? overlapCount / Math.max(pTokens.length, queryTokens.size) : 0;
    const diceScore = calculateDiceSimilarity(cleanQuery, pNameNorm);
    const combinedScore = (tokenOverlapScore * 0.6) + (diceScore * 0.4);

    if (combinedScore > highestScore) {
      highestScore = combinedScore;
      bestMatch = p;
    }
  }

  if (highestScore >= 0.70 && bestMatch) {
    return { product: bestMatch, confidence: 'high', similarity: highestScore };
  } else if (highestScore >= 0.50 && bestMatch) {
    return { product: bestMatch, confidence: 'medium', similarity: highestScore };
  } else if (highestScore >= 0.35 && bestMatch) {
    return { product: bestMatch, confidence: 'low', similarity: highestScore };
  }

  return { product: null, confidence: 'none', similarity: highestScore };
}

/**
 * Parse a raw WhatsApp or text price list against the product catalog
 */
export function parseWhatsAppPriceList(rawText: string, catalog: Product[]): ParseResult {
  if (!rawText || !rawText.trim()) {
    return { items: [], totalLines: 0, matchedCount: 0, unmatchedCount: 0 };
  }

  const lines = rawText.split(/\r?\n/);
  const items: ParsedPriceItem[] = [];
  let matchedCount = 0;

  for (const line of lines) {
    const cleanLine = line.trim();
    // Skip empty lines or divider header lines like "=== DAILY PRICES ===" or "***"
    if (!cleanLine || /^[\=\-\*\#\_]{3,}$/.test(cleanLine)) {
      continue;
    }

    // Strip leading list bullet markers (e.g. "1.", "1)", "*", "-", "•")
    const sanitizedLine = cleanLine.replace(/^(\d+[\.\)]|\*|\-|\•)\s+/, '');

    let itemName = '';
    let pricePart = '';
    let unit = '';
    let notes = '';

    // Regex 1: Split by colon, dash, or equals sign (e.g. "Tomato big crates( premium) : 650", "Nashik new - Big (60+) : 1900/1950")
    // Notice we look for separator where price usually starts with digits, Rs, ₹, Nill, etc.
    const delimiterMatch = sanitizedLine.match(/^([^:\-=]+)[:\-=](.+)$/);

    if (delimiterMatch) {
      itemName = delimiterMatch[1].trim();
      pricePart = delimiterMatch[2].trim();
    } else {
      // Regex 2: Space separated price at the end (e.g. "Cabbage local 600/500", "Cauliflower 180")
      const trailingNumberMatch = sanitizedLine.match(/^(.+?)\s+((?:(?:rs\.?|₹)\s*)?[0-9\/\.\-]+(?:\s*\(.*?\))?|[Nn]ill|[Nn]il|\—|\-)$/i);
      if (trailingNumberMatch) {
        itemName = trailingNumberMatch[1].trim();
        pricePart = trailingNumberMatch[2].trim();
      } else {
        // Fallback: entire line is item name with no price yet
        itemName = sanitizedLine;
        pricePart = '';
      }
    }

    // Clean up currency symbols from price
    let cleanPrice = pricePart
      .replace(/^(rs\.?|inr|₹)\s*/i, '')
      .replace(/\/\s*(rs\.?|inr|₹)\s*/i, '/')
      .trim();

    // Extract unit inside parentheses from price or item name (e.g. "1300 (20kg box)", "550 (crate)")
    const bracketUnitMatch = cleanPrice.match(/\((.*?)\)/) || itemName.match(/\(([^0-9\+\(\)]+?)\)$/);
    if (bracketUnitMatch) {
      unit = bracketUnitMatch[1].trim();
      // Remove bracket part from cleanPrice if it was extracted from price
      cleanPrice = cleanPrice.replace(/\(.*?\)/, '').trim();
    }

    // Check if there are notes (e.g. "1900/1950 - Good quality")
    const notesMatch = cleanPrice.match(/[\-\–\—\,]\s*([a-zA-Z\s]+)$/);
    if (notesMatch && isNaN(Number(notesMatch[1].trim()))) {
      notes = notesMatch[1].trim();
      cleanPrice = cleanPrice.replace(/[\-\–\—\,]\s*[a-zA-Z\s]+$/, '').trim();
    }

    // Perform Catalog Fuzzy Matching
    const matchResult = matchProduct(itemName, catalog);
    const matched = matchResult.product;

    if (matched) {
      matchedCount++;
      if (!unit && matched.default_unit) {
        unit = matched.default_unit;
      }
    }

    items.push({
      rawLine: cleanLine,
      extractedName: itemName,
      price: cleanPrice,
      unit: unit || (matched?.default_unit || 'kg'),
      priceNotes: notes,
      matchedProduct: matched,
      confidence: matchResult.confidence,
      similarity: matchResult.similarity,
      isNewProduct: !matched,
    });
  }

  return {
    items,
    totalLines: items.length,
    matchedCount,
    unmatchedCount: items.length - matchedCount,
  };
}
