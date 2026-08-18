import React from 'react';

// Produce icon mapping
export const PRODUCE_SVG_MAP: Record<string, string> = {
  'banana-flower': `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 64"><path d="M24 16 C16 28 20 48 32 56 C44 48 48 28 40 16 Z" fill="#6A1B9A"/><path d="M32 16 L32 8" stroke="#2E7D32" stroke-width="3" stroke-linecap="round"/></svg>`,
  'banana': `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 64"><path d="M14 26 C22 14 44 18 52 38 C42 46 26 42 14 26 Z" fill="#FDD835"/><path d="M12 26 L10 24" stroke="#5D4037" stroke-width="3" stroke-linecap="round"/></svg>`,
  'beans': `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 64"><path d="M18 12 C14 24 18 42 32 52 C36 44 34 26 24 12 Z" fill="#43A047"/><path d="M28 14 C26 28 32 44 44 50 C46 40 42 24 34 14 Z" fill="#2E7D32"/></svg>`,
  'beetroot': `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 64"><circle cx="32" cy="38" r="20" fill="#880E4F"/><path d="M32 58 L32 62" stroke="#880E4F" stroke-width="3"/><path d="M32 18 C28 8 20 6 18 6 C20 12 26 16 32 18 Z" fill="#2E7D32"/><path d="M32 18 C36 8 44 6 46 6 C44 12 38 16 32 18 Z" fill="#43A047"/></svg>`,
  'brinjal': `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 64"><path d="M22 28 C16 42 22 58 34 58 C46 58 50 44 44 28 C40 22 30 20 22 28 Z" fill="#4A148C"/><path d="M30 22 C32 14 36 8 40 6" stroke="#2E7D32" stroke-width="3" fill="none" stroke-linecap="round"/><path d="M22 24 C26 22 38 22 42 24 C38 26 34 28 32 30 C30 28 26 26 22 24 Z" fill="#43A047"/></svg>`,
  'cabbage': `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 64"><circle cx="32" cy="34" r="22" fill="#81C784"/><path d="M16 26 C22 16 42 16 48 26 C40 34 24 34 16 26 Z" fill="#A5D6A7"/><path d="M20 44 C26 50 38 50 44 44 C38 38 26 38 20 44 Z" fill="#66BB6A"/><path d="M12 34 C16 42 24 46 32 46 C24 38 20 30 12 34 Z" fill="#4CAF50"/><path d="M52 34 C48 42 40 46 32 46 C40 38 44 30 52 34 Z" fill="#4CAF50"/></svg>`,
  'capsicum': `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 64"><path d="M18 24 C14 36 16 52 28 56 C34 56 36 50 36 50 C36 50 38 56 44 56 C54 52 56 36 52 24 C48 18 22 18 18 24 Z" fill="#2E7D32"/><path d="M32 18 L32 8 C32 6 36 6 36 6" stroke="#1B5E20" stroke-width="3" fill="none" stroke-linecap="round"/><ellipse cx="26" cy="36" rx="4" ry="12" fill="#43A047"/></svg>`,
  'carrot': `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 64"><path d="M22 18 C28 16 42 24 48 28 C36 42 22 56 16 58 C16 52 18 36 22 18 Z" fill="#EF6C00"/><path d="M44 24 C48 16 54 12 58 10 C54 16 50 22 46 26 Z" fill="#2E7D32"/><path d="M42 20 C48 18 56 18 60 16 C54 22 48 24 44 24 Z" fill="#43A047"/></svg>`,
  'cauliflower': `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 64"><path d="M14 44 C12 32 20 20 28 16 C22 26 22 40 24 48 Z" fill="#2E7D32"/><path d="M50 44 C52 32 44 20 36 16 C42 26 42 40 40 48 Z" fill="#2E7D32"/><circle cx="32" cy="28" r="14" fill="#F5F5F5"/><circle cx="24" cy="30" r="10" fill="#EEEEEE"/><circle cx="40" cy="30" r="10" fill="#EEEEEE"/><circle cx="32" cy="38" r="11" fill="#E0E0E0"/></svg>`,
  'chilli': `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 64"><path d="M48 14 C36 16 22 28 16 44 C14 50 16 54 20 54 C26 54 36 40 44 28 C48 22 50 16 48 14 Z" fill="#C62828"/><path d="M48 14 C52 10 56 8 58 6" stroke="#2E7D32" stroke-width="3" fill="none" stroke-linecap="round"/></svg>`,
  'coconut': `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 64"><circle cx="32" cy="34" r="22" fill="#5D4037"/><circle cx="32" cy="34" r="18" fill="#8D6E63"/><circle cx="32" cy="34" r="14" fill="#FFFFFF"/><circle cx="26" cy="28" r="3" fill="#4E342E"/><circle cx="38" cy="28" r="3" fill="#4E342E"/><circle cx="32" cy="38" r="3" fill="#4E342E"/></svg>`,
  'corn': `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 64"><path d="M20 48 C16 34 26 18 38 12 C44 24 38 44 26 54 Z" fill="#FDD835"/><path d="M14 52 C18 42 22 36 22 36 C18 44 16 50 14 52 Z" fill="#43A047"/><path d="M16 56 C24 52 32 50 42 52 C32 56 22 58 16 56 Z" fill="#2E7D32"/></svg>`,
  'cucumber': `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 64"><path d="M14 46 C10 38 16 22 30 16 C44 10 54 18 52 28 C50 38 42 50 28 54 C18 56 14 50 14 46 Z" fill="#388E3C"/><ellipse cx="32" cy="34" rx="4" ry="14" transform="rotate(-30 32 34)" fill="#4CAF50"/></svg>`,
  'curry-leaves': `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 64"><path d="M12 52 Q32 32 52 12" stroke="#2E7D32" stroke-width="2.5" fill="none"/><path d="M22 42 C16 40 16 34 22 34 C26 34 26 40 22 42 Z" fill="#43A047"/><path d="M32 32 C26 30 26 24 32 24 C36 24 36 30 32 32 Z" fill="#43A047"/><path d="M42 22 C36 20 36 14 42 14 C46 14 46 20 42 22 Z" fill="#43A047"/><path d="M30 46 C34 46 38 42 36 38 C32 38 28 42 30 46 Z" fill="#388E3C"/><path d="M40 36 C44 36 48 32 46 28 C42 28 38 32 40 36 Z" fill="#388E3C"/></svg>`,
  'custard-apple': `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 64"><circle cx="32" cy="36" r="20" fill="#388E3C"/><circle cx="24" cy="28" r="6" fill="#4CAF50"/><circle cx="36" cy="26" r="6" fill="#4CAF50"/><circle cx="26" cy="40" r="6" fill="#4CAF50"/><circle cx="38" cy="38" r="6" fill="#4CAF50"/><circle cx="32" cy="48" r="5" fill="#4CAF50"/></svg>`,
  'drumstick': `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 64"><path d="M12 54 L52 10" stroke="#2E7D32" stroke-width="6" stroke-linecap="round"/><path d="M18 48 L22 44 M28 38 L32 34 M38 28 L42 24 M46 20 L50 16" stroke="#1B5E20" stroke-width="3"/></svg>`,
  'garlic': `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 64"><path d="M20 42 C16 32 24 22 32 18 C40 22 48 32 44 42 C42 50 36 54 32 54 C28 54 22 50 20 42 Z" fill="#F5F5F5"/><path d="M32 18 L32 10" stroke="#A1887F" stroke-width="3" stroke-linecap="round"/><path d="M26 26 C24 34 26 44 30 52" stroke="#E0E0E0" stroke-width="2" fill="none"/><path d="M38 26 C40 34 38 44 34 52" stroke="#E0E0E0" stroke-width="2" fill="none"/></svg>`,
  'generic-fruit': `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 64"><circle cx="32" cy="36" r="22" fill="#E65100"/><circle cx="26" cy="28" r="6" fill="#FF9800"/><path d="M32 14 L36 6" stroke="#2E7D32" stroke-width="3" stroke-linecap="round"/></svg>`,
  'generic-veg': `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 64"><circle cx="32" cy="36" r="22" fill="#2E7D32"/><ellipse cx="26" cy="28" rx="6" ry="10" fill="#4CAF50"/><path d="M32 14 L32 6" stroke="#1B5E20" stroke-width="3" stroke-linecap="round"/></svg>`,
  'ginger': `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 64"><path d="M18 36 C14 28 20 20 28 22 C32 16 42 16 46 22 C52 24 54 32 50 38 C46 44 44 50 38 52 C28 54 22 48 18 36 Z" fill="#D7CCC8"/><circle cx="30" cy="28" r="4" fill="#BCAAA4"/><circle cx="42" cy="30" r="4" fill="#BCAAA4"/></svg>`,
  'gourd': `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 64"><path d="M28 14 C26 24 20 38 20 46 C20 54 26 58 32 58 C38 58 44 54 44 46 C44 38 38 24 36 14 Z" fill="#66BB6A"/><path d="M32 14 L32 6" stroke="#2E7D32" stroke-width="3" stroke-linecap="round"/></svg>`,
  'guava': `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 64"><circle cx="32" cy="36" r="20" fill="#689F38"/><circle cx="32" cy="36" r="15" fill="#C8E6C9"/><circle cx="32" cy="36" r="11" fill="#FF8A80"/><circle cx="30" cy="34" r="1.5" fill="#5D4037"/><circle cx="35" cy="36" r="1.5" fill="#5D4037"/></svg>`,
  'keerai': `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 64"><path d="M32 58 L32 20" stroke="#2E7D32" stroke-width="3"/><path d="M32 20 C18 12 14 30 32 40 C50 30 46 12 32 20 Z" fill="#43A047"/><path d="M32 30 C22 24 20 36 32 44 C44 36 42 24 32 30 Z" fill="#66BB6A"/></svg>`,
  'ladies-finger': `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 64"><path d="M28 14 L36 14 L40 48 L32 58 L24 48 Z" fill="#43A047"/><path d="M32 14 L32 6" stroke="#2E7D32" stroke-width="3" stroke-linecap="round"/><line x1="32" y1="18" x2="32" y2="52" stroke="#2E7D32" stroke-width="1.5"/></svg>`,
  'lemon': `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 64"><ellipse cx="32" cy="34" rx="22" ry="18" fill="#FDD835"/><ellipse cx="32" cy="34" rx="18" ry="14" fill="#FFEE58"/><path d="M10 34 L8 34 M54 34 L56 34" stroke="#FBC02D" stroke-width="3" stroke-linecap="round"/></svg>`,
  'mango': `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 64"><path d="M34 14 C48 14 54 28 50 42 C46 54 32 58 24 50 C16 42 18 24 28 16 C30 14 32 14 34 14 Z" fill="#FB8C00"/><path d="M34 14 C36 8 40 6 42 6" stroke="#2E7D32" stroke-width="3" fill="none" stroke-linecap="round"/></svg>`,
  'melon': `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 64"><ellipse cx="32" cy="34" rx="22" ry="18" fill="#FFB74D"/><path d="M12 34 Q32 20 52 34" stroke="#FFA726" stroke-width="2" fill="none"/><path d="M14 40 Q32 26 50 40" stroke="#FFA726" stroke-width="2" fill="none"/></svg>`,
  'mosambi': `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 64"><circle cx="32" cy="34" r="22" fill="#7CB342"/><circle cx="32" cy="34" r="18" fill="#C0CA33"/><circle cx="32" cy="34" r="2" fill="#558B2F"/></svg>`,
  'onion': `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 64"><path d="M32 10 C20 18 12 30 12 42 C12 54 21 60 32 60 C43 60 52 54 52 42 C52 30 44 18 32 10 Z" fill="#AB47BC"/><path d="M32 10 C24 22 20 34 20 44 C20 54 26 58 32 58" fill="none" stroke="#CE93D8" stroke-width="2.5"/><path d="M32 10 L32 5 C32 3 30 2 29 2" stroke="#66BB6A" stroke-width="3" fill="none" stroke-linecap="round"/></svg>`,
  'orange': `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 64"><circle cx="32" cy="36" r="22" fill="#FB8C00"/><circle cx="32" cy="36" r="18" fill="#FFA726"/><path d="M32 14 L34 8" stroke="#2E7D32" stroke-width="3" stroke-linecap="round"/></svg>`,
  'papaya': `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 64"><path d="M32 12 C42 12 50 24 48 40 C46 52 38 58 32 58 C26 58 18 52 16 40 C14 24 22 12 32 12 Z" fill="#FFA726"/><ellipse cx="32" cy="38" rx="8" ry="12" fill="#E65100"/><circle cx="32" cy="36" r="2" fill="#212121"/></svg>`,
  'peas': `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 64"><path d="M12 26 C24 16 44 18 52 36 C40 48 20 44 12 26 Z" fill="#43A047"/><circle cx="24" cy="30" r="4" fill="#81C784"/><circle cx="34" cy="32" r="4" fill="#81C784"/><circle cx="44" cy="34" r="4" fill="#81C784"/></svg>`,
  'pineapple': `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 64"><ellipse cx="32" cy="40" rx="16" ry="20" fill="#FFA000"/><path d="M32 20 C28 8 20 6 18 6 C24 12 28 16 32 20 Z" fill="#2E7D32"/><path d="M32 20 C36 8 44 6 46 6 C40 12 36 16 32 20 Z" fill="#43A047"/></svg>`,
  'potato': `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 64"><path d="M16 34 C14 22 24 14 36 14 C48 14 54 24 52 38 C50 48 40 54 28 52 C18 50 16 42 16 34 Z" fill="#D7CCC8"/><circle cx="28" cy="26" r="2" fill="#A1887F"/><circle cx="40" cy="34" r="2" fill="#A1887F"/><circle cx="30" cy="42" r="2" fill="#A1887F"/><circle cx="44" cy="24" r="1.5" fill="#A1887F"/></svg>`,
  'pumpkin': `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 64"><ellipse cx="32" cy="36" rx="22" ry="18" fill="#F57C00"/><ellipse cx="32" cy="36" rx="14" ry="18" fill="#FB8C00"/><ellipse cx="32" cy="36" rx="6" ry="18" fill="#FFA726"/><path d="M32 18 L32 10" stroke="#2E7D32" stroke-width="4" stroke-linecap="round"/></svg>`,
  'radish': `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 64"><path d="M32 18 C24 24 22 42 32 58 C42 42 40 24 32 18 Z" fill="#FFFFFF"/><path d="M32 18 C28 8 20 6 18 6 C20 12 26 16 32 18 Z" fill="#2E7D32"/><path d="M32 18 C36 8 44 6 46 6 C44 12 38 16 32 18 Z" fill="#43A047"/></svg>`,
  'red-banana': `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 64"><path d="M14 26 C22 14 44 18 52 38 C42 46 26 42 14 26 Z" fill="#C62828"/><path d="M12 26 L10 24" stroke="#5D4037" stroke-width="3" stroke-linecap="round"/></svg>`,
  'sambar-onion': `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 64"><circle cx="32" cy="36" r="16" fill="#D81B60"/><circle cx="32" cy="36" r="12" fill="#E91E63"/><path d="M32 20 L32 12" stroke="#4CAF50" stroke-width="3" stroke-linecap="round"/></svg>`,
  'sapota': `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 64"><ellipse cx="32" cy="34" rx="18" ry="22" fill="#8D6E63"/><circle cx="32" cy="34" r="3" fill="#3E2723"/></svg>`,
  'tomato': `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 64"><circle cx="32" cy="36" r="24" fill="#E53935"/><ellipse cx="26" cy="28" rx="8" ry="12" fill="#EF5350"/><path d="M32 14c-2 6-8 8-12 8 6-2 10-6 12-8z M32 14c2 6 8 8 12 8-6-2-10-6-12-8z M32 14c-1-5 2-8 3-10-1 4 0 7-3 10z" fill="#43A047"/></svg>`,
  'watermelon': `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 64"><path d="M10 24 C14 46 32 56 54 52 C50 30 32 20 10 24 Z" fill="#2E7D32"/><path d="M14 26 C18 44 32 52 50 48 C46 32 32 22 14 26 Z" fill="#C62828"/><circle cx="28" cy="34" r="1.5" fill="#212121"/><circle cx="36" cy="38" r="1.5" fill="#212121"/><circle cx="42" cy="42" r="1.5" fill="#212121"/></svg>`,
};

interface ProduceIconProps {
  iconName?: string;
  imageUrl?: string;
  name: string;
  size?: number;
  className?: string;
}

export const ProduceThumbnail: React.FC<ProduceIconProps> = ({
  iconName,
  imageUrl,
  name,
  size = 36,
  className = 'produce-thumbnail',
}) => {
  // Normalize key
  let cleanKey = (iconName || '')
    .replace(/\.svg$/i, '')
    .replace(/^assets\/images\/produce\//i, '')
    .trim();

  // If no iconName but name is given
  if (!cleanKey && name) {
    cleanKey = name.toLowerCase().replace(/\s+/g, '-');
  }

  // Check SVG Map
  const svgContent = PRODUCE_SVG_MAP[cleanKey] || 
    (cleanKey.includes('fruit') ? PRODUCE_SVG_MAP['generic-fruit'] : PRODUCE_SVG_MAP['generic-veg']);

  if (svgContent) {
    return (
      <span
        className={className}
        style={{
          display: 'inline-flex',
          alignItems: 'center',
          justifyContent: 'center',
          width: size,
          height: size,
          minWidth: size,
          minHeight: size,
        }}
        dangerouslySetInnerHTML={{ __html: svgContent }}
        title={name}
        aria-label={name}
      />
    );
  }

  // Fallback img tag
  const src = imageUrl || `/assets/images/produce/${cleanKey || 'generic-veg'}.svg`;
  return (
    <img
      src={src}
      alt={name}
      className={className}
      width={size}
      height={size}
      loading="lazy"
    />
  );
};
