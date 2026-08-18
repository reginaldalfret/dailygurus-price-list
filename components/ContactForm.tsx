'use client';

import React, { useState } from 'react';

export const ContactForm: React.FC = () => {
  const [name, setName] = useState('');
  const [phone, setPhone] = useState('');
  const [email, setEmail] = useState('');
  const [topic, setTopic] = useState('Vegetables Wholesale');
  const [message, setMessage] = useState('');
  const [submittedName, setSubmittedName] = useState('');
  const [submittedTopic, setSubmittedTopic] = useState('');
  const [isSuccess, setIsSuccess] = useState(false);
  const [errorMsg, setErrorMsg] = useState('');

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    if (!name.trim() || !phone.trim() || !message.trim()) {
      setErrorMsg('Please provide your name, phone number, and message.');
      return;
    }

    setErrorMsg('');
    setSubmittedName(name);
    setSubmittedTopic(topic);
    setIsSuccess(true);

    // Reset form fields
    setName('');
    setPhone('');
    setEmail('');
    setMessage('');
  };

  return (
    <div
      style={{
        background: '#FFFFFF',
        border: '1px solid var(--color-border)',
        borderRadius: 'var(--radius-lg)',
        padding: '32px',
        boxShadow: 'var(--shadow-sm)',
      }}
    >
      <h3 style={{ fontSize: '1.3rem', fontWeight: 800, color: 'var(--color-text-main)', marginBottom: '8px' }}>
        Send an Inquiry
      </h3>
      <p style={{ fontSize: '14px', color: 'var(--color-text-muted)', marginBottom: '24px' }}>
        Fill in the form below and our market representatives will get back to you.
      </p>

      {isSuccess && (
        <div className="alert-success">
          Thank you for reaching out, <strong>{submittedName}</strong>! Your message regarding &ldquo;{submittedTopic}&rdquo;
          has been received. Our market desk will get back to you shortly.
        </div>
      )}

      {errorMsg && (
        <div
          style={{
            backgroundColor: '#FEE2E2',
            border: '1px solid #FCA5A5',
            color: '#991B1B',
            padding: '14px 18px',
            borderRadius: 'var(--radius-md)',
            marginBottom: '20px',
            fontWeight: 600,
          }}
        >
          {errorMsg}
        </div>
      )}

      <form onSubmit={handleSubmit} className="form-grid">
        <div className="form-group">
          <label htmlFor="name" className="form-label">
            Your Name *
          </label>
          <input
            type="text"
            id="name"
            name="name"
            className="form-control"
            required
            value={name}
            onChange={e => setName(e.target.value)}
            placeholder="e.g. Ramesh Kumar"
          />
        </div>

        <div className="form-group">
          <label htmlFor="phone" className="form-label">
            Phone Number *
          </label>
          <input
            type="tel"
            id="phone"
            name="phone"
            className="form-control"
            required
            value={phone}
            onChange={e => setPhone(e.target.value)}
            placeholder="e.g. +91 98765 43210"
          />
        </div>

        <div className="form-group">
          <label htmlFor="email" className="form-label">
            Email Address (Optional)
          </label>
          <input
            type="email"
            id="email"
            name="email"
            className="form-control"
            value={email}
            onChange={e => setEmail(e.target.value)}
            placeholder="e.g. ramesh@example.com"
          />
        </div>

        <div className="form-group">
          <label htmlFor="topic" className="form-label">
            Subject / Produce Type
          </label>
          <select
            id="topic"
            name="topic"
            className="form-control"
            value={topic}
            onChange={e => setTopic(e.target.value)}
          >
            <option value="Vegetables Wholesale">Vegetables Wholesale</option>
            <option value="Fruits Wholesale">Fruits Wholesale</option>
            <option value="Bulk Supply Procurement">Bulk Supply Procurement</option>
            <option value="Merchant Partnership">Merchant Partnership</option>
            <option value="Price Discrepancy / Feedback">Price Discrepancy / Feedback</option>
            <option value="Other">Other</option>
          </select>
        </div>

        <div className="form-group full-width">
          <label htmlFor="message" className="form-label">
            Your Message *
          </label>
          <textarea
            id="message"
            name="message"
            className="form-control"
            required
            rows={4}
            value={message}
            onChange={e => setMessage(e.target.value)}
            placeholder="Tell us about the produce items, quantity, or questions you have..."
          ></textarea>
        </div>

        <div className="form-group full-width" style={{ marginTop: '10px' }}>
          <button type="submit" className="btn-primary-submit">
            <span>Submit Message</span>
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2.5">
              <line x1="22" y1="2" x2="11" y2="13"></line>
              <polygon points="22 2 15 22 11 13 2 9 22 2"></polygon>
            </svg>
          </button>
        </div>
      </form>
    </div>
  );
};
