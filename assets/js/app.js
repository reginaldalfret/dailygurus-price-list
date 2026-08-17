/**
 * DailyGurus Price List - Core JavaScript Application
 * Handles instant search filtering, accordion toggling, mobile menu, and UI interactions
 */

document.addEventListener('DOMContentLoaded', () => {
  // 1. Mobile Menu Drawer Navigation
  initMobileDrawer();

  // 2. Accordions Expand / Collapse (Smart on mobile)
  initAccordions();

  // 3. Section Toggles (Expand All / Collapse All)
  initSectionToggles();

  // 4. Sticky Category Jump Navigation
  initStickyCategoryBar();

  // 5. Live Client-Side Search & Filtering
  initLiveSearch();

  // 6. Sticky Header & Back to Top
  initScrollBehaviors();
});

/**
 * Mobile Navigation Drawer Toggle
 */
function initMobileDrawer() {
  const mobileMenuBtn = document.getElementById('mobileMenuBtn');
  const drawerCloseBtn = document.getElementById('drawerCloseBtn');
  const mobileNavDrawer = document.getElementById('mobileNavDrawer');
  const mobileOverlay = document.getElementById('mobileOverlay');
  const mobileNavLinks = document.querySelectorAll('.mobile-nav-link');

  if (!mobileMenuBtn || !mobileNavDrawer || !mobileOverlay) return;

  function openDrawer() {
    mobileNavDrawer.classList.add('is-open');
    mobileOverlay.classList.add('is-active');
    mobileMenuBtn.setAttribute('aria-expanded', 'true');
    mobileNavDrawer.setAttribute('aria-hidden', 'false');
    document.body.style.overflow = 'hidden';
  }

  function closeDrawer() {
    mobileNavDrawer.classList.remove('is-open');
    mobileOverlay.classList.remove('is-active');
    mobileMenuBtn.setAttribute('aria-expanded', 'false');
    mobileNavDrawer.setAttribute('aria-hidden', 'true');
    document.body.style.overflow = '';
  }

  mobileMenuBtn.addEventListener('click', openDrawer);
  if (drawerCloseBtn) drawerCloseBtn.addEventListener('click', closeDrawer);
  mobileOverlay.addEventListener('click', closeDrawer);

  mobileNavLinks.forEach(link => {
    link.addEventListener('click', () => {
      closeDrawer();
    });
  });

  // Handle escape key
  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && mobileNavDrawer.classList.contains('is-open')) {
      closeDrawer();
    }
  });
}

/**
 * Accordion Interactivity
 */
function initAccordions() {
  const accordionHeaders = document.querySelectorAll('.accordion-header');
  const isMobile = window.innerWidth <= 768;

  // On initial mobile page load, keep first 2 categories open and collapse the rest for effortless scanning
  if (isMobile) {
    const allCards = document.querySelectorAll('.accordion-card');
    allCards.forEach((card, index) => {
      // Keep only first 2 cards open on initial mobile view
      if (index > 1) {
        card.classList.remove('is-open');
        const header = card.querySelector('.accordion-header');
        if (header) header.setAttribute('aria-expanded', 'false');
      }
    });

    const vegToggleBtn = document.getElementById('toggleAllVegBtn');
    if (vegToggleBtn) {
      const btnText = vegToggleBtn.querySelector('.toggle-text');
      if (btnText) btnText.textContent = 'Expand All';
    }
  }

  accordionHeaders.forEach(header => {
    header.addEventListener('click', function(e) {
      // Prevent trigger if clicking a direct link inside
      if (e.target.tagName === 'A') return;

      const card = this.closest('.accordion-card');
      if (!card) return;

      const isOpen = card.classList.contains('is-open');
      if (isOpen) {
        card.classList.remove('is-open');
        this.setAttribute('aria-expanded', 'false');
      } else {
        card.classList.add('is-open');
        this.setAttribute('aria-expanded', 'true');
      }
    });
  });
}

/**
 * Expand All / Collapse All Section Toggles
 */
function initSectionToggles() {
  // Vegetables Expand All Toggle
  const vegToggleBtn = document.getElementById('toggleAllVegBtn');
  if (vegToggleBtn) {
    vegToggleBtn.addEventListener('click', () => {
      const vegCards = document.querySelectorAll('#vegetablesAccordions .accordion-card');
      const isAnyClosed = Array.from(vegCards).some(c => !c.classList.contains('is-open'));

      vegCards.forEach(card => {
        const header = card.querySelector('.accordion-header');
        if (isAnyClosed) {
          card.classList.add('is-open');
          if (header) header.setAttribute('aria-expanded', 'true');
        } else {
          card.classList.remove('is-open');
          if (header) header.setAttribute('aria-expanded', 'false');
        }
      });

      const btnText = vegToggleBtn.querySelector('.toggle-text');
      if (btnText) {
        btnText.textContent = isAnyClosed ? 'Collapse All' : 'Expand All';
      }
    });
  }

  // Fruits Expand All Toggle
  const fruitToggleBtn = document.getElementById('toggleAllFruitBtn');
  if (fruitToggleBtn) {
    fruitToggleBtn.addEventListener('click', () => {
      const fruitCards = document.querySelectorAll('#fruitsAccordions .accordion-card');
      const isAnyClosed = Array.from(fruitCards).some(c => !c.classList.contains('is-open'));

      fruitCards.forEach(card => {
        const header = card.querySelector('.accordion-header');
        if (isAnyClosed) {
          card.classList.add('is-open');
          if (header) header.setAttribute('aria-expanded', 'true');
        } else {
          card.classList.remove('is-open');
          if (header) header.setAttribute('aria-expanded', 'false');
        }
      });

      const btnText = fruitToggleBtn.querySelector('.toggle-text');
      if (btnText) {
        btnText.textContent = isAnyClosed ? 'Collapse Fruit List' : 'View Full Fruit Price List →';
      }
    });
  }
}

/**
 * Sticky Category Jump Navigation & Scroll Tracking
 */
function initStickyCategoryBar() {
  const catPills = document.querySelectorAll('.cat-pill');
  if (!catPills.length) return;

  catPills.forEach(pill => {
    pill.addEventListener('click', function(e) {
      e.preventDefault();
      const targetId = this.dataset.target || this.getAttribute('href').replace('#', '');
      const targetEl = document.getElementById(targetId);

      if (!targetEl) return;

      // If targeting an accordion card, make sure it is open
      if (targetEl.classList.contains('accordion-card')) {
        targetEl.classList.add('is-open');
        const header = targetEl.querySelector('.accordion-header');
        if (header) header.setAttribute('aria-expanded', 'true');
      }

      // Calculate scroll offset accounting for sticky header & sticky nav bar
      const isMobile = window.innerWidth <= 768;
      const headerOffset = isMobile ? 115 : 130;
      const targetPos = targetEl.getBoundingClientRect().top + window.scrollY - headerOffset;

      window.scrollTo({
        top: targetPos,
        behavior: 'smooth'
      });

      // Update active pill
      catPills.forEach(p => p.classList.remove('active'));
      this.classList.add('active');

      // Scroll the active pill into view in the horizontal pill bar
      this.scrollIntoView({ behavior: 'smooth', inline: 'center', block: 'nearest' });
    });
  });
}

/**
 * Live Client-side Instant Search & Text Highlight
 */
function initLiveSearch() {
  const searchInput = document.getElementById('priceSearchInput');
  const searchClearBtn = document.getElementById('searchClearBtn');
  const searchCountBadge = document.getElementById('searchCountBadge');
  const noResultsBox = document.getElementById('noSearchResults');
  const allRows = document.querySelectorAll('.price-table tbody tr');
  const allCards = document.querySelectorAll('.accordion-card');

  if (!searchInput) return;

  // Store original HTML and text for clean highlight restoration
  allRows.forEach(row => {
    const enSpan = row.querySelector('.produce-name-en');
    const taSpan = row.querySelector('.produce-name-ta');
    if (enSpan && !enSpan.dataset.originalText) {
      enSpan.dataset.originalText = enSpan.textContent.trim();
    }
    if (taSpan && !taSpan.dataset.originalText) {
      taSpan.dataset.originalText = taSpan.textContent.trim();
    }
  });

  function performSearch() {
    const query = searchInput.value.trim().toLowerCase();

    if (searchClearBtn) {
      searchClearBtn.style.display = query.length > 0 ? 'flex' : 'none';
    }

    if (query === '') {
      // Reset all
      if (searchCountBadge) searchCountBadge.style.display = 'none';
      if (noResultsBox) noResultsBox.style.display = 'none';

      allRows.forEach(row => {
        row.style.display = '';
        const enSpan = row.querySelector('.produce-name-en');
        const taSpan = row.querySelector('.produce-name-ta');
        if (enSpan && enSpan.dataset.originalText) {
          enSpan.textContent = enSpan.dataset.originalText;
        }
        if (taSpan && taSpan.dataset.originalText) {
          taSpan.textContent = taSpan.dataset.originalText;
        }
      });

      allCards.forEach(card => {
        card.style.display = '';
      });

      return;
    }

    let totalMatches = 0;

    allCards.forEach(card => {
      const rows = card.querySelectorAll('.price-table tbody tr');
      let cardMatches = 0;

      rows.forEach(row => {
        const enSpan = row.querySelector('.produce-name-en');
        const taSpan = row.querySelector('.produce-name-ta');
        const priceCell = row.querySelector('.item-price-cell');
        
        const origEn = enSpan ? (enSpan.dataset.originalText || enSpan.textContent.trim()) : '';
        const origTa = taSpan ? (taSpan.dataset.originalText || taSpan.textContent.trim()) : '';
        const enLower = origEn.toLowerCase();
        const taLower = origTa.toLowerCase();
        const priceText = priceCell ? priceCell.textContent.toLowerCase() : '';

        const isMatch = enLower.includes(query) || taLower.includes(query) || priceText.includes(query);

        if (isMatch) {
          row.style.display = '';
          cardMatches++;
          totalMatches++;

          // Highlight matching text in English name
          if (enSpan && enLower.includes(query)) {
            const regex = new RegExp(`(${escapeRegex(query)})`, 'gi');
            enSpan.innerHTML = origEn.replace(regex, '<mark class="search-highlight">$1</mark>');
          } else if (enSpan) {
            enSpan.textContent = origEn;
          }

          // Highlight matching text in Tamil name
          if (taSpan && taLower.includes(query)) {
            const regex = new RegExp(`(${escapeRegex(query)})`, 'gi');
            taSpan.innerHTML = origTa.replace(regex, '<mark class="search-highlight">$1</mark>');
          } else if (taSpan) {
            taSpan.textContent = origTa;
          }
        } else {
          row.style.display = 'none';
          if (enSpan) enSpan.textContent = origEn;
          if (taSpan) taSpan.textContent = origTa;
        }
      });

      // If card has matching items, show & expand it. Otherwise hide it.
      if (cardMatches > 0) {
        card.style.display = '';
        card.classList.add('is-open');
        const header = card.querySelector('.accordion-header');
        if (header) header.setAttribute('aria-expanded', 'true');
      } else {
        card.style.display = 'none';
      }
    });

    // Update count badge
    if (searchCountBadge) {
      searchCountBadge.style.display = 'inline-block';
      searchCountBadge.textContent = `${totalMatches} ${totalMatches === 1 ? 'item' : 'items'} found`;
    }

    // Toggle No Results
    if (noResultsBox) {
      noResultsBox.style.display = totalMatches === 0 ? 'block' : 'none';
      const termSpan = document.getElementById('searchTermDisplay');
      if (termSpan) termSpan.textContent = query;
    }
  }

  searchInput.addEventListener('input', performSearch);

  if (searchClearBtn) {
    searchClearBtn.addEventListener('click', () => {
      searchInput.value = '';
      searchInput.focus();
      performSearch();
    });
  }

  // Keyboard shortcut '/' to focus search
  document.addEventListener('keydown', (e) => {
    if (e.key === '/' && document.activeElement !== searchInput && !['INPUT', 'TEXTAREA'].includes(document.activeElement.tagName)) {
      e.preventDefault();
      searchInput.focus();
      searchInput.select();
    }
  });
}

function escapeRegex(string) {
  return string.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
}

/**
 * Header Scroll & Back-to-Top Behaviors
 */
function initScrollBehaviors() {
  const header = document.getElementById('siteHeader');
  const backToTopBtn = document.getElementById('backToTopBtn');

  window.addEventListener('scroll', () => {
    const scrollPos = window.scrollY;

    if (header) {
      if (scrollPos > 10) {
        header.classList.add('is-scrolled');
      } else {
        header.classList.remove('is-scrolled');
      }
    }

    if (backToTopBtn) {
      if (scrollPos > 320) {
        backToTopBtn.classList.add('is-visible');
      } else {
        backToTopBtn.classList.remove('is-visible');
      }
    }
  }, { passive: true });

  if (backToTopBtn) {
    backToTopBtn.addEventListener('click', () => {
      window.scrollTo({
        top: 0,
        behavior: 'smooth'
      });
    });
  }
}
