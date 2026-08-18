import { Metadata } from 'next';
import Link from 'next/link';

export const metadata: Metadata = {
  title: 'About DailyGurus - Daily Wholesale Produce Market Intelligence',
  description:
    'Learn about DailyGurus, Chennai\'s leading wholesale market price tracking platform providing daily transparent rates for vegetables and fruits directly from Koyambedu Mandi.',
};

export default function AboutPage() {
  return (
    <>
      {/* Page Hero */}
      <section className="page-hero">
        <div className="container">
          <h1 className="page-hero-title">About DailyGurus</h1>
          <p className="page-hero-desc">
            Empowering traders, restaurants, retailers, and consumers with accurate, transparent daily wholesale market
            prices from Koyambedu, Chennai.
          </p>
        </div>
      </section>

      {/* About Main Content */}
      <section className="page-body-section">
        <div className="container">
          <div style={{ maxWidth: '900px', margin: '0 auto' }}>
            {/* Mission Card */}
            <div
              style={{
                background: '#FFFFFF',
                border: '1px solid var(--color-border)',
                borderRadius: 'var(--radius-lg)',
                padding: '36px',
                boxShadow: 'var(--shadow-sm)',
                marginBottom: '36px',
              }}
            >
              <span
                style={{
                  fontSize: '12px',
                  fontWeight: 700,
                  textTransform: 'uppercase',
                  letterSpacing: '0.05em',
                  color: 'var(--color-primary)',
                }}
              >
                Our Purpose
              </span>
              <h2
                style={{
                  fontSize: '1.8rem',
                  fontWeight: 800,
                  color: 'var(--color-primary-dark)',
                  margin: '8px 0 16px 0',
                }}
              >
                Democratizing Wholesale Market Prices
              </h2>
              <p
                style={{
                  fontSize: '16px',
                  color: 'var(--color-text-body)',
                  lineHeight: '1.7',
                  marginBottom: '16px',
                }}
              >
                Koyambedu Wholesale Market in Chennai is one of Asia&apos;s largest perishable goods markets, handling
                thousands of metric tonnes of fresh vegetables and fruits every morning. However, volatile auction
                prices and lack of real-time price dissemination often leave vendors, chefs, and retail buyers in the dark.
              </p>
              <p style={{ fontSize: '16px', color: 'var(--color-text-body)', lineHeight: '1.7' }}>
                <strong>DailyGurus Price List</strong> was established to bridge this information gap. By recording and
                publishing verified morning trading auction prices every single day, we enable market participants to buy
                with confidence, forecast costs accurately, and trade with total transparency.
              </p>
            </div>

            {/* Stats Bar (3 Cards) */}
            <div
              style={{
                display: 'grid',
                gridTemplateColumns: 'repeat(auto-fit, minmax(220px, 1fr))',
                gap: '20px',
                marginBottom: '40px',
              }}
            >
              <div
                style={{
                  background: '#FFFFFF',
                  border: '1px solid var(--color-border)',
                  borderRadius: 'var(--radius-lg)',
                  padding: '24px',
                  textAlign: 'center',
                  boxShadow: 'var(--shadow-card)',
                }}
              >
                <div style={{ fontSize: '2.2rem', fontWeight: 800, color: 'var(--color-primary)' }}>80+</div>
                <div
                  style={{
                    fontSize: '14px',
                    fontWeight: 600,
                    color: 'var(--color-text-muted)',
                    marginTop: '4px',
                  }}
                >
                  Daily Tracked Commodities
                </div>
              </div>
              <div
                style={{
                  background: '#FFFFFF',
                  border: '1px solid var(--color-border)',
                  borderRadius: 'var(--radius-lg)',
                  padding: '24px',
                  textAlign: 'center',
                  boxShadow: 'var(--shadow-card)',
                }}
              >
                <div style={{ fontSize: '2.2rem', fontWeight: 800, color: 'var(--color-fruit-primary)' }}>
                  5:00 AM
                </div>
                <div
                  style={{
                    fontSize: '14px',
                    fontWeight: 600,
                    color: 'var(--color-text-muted)',
                    marginTop: '4px',
                  }}
                >
                  Daily Auction Rates Published
                </div>
              </div>
              <div
                style={{
                  background: '#FFFFFF',
                  border: '1px solid var(--color-border)',
                  borderRadius: 'var(--radius-lg)',
                  padding: '24px',
                  textAlign: 'center',
                  boxShadow: 'var(--shadow-card)',
                }}
              >
                <div style={{ fontSize: '2.2rem', fontWeight: 800, color: 'var(--color-primary-dark)' }}>100%</div>
                <div
                  style={{
                    fontSize: '14px',
                    fontWeight: 600,
                    color: 'var(--color-text-muted)',
                    marginTop: '4px',
                  }}
                >
                  Free &amp; Open Access
                </div>
              </div>
            </div>

            {/* How We Operate (3 Steps) */}
            <div style={{ marginBottom: '40px' }}>
              <h3
                style={{
                  fontSize: '1.5rem',
                  fontWeight: 800,
                  color: 'var(--color-text-main)',
                  textAlign: 'center',
                  marginBottom: '24px',
                }}
              >
                How We Collect &amp; Verify Rates
              </h3>

              <div
                style={{
                  display: 'grid',
                  gridTemplateColumns: 'repeat(auto-fit, minmax(240px, 1fr))',
                  gap: '20px',
                }}
              >
                <div
                  style={{
                    background: '#FFFFFF',
                    border: '1px solid var(--color-border)',
                    borderRadius: 'var(--radius-lg)',
                    padding: '24px',
                  }}
                >
                  <div style={{ fontSize: '28px', marginBottom: '12px' }}>🚛</div>
                  <h4
                    style={{
                      fontSize: '1.1rem',
                      fontWeight: 700,
                      color: 'var(--color-text-main)',
                      marginBottom: '8px',
                    }}
                  >
                    1. Mandi Influx
                  </h4>
                  <p style={{ fontSize: '14px', color: 'var(--color-text-muted)', lineHeight: '1.6' }}>
                    Trucks and farm consignments arrive overnight from across Tamil Nadu, Karnataka, Andhra Pradesh, and
                    Maharashtra.
                  </p>
                </div>

                <div
                  style={{
                    background: '#FFFFFF',
                    border: '1px solid var(--color-border)',
                    borderRadius: 'var(--radius-lg)',
                    padding: '24px',
                  }}
                >
                  <div style={{ fontSize: '28px', marginBottom: '12px' }}>⚖️</div>
                  <h4
                    style={{
                      fontSize: '1.1rem',
                      fontWeight: 700,
                      color: 'var(--color-text-main)',
                      marginBottom: '8px',
                    }}
                  >
                    2. Auction Verification
                  </h4>
                  <p style={{ fontSize: '14px', color: 'var(--color-text-muted)', lineHeight: '1.6' }}>
                    Our ground representatives track opening auctions and wholesale transactions across tomato, onion,
                    potato, and fruit complexes.
                  </p>
                </div>

                <div
                  style={{
                    background: '#FFFFFF',
                    border: '1px solid var(--color-border)',
                    borderRadius: 'var(--radius-lg)',
                    padding: '24px',
                  }}
                >
                  <div style={{ fontSize: '28px', marginBottom: '12px' }}>📱</div>
                  <h4
                    style={{
                      fontSize: '1.1rem',
                      fontWeight: 700,
                      color: 'var(--color-text-main)',
                      marginBottom: '8px',
                    }}
                  >
                    3. Instant Broadcast
                  </h4>
                  <p style={{ fontSize: '14px', color: 'var(--color-text-muted)', lineHeight: '1.6' }}>
                    Verified prices with standardized packaging units (crates, bags, kg) are published online for
                    immediate access.
                  </p>
                </div>
              </div>
            </div>

            {/* Call to Action Card */}
            <div
              style={{
                background: 'linear-gradient(135deg, #0D4715 0%, #1B5E20 100%)',
                color: '#FFFFFF',
                borderRadius: 'var(--radius-lg)',
                padding: '36px',
                textAlign: 'center',
                boxShadow: 'var(--shadow-lg)',
              }}
            >
              <h3 style={{ fontSize: '1.6rem', fontWeight: 800, marginBottom: '10px' }}>
                Stay Ahead of Mandi Price Trends
              </h3>
              <p
                style={{
                  fontSize: '15px',
                  color: '#DCFCE7',
                  maxWidth: '540px',
                  margin: '0 auto 24px auto',
                }}
              >
                Bookmark DailyGurus or check in every morning at 5:00 AM for Chennai&apos;s official wholesale produce
                price benchmark.
              </p>
              <div
                style={{
                  display: 'flex',
                  justifyContent: 'center',
                  gap: '14px',
                  flexWrap: 'wrap',
                }}
              >
                <Link
                  href="/"
                  style={{
                    backgroundColor: '#FFFFFF',
                    color: '#0D4715',
                    fontWeight: 700,
                    padding: '10px 24px',
                    borderRadius: 'var(--radius-full)',
                  }}
                >
                  View Today&apos;s Prices &rarr;
                </Link>
                <Link
                  href="/contact"
                  style={{
                    background: 'rgba(255, 255, 255, 0.15)',
                    color: '#FFFFFF',
                    border: '1px solid rgba(255, 255, 255, 0.3)',
                    fontWeight: 600,
                    padding: '10px 24px',
                    borderRadius: 'var(--radius-full)',
                  }}
                >
                  Contact Market Team
                </Link>
              </div>
            </div>
          </div>
        </div>
      </section>
    </>
  );
}
