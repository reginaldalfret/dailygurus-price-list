import { NextRequest, NextResponse } from 'next/server';
import { supabaseAdmin } from '@/lib/supabase/admin';
import { getAdminSessionFromRequest } from '@/lib/auth';
import { parseWhatsAppPriceList } from '@/lib/whatsapp-parser';
import { Product } from '@/lib/types';

export const dynamic = 'force-dynamic';

export async function POST(request: NextRequest) {
  try {
    const session = getAdminSessionFromRequest(request);
    if (!session) {
      return NextResponse.json({ success: false, error: 'Unauthorized' }, { status: 401 });
    }

    const body = await request.json();
    const { text, customCatalog } = body;

    if (!text || typeof text !== 'string' || !text.trim()) {
      return NextResponse.json(
        { success: false, error: 'Price list text is required' },
        { status: 400 }
      );
    }

    let catalog: Product[] = customCatalog;

    if (!catalog || !Array.isArray(catalog) || catalog.length === 0) {
      const { data: productsData, error } = await supabaseAdmin
        .from('products')
        .select('*')
        .eq('active', 1)
        .order('display_order', { ascending: true });

      if (error) throw error;
      catalog = productsData || [];
    }

    const parseResult = parseWhatsAppPriceList(text, catalog);

    return NextResponse.json({
      success: true,
      result: parseResult,
    });
  } catch (error: any) {
    console.error('Import parse error:', error);
    return NextResponse.json(
      { success: false, error: error.message || 'Failed to parse text' },
      { status: 500 }
    );
  }
}
