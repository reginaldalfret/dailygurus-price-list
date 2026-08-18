'use client';

import React, { useState, useEffect } from 'react';
import Link from 'next/link';
import { formatDateLong } from '@/lib/price-formatter';

interface DashboardData {
  todayDate: string;
  isTodayPublished: boolean;
  hasTodayPrices: boolean;
  todayPricedCount: number;
  totalProducts: number;
  vegCount: number;
  fruitCount: number;
  recentDates: Array<{
    price_date: string;
    is_published: number;
    notes: string;
    updated_at: string;
  }>;
}

export default function AdminDashboardPage() {
  const [data, setData] = useState<DashboardData | null>(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);

  const todayStr = new Date().toISOString().split('T')[0];

  useEffect(() => {
    async function loadDashboard() {
      try {
        setLoading(true);
        // Fetch prices for today to get status
        const priceRes = await fetch(`/api/admin/prices?date=${todayStr}`);
        if (priceRes.status === 401) {
          window.location.href = '/admin';
          return;
        }

        const priceJson = await priceRes.json();
        const prodRes = await fetch('/api/admin/products');
        const prodJson = await prodRes.json();

        const prods = prodJson.products || [];
        const vegCount = prods.filter((p: any) => p.category_id === 1 && p.active === 1).length;
        const fruitCount = prods.filter((p: any) => p.category_id === 2 && p.active === 1).length;

        const currentPrices = (priceJson.products || []).filter((p: any) => p.price && p.price !== '—' && p.price.toLowerCase() !== 'nill');

        setData({
          todayDate: todayStr,
          isTodayPublished: priceJson.priceDateInfo?.is_published === 1,
          hasTodayPrices: currentPrices.length > 0,
          todayPricedCount: currentPrices.length,
          totalProducts: prods.length,
          vegCount,
          fruitCount,
          recentDates: priceJson.recentDates || [],
        });
      } catch (err: any) {
        setError(err.message || 'Failed to load dashboard metrics');
      } finally {
        setLoading(false);
      }
    }

    loadDashboard();
  }, [todayStr]);

  if (loading) {
    return (
      <div className="admin-container" style={{ textAlign: 'center', padding: '80px 20px' }}>
        <div style={{ fontSize: '2rem', marginBottom: '16px' }}>⏳</div>
        <h3 style={{ fontSize: '1.2rem', fontWeight: 700, color: '#334155' }}>Loading Admin Dashboard...</h3>
        <p style={{ color: '#64748b', fontSize: '0.9rem' }}>Fetching real-time market data and catalog statistics</p>
      </div>
    );
  }

  if (error || !data) {
    return (
      <div className="admin-container" style={{ padding: '40px 20px' }}>
        <div className="dg-card" style={{ padding: '24px', borderColor: 'var(--danger-border)', backgroundColor: 'var(--danger-light)' }}>
          <h3 style={{ color: 'var(--danger)', fontWeight: 700, marginBottom: '8px' }}>⚠️ Error loading dashboard</h3>
          <p style={{ color: '#7f1d1d', fontSize: '0.9rem' }}>{error || 'Unable to connect to database'}</p>
        </div>
      </div>
    );
  }

  return (
    <div className="admin-container">
      {/* Header Banner */}
      <div
        style={{
          display: 'flex',
          justifyContent: 'space-between',
          alignItems: 'center',
          flexWrap: 'wrap',
          gap: '16px',
          marginBottom: '28px',
        }}
      >
        <div>
          <h1 style={{ fontSize: '1.75rem', fontWeight: 800, color: '#0f172a', letterSpacing: '-0.02em', marginBottom: '4px' }}>
            Wholesale Operations Dashboard
          </h1>
          <p style={{ color: '#64748b', fontSize: '0.92rem' }}>
            Today is <strong style={{ color: '#0f172a' }}>{formatDateLong(data.todayDate)}</strong>
          </p>
        </div>

        <div style={{ display: 'flex', gap: '12px' }}>
          <Link href={`/admin/prices?date=${data.todayDate}`} className="dg-btn dg-btn-primary">
            <span>⚡</span>
            <span>Update Today&apos;s Prices</span>
          </Link>
        </div>
      </div>

      {/* Today's Publishing Status Alert Card */}
      <div
        className="dg-card"
        style={{
          padding: '20px 24px',
          marginBottom: '28px',
          backgroundColor: data.isTodayPublished ? '#f0fdf4' : data.hasTodayPrices ? '#fffbeb' : '#f8fafc',
          borderLeft: `5px solid ${data.isTodayPublished ? '#10b981' : data.hasTodayPrices ? '#f59e0b' : '#94a3b8'}`,
        }}
      >
        <div style={{ display: 'flex', alignItems: 'center', justifyContent: 'space-between', flexWrap: 'wrap', gap: '16px' }}>
          <div style={{ display: 'flex', alignItems: 'center', gap: '16px' }}>
            <div
              style={{
                width: '44px',
                height: '44px',
                borderRadius: '12px',
                backgroundColor: data.isTodayPublished ? '#dcfce7' : data.hasTodayPrices ? '#fef3c7' : '#e2e8f0',
                display: 'flex',
                alignItems: 'center',
                justifyContent: 'center',
                fontSize: '1.4rem',
              }}
            >
              {data.isTodayPublished ? '🚀' : data.hasTodayPrices ? '📝' : '⚠️'}
            </div>
            <div>
              <div style={{ display: 'flex', alignItems: 'center', gap: '10px', marginBottom: '4px' }}>
                <span style={{ fontWeight: 800, fontSize: '1.05rem', color: '#0f172a' }}>
                  Today&apos;s Price Sheet Status:
                </span>
                {data.isTodayPublished ? (
                  <span className="badge badge-published">● Published Live</span>
                ) : data.hasTodayPrices ? (
                  <span className="badge badge-draft">● Draft (Not Live)</span>
                ) : (
                  <span className="badge" style={{ backgroundColor: '#f1f5f9', color: '#64748b' }}>● No Prices Entered</span>
                )}
              </div>
              <p style={{ fontSize: '0.86rem', color: '#475569' }}>
                {data.isTodayPublished
                  ? `Live on the public website with ${data.todayPricedCount} items priced.`
                  : data.hasTodayPrices
                  ? `${data.todayPricedCount} prices saved in draft. Publish now to make them visible to wholesale buyers.`
                  : `No prices entered yet for today (${formatDateLong(data.todayDate)}). Use WhatsApp bulk paste or copy from yesterday.`}
              </p>
            </div>
          </div>

          <Link href={`/admin/prices?date=${data.todayDate}`} className="dg-btn dg-btn-secondary" style={{ fontWeight: 700 }}>
            <span>Manage Price Sheet →</span>
          </Link>
        </div>
      </div>

      {/* KPI Stats Grid */}
      <div
        style={{
          display: 'grid',
          gridTemplateColumns: 'repeat(auto-fit, minmax(220px, 1fr))',
          gap: '18px',
          marginBottom: '32px',
        }}
      >
        <div className="dg-card" style={{ padding: '20px 22px' }}>
          <div style={{ fontSize: '0.8rem', fontWeight: 700, color: '#64748b', textTransform: 'uppercase', letterSpacing: '0.04em', marginBottom: '8px' }}>
            Catalog Products
          </div>
          <div style={{ fontSize: '2rem', fontWeight: 800, color: '#0f172a', lineHeight: 1.1, marginBottom: '6px' }}>
            {data.totalProducts}
          </div>
          <div style={{ fontSize: '0.82rem', color: '#059669', fontWeight: 600 }}>
            Master items in database
          </div>
        </div>

        <div className="dg-card" style={{ padding: '20px 22px' }}>
          <div style={{ fontSize: '0.8rem', fontWeight: 700, color: '#64748b', textTransform: 'uppercase', letterSpacing: '0.04em', marginBottom: '8px' }}>
            Active Vegetables
          </div>
          <div style={{ fontSize: '2rem', fontWeight: 800, color: '#047857', lineHeight: 1.1, marginBottom: '6px' }}>
            {data.vegCount}
          </div>
          <div style={{ fontSize: '0.82rem', color: '#64748b' }}>
            Vegetable catalog items
          </div>
        </div>

        <div className="dg-card" style={{ padding: '20px 22px' }}>
          <div style={{ fontSize: '0.8rem', fontWeight: 700, color: '#64748b', textTransform: 'uppercase', letterSpacing: '0.04em', marginBottom: '8px' }}>
            Active Fruits
          </div>
          <div style={{ fontSize: '2rem', fontWeight: 800, color: '#b91c1c', lineHeight: 1.1, marginBottom: '6px' }}>
            {data.fruitCount}
          </div>
          <div style={{ fontSize: '0.82rem', color: '#64748b' }}>
            Fruit catalog items
          </div>
        </div>

        <div className="dg-card" style={{ padding: '20px 22px' }}>
          <div style={{ fontSize: '0.8rem', fontWeight: 700, color: '#64748b', textTransform: 'uppercase', letterSpacing: '0.04em', marginBottom: '8px' }}>
            Today Priced Items
          </div>
          <div style={{ fontSize: '2rem', fontWeight: 800, color: '#0284c7', lineHeight: 1.1, marginBottom: '6px' }}>
            {data.todayPricedCount}
          </div>
          <div style={{ fontSize: '0.82rem', color: '#64748b' }}>
            {data.totalProducts > 0 ? `${Math.round((data.todayPricedCount / data.totalProducts) * 100)}% of catalog` : '0%'}
          </div>
        </div>
      </div>

      {/* Quick Action Cards Grid */}
      <h2 style={{ fontSize: '1.2rem', fontWeight: 800, color: '#0f172a', marginBottom: '16px' }}>
        Quick Management Tools
      </h2>

      <div
        style={{
          display: 'grid',
          gridTemplateColumns: 'repeat(auto-fit, minmax(280px, 1fr))',
          gap: '20px',
          marginBottom: '36px',
        }}
      >
        <Link
          href={`/admin/prices?date=${data.todayDate}&openImport=true`}
          className="dg-card"
          style={{
            padding: '24px',
            textDecoration: 'none',
            color: 'inherit',
            display: 'flex',
            flexDirection: 'column',
            justifyContent: 'space-between',
            borderTop: '4px solid #10b981',
          }}
        >
          <div>
            <div style={{ fontSize: '1.8rem', marginBottom: '12px' }}>📋</div>
            <h3 style={{ fontSize: '1.1rem', fontWeight: 700, color: '#0f172a', marginBottom: '6px' }}>
              WhatsApp Bulk Import
            </h3>
            <p style={{ fontSize: '0.86rem', color: '#64748b', lineHeight: 1.4 }}>
              Paste daily raw market text or WhatsApp lists. Our parser fuzzy-matches items against your catalog instantly.
            </p>
          </div>
          <div style={{ marginTop: '16px', fontWeight: 700, color: '#059669', fontSize: '0.88rem' }}>
            Launch Bulk Parser →
          </div>
        </Link>

        <Link
          href={`/admin/prices?date=${data.todayDate}`}
          className="dg-card"
          style={{
            padding: '24px',
            textDecoration: 'none',
            color: 'inherit',
            display: 'flex',
            flexDirection: 'column',
            justifyContent: 'space-between',
            borderTop: '4px solid #0284c7',
          }}
        >
          <div>
            <div style={{ fontSize: '1.8rem', marginBottom: '12px' }}>💰</div>
            <h3 style={{ fontSize: '1.1rem', fontWeight: 700, color: '#0f172a', marginBottom: '6px' }}>
              Daily Price Editor
            </h3>
            <p style={{ fontSize: '0.86rem', color: '#64748b', lineHeight: 1.4 }}>
              Full spreadsheet-style editor with live search, copy previous day, unit selector, and instant publish toggle.
            </p>
          </div>
          <div style={{ marginTop: '16px', fontWeight: 700, color: '#0284c7', fontSize: '0.88rem' }}>
            Edit Daily Sheet →
          </div>
        </Link>

        <Link
          href="/admin/products"
          className="dg-card"
          style={{
            padding: '24px',
            textDecoration: 'none',
            color: 'inherit',
            display: 'flex',
            flexDirection: 'column',
            justifyContent: 'space-between',
            borderTop: '4px solid #8b5cf6',
          }}
        >
          <div>
            <div style={{ fontSize: '1.8rem', marginBottom: '12px' }}>📦</div>
            <h3 style={{ fontSize: '1.1rem', fontWeight: 700, color: '#0f172a', marginBottom: '6px' }}>
              Product Catalog Manager
            </h3>
            <p style={{ fontSize: '0.86rem', color: '#64748b', lineHeight: 1.4 }}>
              Add new commodities, edit English & Tamil names, customize default wholesale units, and set display priority.
            </p>
          </div>
          <div style={{ marginTop: '16px', fontWeight: 700, color: '#8b5cf6', fontSize: '0.88rem' }}>
            Manage Products →
          </div>
        </Link>

        <Link
          href="/admin/history"
          className="dg-card"
          style={{
            padding: '24px',
            textDecoration: 'none',
            color: 'inherit',
            display: 'flex',
            flexDirection: 'column',
            justifyContent: 'space-between',
            borderTop: '4px solid #f59e0b',
          }}
        >
          <div>
            <div style={{ fontSize: '1.8rem', marginBottom: '12px' }}>📅</div>
            <h3 style={{ fontSize: '1.1rem', fontWeight: 700, color: '#0f172a', marginBottom: '6px' }}>
              Historical Price Archives
            </h3>
            <p style={{ fontSize: '0.86rem', color: '#64748b', lineHeight: 1.4 }}>
              Review past daily price sheets, track wholesale market movements, or duplicate historical records.
            </p>
          </div>
          <div style={{ marginTop: '16px', fontWeight: 700, color: '#f59e0b', fontSize: '0.88rem' }}>
            View History →
          </div>
        </Link>
      </div>

      {/* Recent Price Sheets Table */}
      <div className="dg-card" style={{ overflow: 'hidden' }}>
        <div
          style={{
            padding: '16px 20px',
            borderBottom: '1px solid var(--border-color)',
            display: 'flex',
            alignItems: 'center',
            justifyContent: 'space-between',
          }}
        >
          <h3 style={{ fontSize: '1.05rem', fontWeight: 700, color: '#0f172a' }}>
            Recent Price Sheets
          </h3>
          <Link href="/admin/history" style={{ fontSize: '0.82rem', color: 'var(--primary)', fontWeight: 700, textDecoration: 'none' }}>
            View All History →
          </Link>
        </div>

        <div style={{ overflowX: 'auto' }}>
          <table style={{ width: '100%', borderCollapse: 'collapse', fontSize: '0.88rem' }}>
            <thead>
              <tr style={{ backgroundColor: '#f8fafc', borderBottom: '1px solid var(--border-color)', textAlign: 'left' }}>
                <th style={{ padding: '12px 18px', fontWeight: 700, color: '#475569' }}>Date</th>
                <th style={{ padding: '12px 18px', fontWeight: 700, color: '#475569' }}>Status</th>
                <th style={{ padding: '12px 18px', fontWeight: 700, color: '#475569' }}>Notes</th>
                <th style={{ padding: '12px 18px', fontWeight: 700, color: '#475569', textAlign: 'right' }}>Actions</th>
              </tr>
            </thead>
            <tbody>
              {data.recentDates.slice(0, 6).map(row => (
                <tr key={row.price_date} style={{ borderBottom: '1px solid var(--border-color)' }}>
                  <td style={{ padding: '12px 18px', fontWeight: 700, color: '#0f172a' }}>
                    {formatDateLong(row.price_date)}
                  </td>
                  <td style={{ padding: '12px 18px' }}>
                    {row.is_published === 1 ? (
                      <span className="badge badge-published">● Live</span>
                    ) : (
                      <span className="badge badge-draft">● Draft</span>
                    )}
                  </td>
                  <td style={{ padding: '12px 18px', color: '#64748b' }}>
                    {row.notes || '—'}
                  </td>
                  <td style={{ padding: '12px 18px', textAlign: 'right' }}>
                    <Link
                      href={`/admin/prices?date=${row.price_date}`}
                      className="dg-btn dg-btn-secondary"
                      style={{ padding: '5px 12px', fontSize: '0.8rem' }}
                    >
                      Open Sheet
                    </Link>
                  </td>
                </tr>
              ))}
              {data.recentDates.length === 0 && (
                <tr>
                  <td colSpan={4} style={{ padding: '30px', textAlign: 'center', color: '#94a3b8' }}>
                    No previous price sheets recorded yet.
                  </td>
                </tr>
              )}
            </tbody>
          </table>
        </div>
      </div>
    </div>
  );
}
