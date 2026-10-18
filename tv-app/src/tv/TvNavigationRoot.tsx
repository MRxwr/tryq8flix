import { useEffect } from 'react';
import { useLocation } from 'react-router-dom';
import { useTvNavigation } from './useTvNavigation';

const PAGE = '.tv-page:not(.ion-page-hidden)';

/** Priority list: an explicit autofocus target, then content tiles, then inputs, then buttons. */
const AUTOFOCUS_SELECTORS = [
  '[data-tv-autofocus]',
  '[data-tv]',
  'input',
  'button',
];

/** The newest page is the last one in the outlet; older pages linger during transitions. */
function activePage(): HTMLElement | null {
  const pages = Array.from(document.querySelectorAll<HTMLElement>(PAGE));
  return pages.length ? pages[pages.length - 1] : null;
}

function focusFirstControl(): boolean {
  const page = activePage();
  if (!page) return false;
  if (page.contains(document.activeElement)) return true;

  for (const selector of AUTOFOCUS_SELECTORS) {
    const target = Array.from(page.querySelectorAll<HTMLElement>(selector)).find((element) => {
      if (element.closest('[aria-hidden="true"], [inert]')) return false;
      const style = window.getComputedStyle(element);
      if (style.visibility === 'hidden' || style.display === 'none') return false;
      const rect = element.getBoundingClientRect();
      return rect.width > 0 && rect.height > 0;
    });
    if (target) {
      target.focus({ preventScroll: true });
      return true;
    }
  }
  return false;
}

export function TvNavigationRoot(): null {
  useTvNavigation();
  const { pathname } = useLocation();

  useEffect(() => {
    // Try right away, then again once the outgoing page has finished animating out.
    const timers = [0, 150, 400].map((delay) => window.setTimeout(focusFirstControl, delay));
    return () => timers.forEach((timer) => window.clearTimeout(timer));
  }, [pathname]);

  return null;
}
