import { useEffect } from 'react';
import { useNavigate } from 'react-router-dom';
import { pickNeighbour, type Box, type Direction } from './spatialNav';

const FOCUSABLE = 'button:not([disabled]), a[href], input:not([disabled]), select, textarea, [data-tv]';

const KEY_TO_DIRECTION: Record<string, Direction> = {
  ArrowUp: 'up',
  ArrowDown: 'down',
  ArrowLeft: 'left',
  ArrowRight: 'right',
};

const toBox = (rect: DOMRect): Box => ({ left: rect.left, top: rect.top, width: rect.width, height: rect.height });

/** Visible, enabled controls on the current page (inactive Ionic pages are skipped). */
function focusables(): HTMLElement[] {
  return Array.from(document.querySelectorAll<HTMLElement>(FOCUSABLE)).filter((element) => {
    if (element.closest('.ion-page-hidden, [inert], [aria-hidden="true"]')) return false;
    const rect = element.getBoundingClientRect();
    return rect.width > 0 && rect.height > 0;
  });
}

function isTextEntry(element: Element | null): boolean {
  return element instanceof HTMLInputElement
    || element instanceof HTMLTextAreaElement
    || element instanceof HTMLSelectElement;
}

/** Moves focus to the neighbour in `direction`. Returns false when there is none. */
export function moveFocus(direction: Direction): boolean {
  const items = focusables();
  const active = document.activeElement as HTMLElement | null;

  if (!active || !items.includes(active)) {
    items[0]?.focus({ preventScroll: true });
    return items.length > 0;
  }

  const others = items.filter((element) => element !== active);
  const index = pickNeighbour(
    toBox(active.getBoundingClientRect()),
    others.map((element) => toBox(element.getBoundingClientRect())),
    direction,
  );
  if (index < 0) return false;

  const next = others[index];
  next.focus({ preventScroll: true });
  next.scrollIntoView({ block: 'nearest', inline: 'nearest', behavior: 'smooth' });
  return true;
}

/** Wires the remote's D-pad, Enter and Back keys to the page. */
export function useTvNavigation(): void {
  const navigate = useNavigate();

  useEffect(() => {
    const onKeyDown = (event: KeyboardEvent) => {
      const active = document.activeElement;
      const direction = KEY_TO_DIRECTION[event.key];

      if (direction) {
        // Left/right move the caret inside text fields.
        if (isTextEntry(active) && (direction === 'left' || direction === 'right')) return;
        event.preventDefault();
        moveFocus(direction);
        return;
      }

      if (event.key === 'Enter' && active instanceof HTMLElement && active.hasAttribute('data-tv')
        && !['BUTTON', 'A', 'INPUT', 'TEXTAREA', 'SELECT'].includes(active.tagName)) {
        event.preventDefault();
        active.click();
        return;
      }

      if ((event.key === 'Escape' || event.key === 'Backspace') && !isTextEntry(active)) {
        if (window.history.length > 1) {
          event.preventDefault();
          navigate(-1);
        }
      }
    };

    window.addEventListener('keydown', onKeyDown);
    return () => window.removeEventListener('keydown', onKeyDown);
  }, [navigate]);
}
