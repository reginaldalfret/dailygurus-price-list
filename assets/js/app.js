/**
 * DailyGurus Price List - Core JavaScript Application
 * Handles instant search filtering, accordion toggling, mobile menu, and UI interactions
 */

document.addEventListener('DOMContentLoaded', () => {
  // 1. Mobile Menu Drawer Navigation
  initMobileDrawer();

  // 2. Accordions Expand / Collapse
  initAccordions();

  // 3. Section Toggles (Expand All / Collapse All)
  initSectionToggles();

  // 4. Live Client-Side Search & Filtering
  initLiveSearch();

  // 5. Sticky Header & Back to Top
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

  // Store original item texts for clean highlight restoration
  allRows.forEach(row => {
    const nameCell = row.querySelector('.item-name-cell');
    if (nameCell && !nameCell.dataset.originalText) {
      nameCell.dataset.originalText = nameCell.textContent.trim();
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
        const nameCell = row.querySelector('.item-name-cell');
        if (nameCell && nameCell.dataset.originalText) {
          nameCell.textContent = nameCell.dataset.originalText;
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
        const nameCell = row.querySelector('.item-name-cell');
        const priceCell = row.querySelector('.item-price-cell');
        const originalName = nameCell ? nameCell.dataset.originalText : '';
        const nameLower = originalName.toLowerCase();
        const priceText = priceCell ? priceCell.textContent.toLowerCase() : '';

        const isMatch = nameLower.includes(query) || priceText.includes(query);

        if (isMatch) {
          row.style.display = '';
          cardMatches++;
          totalMatches++;

          // Highlight matching text in name
          if (nameCell && nameLower.includes(query)) {
            const regex = new RegExp(`(${escapeRegex(query)})`, 'gi');
            nameCell.innerHTML = originalName.replace(regex, '<mark class="search-highlight">$1</mark>');
          } else if (nameCell) {
            nameCell.textContent = originalName;
          }
        } else {
          row.style.display = 'none';
          if (nameCell) nameCell.textContent = originalName;
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
