/**
 * DailyGurus Price List - SQLite to Supabase Migration Runner
 * Usage: SUPABASE_URL=... SUPABASE_SERVICE_ROLE_KEY=... node scripts/migrate_sqlite_to_supabase.js
 */

const fs = require('fs');
const path = require('path');
const { createClient } = require('@supabase/supabase-js');

const dumpFile = path.join(__dirname, 'sqlite_dump.json');

if (!fs.existsSync(dumpFile)) {
  console.error('Error: scripts/sqlite_dump.json not found! Run export_sqlite_json.php first.');
  process.exit(1);
}

const supabaseUrl = process.env.NEXT_PUBLIC_SUPABASE_URL || process.env.SUPABASE_URL;
const supabaseKey = process.env.SUPABASE_SERVICE_ROLE_KEY || process.env.SUPABASE_KEY;

if (!supabaseUrl || !supabaseKey) {
  console.error('Error: Missing SUPABASE_URL or SUPABASE_SERVICE_ROLE_KEY environment variables.');
  console.log('Run with:');
  console.log('  node -r dotenv/config scripts/migrate_sqlite_to_supabase.js');
  console.log('or:');
  console.log('  $env:NEXT_PUBLIC_SUPABASE_URL="https://...supabase.co"; $env:SUPABASE_SERVICE_ROLE_KEY="..."; node scripts/migrate_sqlite_to_supabase.js');
  process.exit(1);
}

const supabase = createClient(supabaseUrl, supabaseKey, {
  auth: { persistSession: false }
});

const data = JSON.parse(fs.readFileSync(dumpFile, 'utf8'));

async function runMigration() {
  console.log('\n=== STARTING SQLITE TO SUPABASE MIGRATION ===\n');

  // 1. Migrate Categories
  console.log(`1. Migrating ${data.categories.length} categories...`);
  for (const cat of data.categories) {
    const catType = cat.category_type || (cat.type === 'vegetable' ? 'veg' : cat.type) || (cat.slug === 'vegetables' ? 'veg' : 'fruit');
    const { error } = await supabase.from('categories').upsert({
      id: cat.id,
      name: cat.name,
      slug: cat.slug,
      icon: cat.icon || '',
      category_type: catType,
      display_order: cat.display_order || 0,
      active: cat.active !== undefined ? cat.active : 1
    }, { onConflict: 'id' });

    if (error) console.error(`   Error category ${cat.name}:`, error.message);
  }
  console.log('   ✓ Categories migrated.');

  // 2. Migrate Subcategories
  console.log(`2. Migrating ${data.subcategories.length} subcategories...`);
  for (const sub of data.subcategories) {
    const { error } = await supabase.from('subcategories').upsert({
      id: sub.id,
      category_id: sub.category_id,
      name: sub.name,
      slug: sub.slug,
      icon: sub.icon || '',
      display_order: sub.display_order || 0,
      active: sub.active !== undefined ? sub.active : 1
    }, { onConflict: 'id' });

    if (error) console.error(`   Error subcategory ${sub.name}:`, error.message);
  }
  console.log('   ✓ Subcategories migrated.');

  // 3. Migrate Products
  console.log(`3. Migrating ${data.products.length} products...`);
  for (const prod of data.products) {
    const { error } = await supabase.from('products').upsert({
      id: prod.id,
      category_id: prod.category_id,
      subcategory_id: prod.subcategory_id || null,
      name: prod.name,
      tamil_name: prod.tamil_name || '',
      image_url: prod.image_url || '',
      icon: prod.icon || '',
      default_unit: prod.default_unit || '',
      display_order: prod.display_order || 0,
      active: prod.active !== undefined ? prod.active : 1
    }, { onConflict: 'id' });

    if (error) console.error(`   Error product ${prod.name}:`, error.message);
  }
  console.log('   ✓ Products migrated.');

  // 4. Migrate Price Dates
  console.log(`4. Migrating ${data.price_dates.length} price dates...`);
  for (const pd of data.price_dates) {
    const { error } = await supabase.from('price_dates').upsert({
      price_date: pd.price_date,
      is_published: pd.is_published !== undefined ? pd.is_published : 0,
      notes: pd.notes || ''
    }, { onConflict: 'price_date' });

    if (error) console.error(`   Error price date ${pd.price_date}:`, error.message);
  }
  console.log('   ✓ Price dates migrated.');

  // 5. Migrate Daily Prices
  console.log(`5. Migrating ${data.daily_prices.length} daily price records...`);
  const batchSize = 50;
  for (let i = 0; i < data.daily_prices.length; i += batchSize) {
    const batch = data.daily_prices.slice(i, i + batchSize).map(p => ({
      price_date: p.price_date,
      product_id: p.product_id,
      price: p.price || '',
      unit: p.unit || '',
      price_notes: p.price_notes || p.notes || ''
    }));

    const { error } = await supabase.from('daily_prices').upsert(batch, {
      onConflict: 'price_date,product_id'
    });

    if (error) console.error(`   Error batch ${i}-${i + batchSize}:`, error.message);
  }
  console.log('   ✓ Daily prices migrated.');

  // 6. Migrate Default Admin User
  console.log('6. Seeding Admin credentials (Reginald)...');
  const bcrypt = require('bcryptjs');
  const hash = bcrypt.hashSync('12481248', 10);
  const { error: adminErr } = await supabase.from('admin_users').upsert({
    id: 1,
    username: 'Reginald',
    password_hash: hash,
    email: 'admin@dailygurus.com',
    role: 'admin'
  }, { onConflict: 'username' });

  if (adminErr) console.error('   Error seeding admin:', adminErr.message);
  else console.log('   ✓ Admin user seeded.');

  console.log('\n=== MIGRATION COMPLETED SUCCESSFULLY ===\n');
}

runMigration().catch(err => {
  console.error('Fatal migration error:', err);
  process.exit(1);
});
