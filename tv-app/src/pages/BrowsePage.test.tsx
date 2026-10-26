import { fireEvent, render, screen } from '@testing-library/react';
import { MemoryRouter } from 'react-router-dom';
import { beforeEach, describe, expect, it, vi } from 'vitest';
import { api } from '../api/endpoints';
import { BrowsePage } from './BrowsePage';

vi.mock('../api/endpoints', () => ({
  api: {
    main: vi.fn(),
    shows: vi.fn(),
  },
}));

describe('BrowsePage', () => {
  beforeEach(() => {
    vi.mocked(api.main).mockResolvedValue({ servers: [{ id: 2, name: 'Source 2' }] });
    vi.mocked(api.shows).mockImplementation(async (_server, page = 1) => (
      page === 1
        ? [{ title: 'First title', image: '', href: '/first' }]
        : [{ title: 'Second title', image: '', href: '/second' }]
    ));
  });

  it('loads the next page while scrolling without resetting the browse position', async () => {
    render(
      <MemoryRouter initialEntries={['/browse']}>
        <BrowsePage />
      </MemoryRouter>,
    );

    await screen.findByRole('link', { name: 'First title' });
    const page = document.querySelector<HTMLElement>('.tv-page');
    expect(page).not.toBeNull();
    Object.defineProperty(page, 'clientHeight', { configurable: true, value: 500 });
    Object.defineProperty(page, 'scrollHeight', { configurable: true, value: 1200 });
    page!.scrollTop = 320;

    expect(screen.queryByRole('button', { name: /load more/i })).not.toBeInTheDocument();
    fireEvent.scroll(page!);

    await screen.findByRole('link', { name: 'Second title' });
    expect(api.shows).toHaveBeenNthCalledWith(2, '2', 2);
    expect(page!.scrollTop).toBe(320);
  });
});
