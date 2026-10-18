import { useEffect } from 'react';
import { useLocation } from 'react-router-dom';
import { useTvNavigation } from './useTvNavigation';

const PAGE = '.tv-page:not(.ion-page-hidden)';

/** Priority list: an explicit autofocus target, then content tiles, then inputs, then buttons. */
const AUTOFOCUS_SELECTORS = [
  `${PAGE} [data-tv-autofocus]`,
  `${PAGE} [data-tv]`,
  `${PAGE} input`,
  `${PAGE} button`,
];

export function TvNavigationRoot(): null {
  useTvNavigation();
  const { pathname } = useLocation();

  useEffect(() => {
    const timer = window.setTimeout(() => {
      const page = document.querySelector(PAGE);
      if (page?.contains(document.activeElement)) return;

      for (const selector of AUTOFOCUS_SELECTORS) {
        const target = document.querySelector<HTMLElement>(selector);
        if (target) {
          target.focus({ preventScroll: true });
          return;
        }
      }
    }, 120);

    return () => window.clearTimeout(timer);
  }, [pathname]);

  return null;
}
