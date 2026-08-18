import { Metadata } from 'next';
import { ContactForm } from '@/components/ContactForm';
import { FAQAccordions } from '@/components/FAQAccordions';

export const metadata: Metadata = {
  title: 'Contact DailyGurus - Koyambedu Wholesale Market Chennai',
  description:
    'Get in touch with the DailyGurus market desk. Market location, phone, email, and wholesale produce rate inquiries.',
};

export default function ContactPage() {
  return (
    <>
      {/* Page Hero */}
      <section className="page-hero">
        <div className="container">
          <h1 className="page-hero-title">Contact Us</h1>
          <p className="page-hero-desc">
            Have questions about today&apos;s mandi auction prices, bulk procurement, or produce listings? Reach out to our
            Koyambedu Market desk.
          </p>
        </div>
      </section>

      {/* Contact Body */}
      <section className="page-body-section">
        <div className="container">
          <div
            style={{
              display: 'grid',
              gridTemplateColumns: 'repeat(auto-fit, minmax(320px, 1fr))',
              gap: '36px',
              maxWidth: '1100px',
              margin: '0 auto',
            }}
          >
            {/* Left: Info Cards & Market Hours */}
            <div>
              <div
                style={{
                  background: '#FFFFFF',
                  border: '1px solid var(--color-border)',
                  borderRadius: 'var(--radius-lg)',
                  padding: '28px',
                  boxShadow: 'var(--shadow-sm)',
                  marginBottom: '24px',
                }}
              >
                <h3
                  style={{
                    fontSize: '1.3rem',
                    fontWeight: 800,
                    color: 'var(--color-text-main)',
                    marginBottom: '20px',
                  }}
                >
                  Market Desk Info
                </h3>

                {/* Address */}
                <div className="contact-item" style={{ marginBottom: '20px' }}>
                  <div className="contact-icon" style={{ color: 'var(--color-primary)' }}>
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2">
                      <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                      <circle cx="12" cy="10" r="3"></circle>
                    </svg>
                  </div>
                  <div className="contact-text">
                    <strong style={{ color: 'var(--color-text-main)', fontSize: '15px' }}>
                      Koyambedu Wholesale Market Complex
                    </strong>
                    <span style={{ color: 'var(--color-text-muted)' }}>
                      Vegetable Complex, Gate No. 2, Koyambedu, Chennai - 600092
                    </span>
                  </div>
                </div>

                {/* Phone */}
                <div className="contact-item" style={{ marginBottom: '20px' }}>
                  <div className="contact-icon" style={{ color: 'var(--color-primary)' }}>
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2">
                      <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path>
                    </svg>
                  </div>
                  <div className="contact-text">
                    <a
                      href="tel:+919876543210"
                      style={{ color: 'var(--color-primary-dark)', fontWeight: 700, fontSize: '15px' }}
                    >
                      +91 98765 43210
                    </a>
                    <span style={{ color: 'var(--color-text-muted)' }}>Mon – Sun: 4:30 AM to 8:00 PM</span>
                  </div>
                </div>

                {/* Email */}
                <div className="contact-item">
                  <div className="contact-icon" style={{ color: 'var(--color-primary)' }}>
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2">
                      <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                      <polyline points="22,6 12,13 2,6"></polyline>
                    </svg>
                  </div>
                  <div className="contact-text">
                    <a
                      href="mailto:contact@dailygurus.com"
                      style={{ color: 'var(--color-primary-dark)', fontWeight: 700, fontSize: '15px' }}
                    >
                      contact@dailygurus.com
                    </a>
                    <span style={{ color: 'var(--color-text-muted)' }}>
                      For general inquiries &amp; merchant partnerships
                    </span>
                  </div>
                </div>
              </div>

              {/* Mandi Trading Schedule */}
              <div
                style={{
                  background: 'var(--color-primary-subtle)',
                  border: '1px solid var(--color-primary-border)',
                  borderRadius: 'var(--radius-lg)',
                  padding: '24px',
                }}
              >
                <h4
                  style={{
                    fontSize: '1.1rem',
                    fontWeight: 700,
                    color: 'var(--color-primary-dark)',
                    marginBottom: '12px',
                    display: 'flex',
                    alignItems: 'center',
                    gap: '8px',
                  }}
                >
                  <span>⏰</span> Mandi Trading Schedule
                </h4>
                <ul
                  style={{
                    display: 'flex',
                    flexDirection: 'column',
                    gap: '8px',
                    fontSize: '13.5px',
                    color: 'var(--color-text-body)',
                  }}
                >
                  <li style={{ display: 'flex', justifyContent: 'space-between' }}>
                    <strong>Wholesale Mandi Auction:</strong>
                    <span>4:00 AM – 10:00 AM</span>
                  </li>
                  <li style={{ display: 'flex', justifyContent: 'space-between' }}>
                    <strong>Semi-Wholesale Trading:</strong>
                    <span>8:00 AM – 1:00 PM</span>
                  </li>
                  <li style={{ display: 'flex', justifyContent: 'space-between' }}>
                    <strong>Rates Online Publishing:</strong>
                    <span style={{ color: 'var(--color-primary-dark)', fontWeight: 700 }}>5:00 AM Daily</span>
                  </li>
                </ul>
              </div>
            </div>

            {/* Right: Inquiry Form */}
            <ContactForm />
          </div>

          {/* FAQ Section */}
          <FAQAccordions />
        </div>
      </section>
    </>
  );
}
