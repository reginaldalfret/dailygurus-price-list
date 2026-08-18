import { NextRequest, NextResponse } from 'next/server';
import { supabaseAdmin } from '@/lib/supabase/admin';
import { Category, Subcategory, Product, ProductPriceItem, CategorizedData, PriceStats } from '@/lib/types';

export const dynamic = 'force-dynamic';

export async function GET(request: NextRequest) {
  try {
    const { searchParams } = new URL(request.url);
    let requestedDate = searchParams.get('date');

    // 1. If date is not provided, fetch the most recent published date
    if (!requestedDate) {
      const { data: latestDateData } = await supabaseAdmin
        .from('price_dates')
        .select('price_date')
        .eq('is_published', 1)
        .order('price_date', { ascending: false })
        .limit(1)
        .single();

      if (latestDateData && latestDateData.price_date) {
        requestedDate = latestDateData.price_date;
      } else {
        requestedDate = new Date().toISOString().split('T')[0];
      }
    }

    // 2. Fetch price date record
    const { data: priceDateInfo } = await supabaseAdmin
      .from('price_dates')
      .select('price_date, is_published, notes')
      .eq('price_date', requestedDate)
      .single();

    // 3. Fetch all active categories & subcategories
    const { data: categoriesData, error: catError } = await supabaseAdmin
      .from('categories')
      .select('*')
      .eq('active', 1)
      .order('display_order', { ascending: true });

    if (catError) {
      throw catError;
    }

    const { data: subcategoriesData, error: subError } = await supabaseAdmin
      .from('subcategories')
      .select('*')
      .eq('active', 1)
      .order('display_order', { ascending: true });

    if (subError) {
      throw subError;
    }

    // 4. Fetch all active products
    const { data: productsData, error: prodError } = await supabaseAdmin
      .from('products')
      .select('*')
      .eq('active', 1)
      .order('display_order', { ascending: true });

    if (prodError) {
      throw prodError;
    }

    // 5. Fetch daily prices for the requested date
    const { data: pricesData } = await supabaseAdmin
      .from('daily_prices')
      .select('*')
      .eq('price_date', requestedDate);

    // Map prices by product_id for fast O(1) lookup
    const priceMap = new Map<number, { price: string; unit: string; price_notes?: string }>();
    if (pricesData) {
      for (const p of pricesData) {
        priceMap.set(Number(p.product_id), {
          price: p.price || '',
          unit: p.unit || '',
          price_notes: p.price_notes || '',
        });
      }
    }

    // 6. Structure categorized data
    const vegCategory = categoriesData?.find(c => c.category_type === 'veg') || {
      id: 1,
      name: 'Vegetables',
      slug: 'vegetables',
      icon: '🥦',
      category_type: 'veg',
      display_order: 1,
      active: 1,
    };

    const fruitCategory = categoriesData?.find(c => c.category_type === 'fruit') || {
      id: 2,
      name: 'Fruits',
      slug: 'fruits',
      icon: '🍎',
      category_type: 'fruit',
      display_order: 2,
      active: 1,
    };

    let vegCount = 0;
    let fruitCount = 0;

    const buildSubcategoryTree = (catId: number, isVeg: boolean) => {
      const subs = (subcategoriesData || []).filter(s => s.category_id === catId);
      
      return subs.map(sub => {
        const prods = (productsData || [])
          .filter(p => p.subcategory_id === sub.id)
          .map(prod => {
            const pInfo = priceMap.get(Number(prod.id));
            const item: ProductPriceItem = {
              ...prod,
              price: pInfo?.price || '',
              price_unit: pInfo?.unit || prod.default_unit || 'kg',
              price_notes: pInfo?.price_notes || '',
            };

            if (item.price && item.price !== '—' && item.price.toLowerCase() !== 'nill') {
              if (isVeg) vegCount++;
              else fruitCount++;
            }

            return item;
          });

        return {
          ...sub,
          products: prods,
        };
      });
    };

    const categorized: CategorizedData = {
      vegetables: {
        category: vegCategory,
        subcategories: buildSubcategoryTree(vegCategory.id, true),
      },
      fruits: {
        category: fruitCategory,
        subcategories: buildSubcategoryTree(fruitCategory.id, false),
      },
    };

    // 7. Fetch list of available published dates for date navigation
    const { data: availableDatesData } = await supabaseAdmin
      .from('price_dates')
      .select('price_date, is_published')
      .order('price_date', { ascending: false })
      .limit(30);

    const availableDates = availableDatesData || [];

    const stats: PriceStats = {
      veg_count: vegCount,
      fruit_count: fruitCount,
      total_items: vegCount + fruitCount,
    };

    return NextResponse.json({
      success: true,
      date: requestedDate,
      priceDateInfo: priceDateInfo || {
        price_date: requestedDate,
        is_published: 0,
        notes: '',
      },
      data: categorized,
      stats,
      availableDates,
    });
  } catch (error: any) {
    console.error('Error fetching prices:', error);
    return NextResponse.json(
      { success: false, error: error.message || 'Failed to fetch prices' },
      { status: 500 }
    );
  }
}
