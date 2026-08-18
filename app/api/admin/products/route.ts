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

    const { data: categories } = await supabaseAdmin
      .from('categories')
      .select('*')
      .order('display_order', { ascending: true });

    const { data: subcategories } = await supabaseAdmin
      .from('subcategories')
      .select('*')
      .order('display_order', { ascending: true });

    const { data: products, error: prodErr } = await supabaseAdmin
      .from('products')
      .select('*')
      .order('display_order', { ascending: true });

    if (prodErr) throw prodErr;

    return NextResponse.json({
      success: true,
      products: products || [],
      categories: categories || [],
      subcategories: subcategories || [],
    });
  } catch (error: any) {
    console.error('Products GET error:', error);
    return NextResponse.json(
      { success: false, error: error.message || 'Failed to fetch products' },
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
    const { action, product } = body;

    const now = new Date().toISOString();

    // 1. Toggle Active
    if (action === 'toggle') {
      const { id, active } = body;
      if (!id) {
        return NextResponse.json({ success: false, error: 'Product ID is required' }, { status: 400 });
      }

      const { data, error } = await supabaseAdmin
        .from('products')
        .update({ active: active ? 1 : 0, updated_at: now })
        .eq('id', id)
        .select()
        .single();

      if (error) throw error;

      return NextResponse.json({
        success: true,
        message: `Product ${active ? 'activated' : 'deactivated'} successfully`,
        product: data,
      });
    }

    // 2. Delete Product
    if (action === 'delete') {
      const { id } = body;
      if (!id) {
        return NextResponse.json({ success: false, error: 'Product ID is required' }, { status: 400 });
      }

      const { error } = await supabaseAdmin
        .from('products')
        .delete()
        .eq('id', id);

      if (error) throw error;

      return NextResponse.json({
        success: true,
        message: 'Product deleted successfully',
      });
    }

    // 3. Create or Update Product
    if (action === 'create' || action === 'update' || !action) {
      if (!product || !product.name) {
        return NextResponse.json(
          { success: false, error: 'Product name is required' },
          { status: 400 }
        );
      }

      const payload = {
        category_id: Number(product.category_id) || 1,
        subcategory_id: product.subcategory_id ? Number(product.subcategory_id) : null,
        name: (product.name || '').trim(),
        tamil_name: (product.tamil_name || '').trim(),
        icon: (product.icon || '').trim(),
        image_url: (product.image_url || '').trim(),
        default_unit: (product.default_unit || 'kg').trim(),
        display_order: Number(product.display_order) || 0,
        active: product.active !== undefined ? (product.active ? 1 : 0) : 1,
        updated_at: now,
      };

      if (product.id && product.id > 0) {
        // Update existing
        const { data, error } = await supabaseAdmin
          .from('products')
          .update(payload)
          .eq('id', product.id)
          .select()
          .single();

        if (error) throw error;

        return NextResponse.json({
          success: true,
          message: 'Product updated successfully',
          product: data,
        });
      } else {
        // Create new
        const { data, error } = await supabaseAdmin
          .from('products')
          .insert({
            ...payload,
            created_at: now,
          })
          .select()
          .single();

        if (error) throw error;

        return NextResponse.json({
          success: true,
          message: 'Product created successfully',
          product: data,
        });
      }
    }

    return NextResponse.json(
      { success: false, error: 'Invalid action specified' },
      { status: 400 }
    );
  } catch (error: any) {
    console.error('Products POST error:', error);
    return NextResponse.json(
      { success: false, error: error.message || 'Failed to save product' },
      { status: 500 }
    );
  }
}
