import { useEffect } from 'react';
import { useNavigate } from 'react-router-dom';
import { Capacitor } from '@capacitor/core';
import { App as CapacitorApp } from '@capacitor/app';
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
export function focusables(direction?: Direction, active?: HTMLElement | null): HTMLElement[] {
  const row = (direction === 'left' || direction === 'right')
    ? active?.closest<HTMLElement>('.row__track')
    : null;
  const root: ParentNode = row ?? document;

  return Array.from(root.querySelectorAll<HTMLElement>(FOCUSABLE)).filter((element) => {
    if (element.closest('.ion-page-hidden, [inert], [aria-hidden="true"]')) return false;
    const style = window.getComputedStyle(element);
    if (style.visibility === 'hidden' || style.display === 'none') return false;
    const rect = element.getBoundingClientRect();
    return rect.width > 0 && rect.height > 0;
  });
}

function isTextEntry(element: Element | null): boolean {
  return element instanceof HTMLInputElement
    || element instanceof HTMLTextAreaElement
    || element instanceof HTMLSelectElement;
}

/**
 * Scrolls every scroll container between the element and the page so the focused
 * card is actually on screen. `scrollIntoView` alone does nothing here: the app
 * scrolls a `.tv-page` div (and each row a `.row__track`), not the document, so the
 * focus ring used to travel off screen while the page stayed still.
 */
function revealElement(element: HTMLElement): void {
  const margin = 16;
  let node = element.parentElement;

  while (node && node !== document.body) {
    const style = window.getComputedStyle(node);
    const scrollsY = /(auto|scroll)/.test(style.overflowY) && node.scrollHeight > node.clientHeight + 1;
    const scrollsX = /(auto|scroll)/.test(style.overflowX) && node.scrollWidth > node.clientWidth + 1;

    if (scrollsY || scrollsX) {
      const box = node.getBoundingClientRect();
      const rect = element.getBoundingClientRect();

      if (scrollsY) {
        if (rect.top < box.top + margin) node.scrollTop += rect.top - box.top - margin;
        else if (rect.bottom > box.bottom - margin) node.scrollTop += rect.bottom - box.bottom + margin;
      }
      if (scrollsX) {
        if (rect.left < box.left + margin) node.scrollLeft += rect.left - box.left - margin;
        else if (rect.right > box.right - margin) node.scrollLeft += rect.right - box.right + margin;
      }
    }

    node = node.parentElement;
  }
}

/** Moves focus to the neighbour in `direction`. Returns false when there is none. */
export function moveFocus(direction: Direction): boolean {
  const active = document.activeElement as HTMLElement | null;
  const items = focusables(direction, active);

  if (!active || !items.includes(active)) {
    const first = items[0];
    first?.focus({ preventScroll: true });
    if (first) revealElement(first);
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
  revealElement(next);
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

  // Android TV remotes send a hardware BACK instead of Escape.
  useEffect(() => {
    if (!Capacitor.isNativePlatform()) return;
    const handle = CapacitorApp.addListener('backButton', ({ canGoBack }) => {
      if (canGoBack || window.history.length > 1) navigate(-1);
      else CapacitorApp.exitApp();
    });
    return () => {
      void handle.then((listener) => listener.remove());
    };
  }, [navigate]);
}
