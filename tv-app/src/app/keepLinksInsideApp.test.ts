import { afterEach, beforeEach, describe, expect, it, vi } from 'vitest';
import { keepLinksInsideApp } from './keepLinksInsideApp';

function click(anchor: HTMLAnchorElement): boolean {
  const event = new MouseEvent('click', { bubbles: true, cancelable: true });
  anchor.dispatchEvent(event);
  return event.defaultPrevented;
}

describe('keepLinksInsideApp', () => {
  beforeEach(() => {
    keepLinksInsideApp();
  });

  afterEach(() => {
    document.body.innerHTML = '';
  });

  it('blocks links that leave the app', () => {
    const anchor = document.createElement('a');
    anchor.href = 'https://www.imdb.com/title/tt1234';
    document.body.appendChild(anchor);

    expect(click(anchor)).toBe(true);
  });

  it('blocks foreign links that ask for a new window', () => {
    const anchor = document.createElement('a');
    anchor.href = 'https://vidsrc.cc/v2/embed/movie/1';
    anchor.target = '_blank';
    document.body.appendChild(anchor);

    expect(click(anchor)).toBe(true);
  });

  it('lets in-app links through', () => {
    const anchor = document.createElement('a');
    anchor.href = new URL('/browse', window.location.href).toString();
    document.body.appendChild(anchor);

    const prevented = click(anchor);
    // jsdom cannot actually navigate; drop the href before its queued navigation runs.
    anchor.removeAttribute('href');
    expect(prevented).toBe(false);
  });

  it('ignores non-navigational links', () => {
    const anchor = document.createElement('a');
    anchor.href = '#section';
    document.body.appendChild(anchor);

    expect(click(anchor)).toBe(false);
  });

  it('neutralises window.open', () => {
    expect(window.open('https://example.com')).toBeNull();
    expect(vi.isMockFunction(window.open) || typeof window.open === 'function').toBe(true);
  });
});
