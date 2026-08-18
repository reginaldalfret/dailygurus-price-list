'use client';

import React, { useState, useEffect, useMemo } from 'react';
import { Product, Category, Subcategory } from '@/lib/types';

export default function AdminProductsPage() {
  const [products, setProducts] = useState<Product[]>([]);
  const [categories, setCategories] = useState<Category[]>([]);
  const [subcategories, setSubcategories] = useState<Subcategory[]>([]);
  const [loading, setLoading] = useState(true);
  const [searchQuery, setSearchQuery] = useState('');
  const [categoryFilter, setCategoryFilter] = useState<'all' | 'veg' | 'fruit'>('all');
  const [activeFilter, setActiveFilter] = useState<'all' | 'active' | 'inactive'>('all');

  // Modal State
  const [isModalOpen, setIsModalOpen] = useState(false);
  const [modalMode, setModalMode] = useState<'create' | 'edit'>('create');
  const [editingProduct, setEditingProduct] = useState<Partial<Product>>({
    name: '',
    tamil_name: '',
    category_id: 1,
    subcategory_id: null,
    icon: '🥦',
    default_unit: 'kg',
    display_order: 0,
    active: 1,
  });
  const [saving, setSaving] = useState(false);
  const [toast, setToast] = useState<{ message: string; type: 'success' | 'error' } | null>(null);

  const showToast = (message: string, type: 'success' | 'error' = 'success') => {
    setToast({ message, type });
    setTimeout(() => setToast(null), 4000);
  };

  const loadProducts = async () => {
    setLoading(true);
    try {
      const res = await fetch('/api/admin/products');
      if (res.status === 401) {
        window.location.href = '/admin';
        return;
      }

      const json = await res.json();
      if (!json.success) throw new Error(json.error || 'Failed to load products');

      setProducts(json.products || []);
      setCategories(json.categories || []);
      setSubcategories(json.subcategories || []);
    } catch (err: any) {
      showToast(err.message || 'Error loading product catalog', 'error');
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    loadProducts();
  }, []);

  // Open Create Modal
  const handleOpenCreate = () => {
    setModalMode('create');
    setEditingProduct({
      name: '',
      tamil_name: '',
      category_id: 1,
      subcategory_id: subcategories.find(s => s.category_id === 1)?.id || null,
      icon: '🥦',
      default_unit: 'kg',
      display_order: (products.length + 1) * 10,
      active: 1,
    });
    setIsModalOpen(true);
  };

  // Open Edit Modal
  const handleOpenEdit = (product: Product) => {
    setModalMode('edit');
    setEditingProduct({ ...product });
    setIsModalOpen(true);
  };

  // Toggle Active Status
  const handleToggleActive = async (product: Product) => {
    const nextActive = product.active === 1 ? 0 : 1;
    try {
      const res = await fetch('/api/admin/products', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          action: 'toggle',
          id: product.id,
          active: nextActive,
        }),
      });

      const json = await res.json();
      if (!res.ok || !json.success) throw new Error(json.error || 'Failed to toggle status');

      setProducts(prev =>
        prev.map(p => (p.id === product.id ? { ...p, active: nextActive } : p))
      );
      showToast(`${product.name} is now ${nextActive === 1 ? 'Active' : 'Inactive'}.`, 'success');
    } catch (err: any) {
      showToast(err.message || 'Error toggling product', 'error');
    }
  };

  // Save Modal Form (Create or Update)
  const handleSaveProduct = async (e: React.FormEvent) => {
    e.preventDefault();
    if (!editingProduct.name?.trim()) {
      showToast('Product name is required', 'error');
      return;
    }

    setSaving(true);
    try {
      const res = await fetch('/api/admin/products', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          action: modalMode === 'create' ? 'create' : 'update',
          product: editingProduct,
        }),
      });

      const json = await res.json();
      if (!res.ok || !json.success) throw new Error(json.error || 'Failed to save product');

      if (modalMode === 'create') {
        setProducts(prev => [...prev, json.product]);
        showToast(`Created product "${json.product.name}" successfully!`, 'success');
      } else {
        setProducts(prev => prev.map(p => (p.id === json.product.id ? json.product : p)));
        showToast(`Updated product "${json.product.name}" successfully!`, 'success');
      }

      setIsModalOpen(false);
    } catch (err: any) {
      showToast(err.message || 'Error saving product', 'error');
    } finally {
      setSaving(false);
    }
  };

  // Filtered Products
  const filteredProducts = useMemo(() => {
    return products.filter(p => {
      // Category filter
      if (categoryFilter === 'veg' && p.category_id !== 1) return false;
      if (categoryFilter === 'fruit' && p.category_id !== 2) return false;

      // Active filter
      if (activeFilter === 'active' && p.active !== 1) return false;
      if (activeFilter === 'inactive' && p.active === 1) return false;

      // Search Query
      if (!searchQuery.trim()) return true;
      const q = searchQuery.toLowerCase();
      return (
        (p.name || '').toLowerCase().includes(q) ||
        (p.tamil_name || '').toLowerCase().includes(q) ||
        (p.default_unit || '').toLowerCase().includes(q)
      );
    });
  }, [products, categoryFilter, activeFilter, searchQuery]);

  return (
    <div className="admin-container">
      {/* Toast Notification */}
      {toast && (
        <div
          style={{
            position: 'fixed',
            top: '80px',
            right: '24px',
            zIndex: 1000,
            padding: '14px 20px',
            borderRadius: '10px',
            color: '#ffffff',
            fontWeight: 600,
            fontSize: '0.9rem',
            boxShadow: 'var(--shadow-xl)',
            backgroundColor: toast.type === 'error' ? '#ef4444' : '#059669',
            animation: 'slideUp 0.2s ease-out',
          }}
        >
          {toast.message}
        </div>
      )}

      {/* Header & Add Button */}
      <div
        style={{
          display: 'flex',
          justifyContent: 'space-between',
          alignItems: 'center',
          flexWrap: 'wrap',
          gap: '16px',
          marginBottom: '24px',
        }}
      >
        <div>
          <h1 style={{ fontSize: '1.75rem', fontWeight: 800, color: '#0f172a', letterSpacing: '-0.02em', marginBottom: '4px' }}>
            Product Catalog Master
          </h1>
          <p style={{ color: '#64748b', fontSize: '0.92rem' }}>
            Manage English & Tamil commodity names, categories, default wholesale units, and icons
          </p>
        </div>

        <button
          type="button"
          onClick={handleOpenCreate}
          className="dg-btn dg-btn-primary"
          style={{ fontWeight: 700 }}
        >
          <span>➕</span>
          <span>Add New Product</span>
        </button>
      </div>

      {/* Filter Controls Bar */}
      <div
        className="dg-card"
        style={{
          padding: '16px 20px',
          marginBottom: '24px',
          display: 'flex',
          justifyContent: 'space-between',
          alignItems: 'center',
          flexWrap: 'wrap',
          gap: '16px',
        }}
      >
        {/* Category & Status Filter Tabs */}
        <div style={{ display: 'flex', gap: '8px', flexWrap: 'wrap' }}>
          <button
            type="button"
            onClick={() => setCategoryFilter('all')}
            className={`dg-btn ${categoryFilter === 'all' ? 'dg-btn-primary' : 'dg-btn-outline'}`}
            style={{ padding: '6px 14px', fontSize: '0.84rem' }}
          >
            All Categories ({products.length})
          </button>
          <button
            type="button"
            onClick={() => setCategoryFilter('veg')}
            className={`dg-btn ${categoryFilter === 'veg' ? 'dg-btn-primary' : 'dg-btn-outline'}`}
            style={{ padding: '6px 14px', fontSize: '0.84rem' }}
          >
            🥦 Vegetables ({products.filter(p => p.category_id === 1).length})
          </button>
          <button
            type="button"
            onClick={() => setCategoryFilter('fruit')}
            className={`dg-btn ${categoryFilter === 'fruit' ? 'dg-btn-primary' : 'dg-btn-outline'}`}
            style={{ padding: '6px 14px', fontSize: '0.84rem' }}
          >
            🍎 Fruits ({products.filter(p => p.category_id === 2).length})
          </button>
        </div>

        {/* Search Input */}
        <div style={{ maxWidth: '300px', width: '100%' }}>
          <input
            type="text"
            className="dg-input"
            placeholder="🔍 Search name / தமிழ்..."
            value={searchQuery}
            onChange={e => setSearchQuery(e.target.value)}
          />
        </div>
      </div>

      {/* Products Table */}
      <div className="dg-card" style={{ overflow: 'hidden' }}>
        {loading ? (
          <div style={{ textAlign: 'center', padding: '60px 20px', color: '#64748b' }}>
            <div style={{ fontSize: '1.8rem', marginBottom: '10px' }}>⏳</div>
            <p>Loading master catalog...</p>
          </div>
        ) : (
          <div style={{ overflowX: 'auto' }}>
            <table style={{ width: '100%', borderCollapse: 'collapse', fontSize: '0.9rem' }}>
              <thead>
                <tr style={{ backgroundColor: '#f8fafc', borderBottom: '1px solid var(--border-color)', textAlign: 'left' }}>
                  <th style={{ padding: '14px 18px', fontWeight: 700, color: '#475569', width: '50px' }}>Icon</th>
                  <th style={{ padding: '14px 18px', fontWeight: 700, color: '#475569' }}>Product Name</th>
                  <th style={{ padding: '14px 18px', fontWeight: 700, color: '#475569' }}>Tamil Name (தமிழ்)</th>
                  <th style={{ padding: '14px 18px', fontWeight: 700, color: '#475569' }}>Category</th>
                  <th style={{ padding: '14px 18px', fontWeight: 700, color: '#475569' }}>Default Unit</th>
                  <th style={{ padding: '14px 18px', fontWeight: 700, color: '#475569', width: '100px' }}>Status</th>
                  <th style={{ padding: '14px 18px', fontWeight: 700, color: '#475569', textAlign: 'right' }}>Actions</th>
                </tr>
              </thead>
              <tbody>
                {filteredProducts.map(prod => {
                  const cat = categories.find(c => c.id === prod.category_id);
                  const sub = subcategories.find(s => s.id === prod.subcategory_id);

                  return (
                    <tr
                      key={prod.id}
                      style={{
                        borderBottom: '1px solid var(--border-color)',
                        opacity: prod.active === 1 ? 1 : 0.6,
                        backgroundColor: prod.active === 1 ? '#ffffff' : '#fafafa',
                      }}
                    >
                      {/* Icon */}
                      <td style={{ padding: '12px 18px', textAlign: 'center', fontSize: '1.3rem' }}>
                        {prod.icon || (prod.category_id === 1 ? '🥦' : '🍎')}
                      </td>

                      {/* English Name */}
                      <td style={{ padding: '12px 18px', fontWeight: 700, color: '#0f172a' }}>
                        {prod.name}
                      </td>

                      {/* Tamil Name */}
                      <td style={{ padding: '12px 18px' }}>
                        {prod.tamil_name ? (
                          <span className="tamil-text" style={{ color: '#059669', fontWeight: 600, fontSize: '0.92rem' }}>
                            {prod.tamil_name}
                          </span>
                        ) : (
                          <span style={{ color: '#cbd5e1' }}>—</span>
                        )}
                      </td>

                      {/* Category & Subcategory */}
                      <td style={{ padding: '12px 18px' }}>
                        <div style={{ display: 'flex', flexDirection: 'column', gap: '2px' }}>
                          <div>
                            {prod.category_id === 1 ? (
                              <span className="badge badge-veg">Veg</span>
                            ) : (
                              <span className="badge badge-fruit">Fruit</span>
                            )}
                          </div>
                          {sub && <span style={{ fontSize: '0.75rem', color: '#64748b' }}>{sub.name}</span>}
                        </div>
                      </td>

                      {/* Default Unit */}
                      <td style={{ padding: '12px 18px', color: '#475569' }}>
                        <code>{prod.default_unit || 'kg'}</code>
                      </td>

                      {/* Status Toggle */}
                      <td style={{ padding: '12px 18px' }}>
                        <button
                          type="button"
                          onClick={() => handleToggleActive(prod)}
                          style={{
                            border: 'none',
                            background: 'none',
                            cursor: 'pointer',
                            padding: 0,
                          }}
                          title={`Click to ${prod.active === 1 ? 'deactivate' : 'activate'}`}
                        >
                          {prod.active === 1 ? (
                            <span className="badge" style={{ backgroundColor: '#dcfce7', color: '#15803d' }}>
                              Active
                            </span>
                          ) : (
                            <span className="badge" style={{ backgroundColor: '#f1f5f9', color: '#64748b' }}>
                              Inactive
                            </span>
                          )}
                        </button>
                      </td>

                      {/* Actions */}
                      <td style={{ padding: '12px 18px', textAlign: 'right' }}>
                        <button
                          type="button"
                          onClick={() => handleOpenEdit(prod)}
                          className="dg-btn dg-btn-secondary"
                          style={{ padding: '5px 12px', fontSize: '0.8rem' }}
                        >
                          ✏️ Edit
                        </button>
                      </td>
                    </tr>
                  );
                })}

                {filteredProducts.length === 0 && (
                  <tr>
                    <td colSpan={7} style={{ padding: '40px', textAlign: 'center', color: '#94a3b8' }}>
                      No matching products found in catalog.
                    </td>
                  </tr>
                )}
              </tbody>
            </table>
          </div>
        )}
      </div>

      {/* Add / Edit Product Modal */}
      {isModalOpen && (
        <div className="dg-modal-backdrop" onClick={() => setIsModalOpen(false)}>
          <div
            className="dg-modal-content"
            style={{ maxWidth: '600px' }}
            onClick={e => e.stopPropagation()}
          >
            <div
              style={{
                padding: '18px 24px',
                borderBottom: '1px solid var(--border-color)',
                display: 'flex',
                justifyContent: 'space-between',
                alignItems: 'center',
              }}
            >
              <h3 style={{ fontSize: '1.2rem', fontWeight: 800, color: '#0f172a' }}>
                {modalMode === 'create' ? '➕ Add New Catalog Product' : `✏️ Edit "${editingProduct.name}"`}
              </h3>
              <button
                type="button"
                onClick={() => setIsModalOpen(false)}
                style={{ background: 'none', border: 'none', fontSize: '1.3rem', cursor: 'pointer', color: '#94a3b8' }}
              >
                ✕
              </button>
            </div>

            <form onSubmit={handleSaveProduct}>
              <div style={{ padding: '20px 24px', display: 'flex', flexDirection: 'column', gap: '16px' }}>
                {/* English Name */}
                <div>
                  <label style={{ display: 'block', fontSize: '0.84rem', fontWeight: 700, color: '#334155', marginBottom: '6px' }}>
                    Product Name (English) *
                  </label>
                  <input
                    type="text"
                    className="dg-input"
                    placeholder="e.g. Tomato Big Crates (Premium)"
                    value={editingProduct.name || ''}
                    onChange={e => setEditingProduct({ ...editingProduct, name: e.target.value })}
                    required
                    autoFocus
                  />
                </div>

                {/* Tamil Name */}
                <div>
                  <label style={{ display: 'block', fontSize: '0.84rem', fontWeight: 700, color: '#334155', marginBottom: '6px' }}>
                    Tamil Name (தமிழ் பெயர்)
                  </label>
                  <input
                    type="text"
                    className="dg-input tamil-text"
                    placeholder="e.g. தக்காளி (பெரிய பெட்டி)"
                    value={editingProduct.tamil_name || ''}
                    onChange={e => setEditingProduct({ ...editingProduct, tamil_name: e.target.value })}
                  />
                </div>

                {/* Category & Subcategory Row */}
                <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: '14px' }}>
                  <div>
                    <label style={{ display: 'block', fontSize: '0.84rem', fontWeight: 700, color: '#334155', marginBottom: '6px' }}>
                      Category *
                    </label>
                    <select
                      className="dg-select"
                      value={editingProduct.category_id || 1}
                      onChange={e => {
                        const catId = Number(e.target.value);
                        const firstSub = subcategories.find(s => s.category_id === catId);
                        setEditingProduct({
                          ...editingProduct,
                          category_id: catId,
                          subcategory_id: firstSub?.id || null,
                          icon: catId === 1 ? '🥦' : '🍎',
                        });
                      }}
                    >
                      <option value={1}>🥦 Vegetables</option>
                      <option value={2}>🍎 Fruits</option>
                    </select>
                  </div>

                  <div>
                    <label style={{ display: 'block', fontSize: '0.84rem', fontWeight: 700, color: '#334155', marginBottom: '6px' }}>
                      Subcategory
                    </label>
                    <select
                      className="dg-select"
                      value={editingProduct.subcategory_id || ''}
                      onChange={e => setEditingProduct({ ...editingProduct, subcategory_id: e.target.value ? Number(e.target.value) : null })}
                    >
                      <option value="">-- No Subcategory --</option>
                      {subcategories
                        .filter(s => s.category_id === Number(editingProduct.category_id || 1))
                        .map(sub => (
                          <option key={sub.id} value={sub.id}>
                            {sub.name}
                          </option>
                        ))}
                    </select>
                  </div>
                </div>

                {/* Default Unit & Icon */}
                <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: '14px' }}>
                  <div>
                    <label style={{ display: 'block', fontSize: '0.84rem', fontWeight: 700, color: '#334155', marginBottom: '6px' }}>
                      Default Unit
                    </label>
                    <input
                      type="text"
                      className="dg-input"
                      placeholder="kg / crate / box / bag"
                      value={editingProduct.default_unit || 'kg'}
                      onChange={e => setEditingProduct({ ...editingProduct, default_unit: e.target.value })}
                    />
                  </div>

                  <div>
                    <label style={{ display: 'block', fontSize: '0.84rem', fontWeight: 700, color: '#334155', marginBottom: '6px' }}>
                      Icon / Emoji
                    </label>
                    <input
                      type="text"
                      className="dg-input"
                      placeholder="e.g. 🍅, 🧅, 🥦, 🍎"
                      value={editingProduct.icon || ''}
                      onChange={e => setEditingProduct({ ...editingProduct, icon: e.target.value })}
                    />
                  </div>
                </div>

                {/* Active & Display Order */}
                <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: '14px', alignItems: 'center' }}>
                  <div>
                    <label style={{ display: 'block', fontSize: '0.84rem', fontWeight: 700, color: '#334155', marginBottom: '6px' }}>
                      Display Priority Order
                    </label>
                    <input
                      type="number"
                      className="dg-input"
                      value={editingProduct.display_order ?? 0}
                      onChange={e => setEditingProduct({ ...editingProduct, display_order: Number(e.target.value) })}
                    />
                  </div>

                  <div style={{ paddingTop: '22px' }}>
                    <label style={{ display: 'flex', alignItems: 'center', gap: '8px', cursor: 'pointer', fontSize: '0.88rem', fontWeight: 600 }}>
                      <input
                        type="checkbox"
                        checked={editingProduct.active === 1}
                        onChange={e => setEditingProduct({ ...editingProduct, active: e.target.checked ? 1 : 0 })}
                      />
                      <span>Active in Daily Price Sheet</span>
                    </label>
                  </div>
                </div>
              </div>

              {/* Modal Footer */}
              <div
                style={{
                  padding: '16px 24px',
                  borderTop: '1px solid var(--border-color)',
                  backgroundColor: '#f8fafc',
                  display: 'flex',
                  justifyContent: 'flex-end',
                  gap: '10px',
                }}
              >
                <button
                  type="button"
                  onClick={() => setIsModalOpen(false)}
                  className="dg-btn dg-btn-outline"
                >
                  Cancel
                </button>
                <button
                  type="submit"
                  disabled={saving}
                  className="dg-btn dg-btn-primary"
                >
                  {saving ? 'Saving...' : modalMode === 'create' ? 'Create Product' : 'Save Changes'}
                </button>
              </div>
            </form>
          </div>
        </div>
      )}
    </div>
  );
}
