'use client';

import React, { useState, useEffect } from 'react';
import Link from 'next/link';
import { usePathname } from 'next/navigation';

export const Header: React.FC = () => {
  const pathname = usePathname();
  const [drawerOpen, setDrawerOpen] = useState(false);
  const [isScrolled, setIsScrolled] = useState(false);

  useEffect(() => {
    const handleScroll = () => {
      setIsScrolled(window.scrollY > 20);
    };
    window.addEventListener('scroll', handleScroll, { passive: true });
    return () => window.removeEventListener('scroll', handleScroll);
  }, []);

  // Close drawer on route change
  useEffect(() => {
    setDrawerOpen(false);
  }, [pathname]);

  // Lock body scroll when drawer is open
  useEffect(() => {
    if (drawerOpen) {
      document.body.style.overflow = 'hidden';
    } else {
      document.body.style.overflow = '';
    }
    return () => {
      document.body.style.overflow = '';
    };
  }, [drawerOpen]);

  return (
    <>
      {/* Top Announcement Bar */}
      <div className="topbar">
        <div className="container topbar-container">
          <div className="topbar-left">
            <span className="topbar-badge">📍 Koyambedu Wholesale Market</span>
            <span className="topbar-text">Chennai&apos;s Official Daily Mandi Rates</span>
          </div>
          <div className="topbar-right">
            <a href="tel:+919876543210" className="topbar-link">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2">
                <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path>
              </svg>
              +91 98765 43210
            </a>
            <span className="topbar-divider">|</span>
            <Link href="/admin" className="topbar-link topbar-admin-link">
              <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2">
                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
              </svg>
              Merchant Login
            </Link>
          </div>
        </div>
      </div>

      {/* Main Navigation Header */}
      <header className={`site-header ${isScrolled ? 'is-scrolled' : ''}`} id="siteHeader">
        <div className="container header-container">
          {/* Brand Logo */}
          <Link href="/" className="header-logo" aria-label="DailyGurus Price List Home">
            <img
              src="/assets/images/logo.svg"
              alt="DailyGurus Price List"
              width="220"
              height="48"
              className="logo-img"
            />
          </Link>

          {/* Desktop Navigation Links */}
          <nav className="desktop-nav" aria-label="Main Navigation">
            <ul className="nav-menu">
              <li className="nav-item">
                <Link href="/" className={`nav-link ${pathname === '/' ? 'active' : ''}`}>
                  Home
                </Link>
              </li>
              <li className="nav-item">
                <Link href="/#vegetables" className="nav-link">
                  Price List
                </Link>
              </li>
              <li className="nav-item">
                <Link href="/history" className={`nav-link ${pathname === '/history' ? 'active' : ''}`}>
                  History
                </Link>
              </li>
              <li className="nav-item">
                <Link href="/about" className={`nav-link ${pathname === '/about' ? 'active' : ''}`}>
                  About Us
                </Link>
              </li>
            </ul>
          </nav>

          {/* Header Action Button */}
          <div className="header-actions">
            <Link
              href="/contact"
              className={`btn-contact-pill ${pathname === '/contact' ? 'active' : ''}`}
            >
              Contact Us
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2.5">
                <path d="M5 12h14M12 5l7 7-7 7" />
              </svg>
            </Link>

            {/* Mobile Hamburger Button (44x44px touch target) */}
            <button
              className="mobile-menu-btn"
              id="mobileMenuBtn"
              aria-label="Open mobile navigation menu"
              aria-expanded={drawerOpen}
              aria-controls="mobileNavDrawer"
              onClick={() => setDrawerOpen(true)}
            >
              <span className="hamburger-bar"></span>
              <span className="hamburger-bar"></span>
              <span className="hamburger-bar"></span>
            </button>
          </div>
        </div>
      </header>

      {/* Mobile Slide-out Drawer & Overlay */}
      <div
        className={`mobile-overlay ${drawerOpen ? 'is-active' : ''}`}
        id="mobileOverlay"
        onClick={() => setDrawerOpen(false)}
      />
      <aside
        className={`mobile-nav-drawer ${drawerOpen ? 'is-open' : ''}`}
        id="mobileNavDrawer"
        aria-hidden={!drawerOpen}
      >
        <div className="drawer-header">
          <Link href="/" className="drawer-logo" onClick={() => setDrawerOpen(false)}>
            <img src="/assets/images/logo.svg" alt="DailyGurus Logo" width="170" height="38" />
          </Link>
          <button
            className="drawer-close-btn"
            id="drawerCloseBtn"
            aria-label="Close navigation menu"
            onClick={() => setDrawerOpen(false)}
          >
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2">
              <line x1="18" y1="6" x2="6" y2="18"></line>
              <line x1="6" y1="6" x2="18" y2="18"></line>
            </svg>
          </button>
        </div>
        <nav className="mobile-nav-menu">
          <ul>
            <li>
              <Link
                href="/"
                className={`mobile-nav-link ${pathname === '/' ? 'active' : ''}`}
                onClick={() => setDrawerOpen(false)}
              >
                <span className="nav-icon">🏠</span> Home
              </Link>
            </li>
            <li>
              <Link
                href="/#vegetables"
                className="mobile-nav-link"
                onClick={() => setDrawerOpen(false)}
              >
                <span className="nav-icon">🥦</span> Vegetables Price List
              </Link>
            </li>
            <li>
              <Link
                href="/#fruits"
                className="mobile-nav-link"
                onClick={() => setDrawerOpen(false)}
              >
                <span className="nav-icon">🍎</span> Fruits Price List
              </Link>
            </li>
            <li>
              <Link
                href="/history"
                className={`mobile-nav-link ${pathname === '/history' ? 'active' : ''}`}
                onClick={() => setDrawerOpen(false)}
              >
                <span className="nav-icon">📅</span> Historical Rates
              </Link>
            </li>
            <li>
              <Link
                href="/about"
                className={`mobile-nav-link ${pathname === '/about' ? 'active' : ''}`}
                onClick={() => setDrawerOpen(false)}
              >
                <span className="nav-icon">ℹ️</span> About Us
              </Link>
            </li>
            <li>
              <Link
                href="/contact"
                className={`mobile-nav-link ${pathname === '/contact' ? 'active' : ''}`}
                onClick={() => setDrawerOpen(false)}
              >
                <span className="nav-icon">📞</span> Contact Us
              </Link>
            </li>
          </ul>
        </nav>
        <div className="drawer-footer">
          <div className="drawer-market-status">
            <span className="status-indicator-dot"></span>
            <span>Market Open: 4:00 AM - 12:00 PM</span>
          </div>
          <Link
            href="/admin"
            className="drawer-admin-btn"
            onClick={() => setDrawerOpen(false)}
          >
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2">
              <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
              <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
            </svg>
            Merchant / Admin Login
          </Link>
        </div>
      </aside>
    </>
  );
};
