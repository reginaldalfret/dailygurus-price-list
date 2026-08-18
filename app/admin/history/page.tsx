'use client';

import React, { useState, useEffect, useMemo } from 'react';
import Link from 'next/link';
import { formatDateLong } from '@/lib/price-formatter';

interface PriceDateRecord {
  price_date: string;
  is_published: number;
  notes: string;
  updated_at: string;
}

export default function AdminHistoryPage() {
  const [historyDates, setHistoryDates] = useState<PriceDateRecord[]>([]);
  const [loading, setLoading] = useState(true);
  const [searchQuery, setSearchQuery] = useState('');
  const [statusFilter, setStatusFilter] = useState<'all' | 'published' | 'draft'>('all');

  const todayStr = new Date().toISOString().split('T')[0];

  useEffect(() => {
    async function loadHistory() {
      setLoading(true);
      try {
        const res = await fetch(`/api/admin/prices?date=${todayStr}`);
        if (res.status === 401) {
          window.location.href = '/admin';
          return;
        }

        const json = await res.json();
        setHistoryDates(json.recentDates || []);
      } catch (err) {
        console.error('Error loading history:', err);
      } finally {
        setLoading(false);
      }
    }

    loadHistory();
  }, [todayStr]);

  const filteredHistory = useMemo(() => {
    return historyDates.filter(item => {
      // Status filter
      if (statusFilter === 'published' && item.is_published !== 1) return false;
      if (statusFilter === 'draft' && item.is_published === 1) return false;

      // Search Query
      if (!searchQuery.trim()) return true;
      const q = searchQuery.toLowerCase();
      return item.price_date.includes(q) || (item.notes || '').toLowerCase().includes(q);
    });
  }, [historyDates, statusFilter, searchQuery]);

  return (
    <div className="admin-container">
      {/* Header */}
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
            Price Sheet Archives & History
          </h1>
          <p style={{ color: '#64748b', fontSize: '0.92rem' }}>
            View, audit, manage, and duplicate historical daily wholesale price sheets
          </p>
        </div>

        <Link href={`/admin/prices?date=${todayStr}`} className="dg-btn dg-btn-primary">
          <span>⚡</span>
          <span>Today&apos;s Active Price Sheet</span>
        </Link>
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
        <div style={{ display: 'flex', gap: '8px', flexWrap: 'wrap' }}>
          <button
            type="button"
            onClick={() => setStatusFilter('all')}
            className={`dg-btn ${statusFilter === 'all' ? 'dg-btn-primary' : 'dg-btn-outline'}`}
            style={{ padding: '6px 14px', fontSize: '0.84rem' }}
          >
            All Archives ({historyDates.length})
          </button>
          <button
            type="button"
            onClick={() => setStatusFilter('published')}
            className={`dg-btn ${statusFilter === 'published' ? 'dg-btn-primary' : 'dg-btn-outline'}`}
            style={{ padding: '6px 14px', fontSize: '0.84rem' }}
          >
            🚀 Published Live ({historyDates.filter(h => h.is_published === 1).length})
          </button>
          <button
            type="button"
            onClick={() => setStatusFilter('draft')}
            className={`dg-btn ${statusFilter === 'draft' ? 'dg-btn-primary' : 'dg-btn-outline'}`}
            style={{ padding: '6px 14px', fontSize: '0.84rem' }}
          >
            📝 Drafts ({historyDates.filter(h => h.is_published !== 1).length})
          </button>
        </div>

        <div style={{ maxWidth: '300px', width: '100%' }}>
          <input
            type="text"
            className="dg-input"
            placeholder="🔍 Search date or notes..."
            value={searchQuery}
            onChange={e => setSearchQuery(e.target.value)}
          />
        </div>
      </div>

      {/* History Table */}
      <div className="dg-card" style={{ overflow: 'hidden' }}>
        {loading ? (
          <div style={{ textAlign: 'center', padding: '60px 20px', color: '#64748b' }}>
            <div style={{ fontSize: '1.8rem', marginBottom: '10px' }}>⏳</div>
            <p>Loading historical price records...</p>
          </div>
        ) : (
          <div style={{ overflowX: 'auto' }}>
            <table style={{ width: '100%', borderCollapse: 'collapse', fontSize: '0.9rem' }}>
              <thead>
                <tr style={{ backgroundColor: '#f8fafc', borderBottom: '1px solid var(--border-color)', textAlign: 'left' }}>
                  <th style={{ padding: '14px 18px', fontWeight: 700, color: '#475569' }}>Market Price Date</th>
                  <th style={{ padding: '14px 18px', fontWeight: 700, color: '#475569', width: '140px' }}>Publish Status</th>
                  <th style={{ padding: '14px 18px', fontWeight: 700, color: '#475569' }}>Notes / Description</th>
                  <th style={{ padding: '14px 18px', fontWeight: 700, color: '#475569' }}>Last Updated</th>
                  <th style={{ padding: '14px 18px', fontWeight: 700, color: '#475569', textAlign: 'right' }}>Actions</th>
                </tr>
              </thead>
              <tbody>
                {filteredHistory.map(row => (
                  <tr key={row.price_date} style={{ borderBottom: '1px solid var(--border-color)' }}>
                    <td style={{ padding: '14px 18px' }}>
                      <div style={{ fontWeight: 800, color: '#0f172a' }}>
                        {formatDateLong(row.price_date)}
                      </div>
                      <div style={{ fontSize: '0.78rem', color: '#64748b', fontFamily: 'monospace' }}>
                        {row.price_date}
                      </div>
                    </td>

                    <td style={{ padding: '14px 18px' }}>
                      {row.is_published === 1 ? (
                        <span className="badge badge-published">● Live Online</span>
                      ) : (
                        <span className="badge badge-draft">● Draft</span>
                      )}
                    </td>

                    <td style={{ padding: '14px 18px', color: '#475569' }}>
                      {row.notes || <span style={{ color: '#cbd5e1' }}>—</span>}
                    </td>

                    <td style={{ padding: '14px 18px', fontSize: '0.82rem', color: '#64748b' }}>
                      {row.updated_at ? new Date(row.updated_at).toLocaleString('en-IN') : '—'}
                    </td>

                    <td style={{ padding: '14px 18px', textAlign: 'right' }}>
                      <div style={{ display: 'inline-flex', gap: '8px' }}>
                        <Link
                          href={`/admin/prices?date=${row.price_date}`}
                          className="dg-btn dg-btn-secondary"
                          style={{ padding: '5px 12px', fontSize: '0.8rem' }}
                        >
                          ✏️ Edit Sheet
                        </Link>

                        <Link
                          href={`/admin/prices?date=${todayStr}&copy_from_date=${row.price_date}`}
                          className="dg-btn dg-btn-outline"
                          style={{ padding: '5px 12px', fontSize: '0.8rem' }}
                          title="Copy prices from this date into today's price sheet"
                        >
                          📋 Copy to Today
                        </Link>
                      </div>
                    </td>
                  </tr>
                ))}

                {filteredHistory.length === 0 && (
                  <tr>
                    <td colSpan={5} style={{ padding: '40px', textAlign: 'center', color: '#94a3b8' }}>
                      No historical price records found.
                    </td>
                  </tr>
                )}
              </tbody>
            </table>
          </div>
        )}
      </div>
    </div>
  );
}
