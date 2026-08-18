import { NextRequest, NextResponse } from 'next/server';
import { supabaseAdmin } from '@/lib/supabase/admin';
import { getAdminSessionFromRequest } from '@/lib/auth';

export const dynamic = 'force-dynamic';

export async function GET(request: NextRequest) {
  try {
    const session = getAdminSessionFromRequest(request);
    if (!session) {
      return NextResponse.json({ success: false, error: 'Unauthorized' }, { status: 401 });
    }

    const { searchParams } = new URL(request.url);
    const date = searchParams.get('date') || new Date().toISOString().split('T')[0];
    const copyFromDate = searchParams.get('copy_from_date');

    // 1. Fetch categories and subcategories
    const { data: categories } = await supabaseAdmin
      .from('categories')
      .select('*')
      .order('display_order', { ascending: true });

    const { data: subcategories } = await supabaseAdmin
      .from('subcategories')
      .select('*')
      .order('display_order', { ascending: true });

    // 2. Fetch all products
    const { data: products, error: prodErr } = await supabaseAdmin
      .from('products')
      .select('*')
      .order('display_order', { ascending: true });

    if (prodErr) throw prodErr;

    // 3. Fetch price_dates info for target date
    const { data: priceDateInfo } = await supabaseAdmin
      .from('price_dates')
      .select('*')
      .eq('price_date', date)
      .single();

    // 4. Fetch daily prices for target date or copy source date
    const lookupDate = copyFromDate || date;
    const { data: pricesData } = await supabaseAdmin
      .from('daily_prices')
      .select('*')
      .eq('price_date', lookupDate);

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

    // Attach prices to products
    const productPriceList = (products || []).map(prod => {
      const pInfo = priceMap.get(Number(prod.id));
      return {
        ...prod,
        price: pInfo?.price || '',
        price_unit: pInfo?.unit || prod.default_unit || 'kg',
        price_notes: pInfo?.price_notes || '',
      };
    });

    // 5. Fetch recent price dates list for quick history navigation
    const { data: recentDates } = await supabaseAdmin
      .from('price_dates')
      .select('price_date, is_published, notes, updated_at')
      .order('price_date', { ascending: false })
      .limit(30);

    return NextResponse.json({
      success: true,
      date,
      copiedFrom: copyFromDate || null,
      priceDateInfo: priceDateInfo || {
        price_date: date,
        is_published: 0,
        notes: '',
      },
      categories: categories || [],
      subcategories: subcategories || [],
      products: productPriceList,
      recentDates: recentDates || [],
    });
  } catch (error: any) {
    console.error('Admin prices GET error:', error);
    return NextResponse.json(
      { success: false, error: error.message || 'Failed to fetch admin prices' },
      { status: 500 }
    );
  }
}

export async function POST(request: NextRequest) {
  try {
    const session = getAdminSessionFromRequest(request);
    if (!session) {
      return NextResponse.json({ success: false, error: 'Unauthorized' }, { status: 401 });
    }

    const body = await request.json();
    const { date, is_published, notes, items } = body;

    if (!date) {
      return NextResponse.json(
        { success: false, error: 'Date is required' },
        { status: 400 }
      );
    }

    const isPublished = is_published ? 1 : 0;
    const now = new Date().toISOString();

    // 1. Upsert price_dates
    const { error: dateError } = await supabaseAdmin
      .from('price_dates')
      .upsert({
        price_date: date,
        is_published: isPublished,
        notes: notes || '',
        published_at: isPublished === 1 ? now : null,
        updated_at: now,
      }, { onConflict: 'price_date' });

    if (dateError) throw dateError;

    // 2. Upsert daily prices in batches
    if (Array.isArray(items) && items.length > 0) {
      const priceRows = items
        .filter((item: any) => item && item.product_id)
        .map((item: any) => ({
          price_date: date,
          product_id: Number(item.product_id),
          price: (item.price || '').trim(),
          unit: (item.unit || item.price_unit || '').trim(),
          price_notes: (item.price_notes || '').trim(),
          updated_at: now,
        }));

      if (priceRows.length > 0) {
        const batchSize = 100;
        for (let i = 0; i < priceRows.length; i += batchSize) {
          const batch = priceRows.slice(i, i + batchSize);
          const { error: priceError } = await supabaseAdmin
            .from('daily_prices')
            .upsert(batch, { onConflict: 'price_date,product_id' });

          if (priceError) throw priceError;
        }
      }
    }

    return NextResponse.json({
      success: true,
      message: isPublished ? 'Prices published successfully!' : 'Prices saved as draft!',
      date,
      is_published: isPublished,
      updated_count: items?.length || 0,
    });
  } catch (error: any) {
    console.error('Admin prices POST error:', error);
    return NextResponse.json(
      { success: false, error: error.message || 'Failed to save prices' },
      { status: 500 }
    );
  }
}
