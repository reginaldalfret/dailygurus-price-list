export interface Category {
  id: number;
  name: string;
  slug: string;
  icon: string;
  category_type: 'veg' | 'fruit';
  display_order: number;
  active: number;
  subcategories?: Subcategory[];
}

export interface Subcategory {
  id: number;
  category_id: number;
  name: string;
  slug: string;
  icon: string;
  display_order: number;
  active: number;
  products?: ProductPriceItem[];
}

export interface Product {
  id: number;
  category_id: number;
  subcategory_id: number | null;
  name: string;
  tamil_name: string;
  image_url: string;
  icon: string;
  default_unit: string;
  display_order: number;
  active: number;
}

export interface ProductPriceItem extends Product {
  price: string;
  price_unit: string;
  price_notes?: string;
}

export interface PriceDateInfo {
  price_date: string;
  is_published: number;
  notes: string;
}

export interface CategorizedData {
  vegetables: {
    category: Category;
    subcategories: Subcategory[];
  };
  fruits: {
    category: Category;
    subcategories: Subcategory[];
  };
}

export interface PriceStats {
  veg_count: number;
  fruit_count: number;
  total_items: number;
}
