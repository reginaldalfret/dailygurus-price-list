'use client';

import React, { useState } from 'react';

interface FAQItem {
  id: string;
  question: string;
  answer: string;
}

const FAQS: FAQItem[] = [
  {
    id: 'faq-1',
    question: 'What time are the daily wholesale prices updated?',
    answer:
      'Our market desk begins monitoring opening transactions at Koyambedu Market at 4:00 AM. Prices are compiled, verified with mandi commission agents, and published by 5:00 AM every morning.',
  },
  {
    id: 'faq-2',
    question: 'What do price ranges like ₹1,900 / ₹1,950 mean?',
    answer:
      'In wholesale auctions, prices vary according to grade, freshness, and consignment size. When a range is listed (e.g. ₹1,900 / ₹1,950 per 50 kg), the lower value represents standard grade produce, and the higher value represents premium export-grade produce.',
  },
  {
    id: 'faq-3',
    question: 'Are these prices wholesale or retail?',
    answer:
      'All prices listed on DailyGurus are strictly wholesale mandi rates (crates, 50kg sacks, boxes, or bulk bundles). Local retail neighborhood rates are typically 25% to 50% higher to account for sorting, transport, spoilage, and retail markup.',
  },
];

export const FAQAccordions: React.FC = () => {
  const [openFaqs, setOpenFaqs] = useState<Record<string, boolean>>({
    'faq-1': true,
  });

  const toggleFaq = (id: string) => {
    setOpenFaqs(prev => ({
      ...prev,
      [id]: !prev[id],
    }));
  };

  return (
    <div style={{ maxWidth: '900px', margin: '60px auto 0 auto' }}>
      <h3
        style={{
          fontSize: '1.5rem',
          fontWeight: 800,
          color: 'var(--color-text-main)',
          textAlign: 'center',
          marginBottom: '24px',
        }}
      >
        Frequently Asked Questions
      </h3>

      <div className="accordions-list">
        {FAQS.map(faq => {
          const isOpen = !!openFaqs[faq.id];
          return (
            <div key={faq.id} className={`accordion-card ${isOpen ? 'is-open' : ''}`}>
              <button
                className="accordion-header"
                type="button"
                aria-expanded={isOpen}
                onClick={() => toggleFaq(faq.id)}
              >
                <div className="accordion-title-wrap">
                  <span className="accordion-title">{faq.question}</span>
                </div>
                <div className="accordion-chevron">
                  <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2.5">
                    <polyline points="6 9 12 15 18 9"></polyline>
                  </svg>
                </div>
              </button>
              <div
                className="accordion-content"
                style={{
                  padding: '16px 20px',
                  fontSize: '14.5px',
                  color: 'var(--color-text-body)',
                  lineHeight: '1.6',
                  display: isOpen ? 'block' : 'none',
                }}
              >
                {faq.answer}
              </div>
            </div>
          );
        })}
      </div>
    </div>
  );
};
