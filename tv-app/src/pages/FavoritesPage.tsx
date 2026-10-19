import { useEffect, useState } from 'react';
import { api } from '../api/endpoints';
import type { SavedItem } from '../api/types';
import { AppPage } from '../components/AppPage';
import { PosterCard } from '../components/PosterCard';
import { EmptyState, ErrorState, GridSkeleton } from '../components/States';
import { errorMessage } from '../utils/errors';
import { titleRoute } from '../utils/routes';

export function FavoritesPage() {
  const [items, setItems] = useState<SavedItem[] | null>(null);
  const [error, setError] = useState<string | null>(null);
  const [attempt, setAttempt] = useState(0);

  useEffect(() => {
    let cancelled = false;
    setError(null);
    api
      .favorites()
      .then((list) => {
        if (!cancelled) setItems(list);
      })
      .catch((err) => {
        if (!cancelled) setError(errorMessage(err));
      });
    return () => {
      cancelled = true;
    };
  }, [attempt]);

  async function remove(item: SavedItem) {
    try {
      await api.removeFavorite(item.poster);
      setItems((prev) => (prev ? prev.filter((entry) => entry.id !== item.id) : prev));
    } catch (err) {
      setError(errorMessage(err));
    }
  }

  return (
    <AppPage>
      <div className="page-head">
        <h1>My list</h1>
      </div>

      {error ? <ErrorState message={error} onRetry={() => setAttempt((n) => n + 1)} /> : null}
      {!error && items === null ? <GridSkeleton /> : null}
      {items && items.length === 0 ? (
        <EmptyState text="Your list is empty. Open a title and choose “My list” to save it here." />
      ) : null}

      {items && items.length > 0 ? (
        <div className="poster-grid">
          {items.map((item) => (
            <div className="saved" key={item.id}>
              <PosterCard
                title={item.title}
                image={item.poster}
                to={titleRoute(item.server, { href: item.link, title: item.title, image: item.poster })}
              />
              <button type="button" className="btn btn--ghost btn--block" data-tv onClick={() => remove(item)}>
                Remove
              </button>
            </div>
          ))}
        </div>
      ) : null}
    </AppPage>
  );
}
