import { useEffect, useState } from 'react';
import { api } from '../api/endpoints';
import type { SavedItem } from '../api/types';
import { AppPage } from '../components/AppPage';
import { PosterCard } from '../components/PosterCard';
import { EmptyState, ErrorState, GridSkeleton } from '../components/States';
import { errorMessage } from '../utils/errors';
import { serversRoute } from '../utils/routes';

export function HistoryPage() {
  const [items, setItems] = useState<SavedItem[] | null>(null);
  const [error, setError] = useState<string | null>(null);
  const [attempt, setAttempt] = useState(0);
  const [clearing, setClearing] = useState(false);

  useEffect(() => {
    let cancelled = false;
    setError(null);
    api
      .history()
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

  async function clearAll() {
    setClearing(true);
    try {
      await api.clearHistory();
      setItems([]);
    } catch (err) {
      setError(errorMessage(err));
    } finally {
      setClearing(false);
    }
  }

  return (
    <AppPage>
      <div className="page-head page-head--row">
        <h1>Watch history</h1>
        {items && items.length > 0 ? (
          <button type="button" className="btn btn--ghost" data-tv onClick={clearAll} disabled={clearing}>
            {clearing ? 'Clearing…' : 'Clear history'}
          </button>
        ) : null}
      </div>

      {error ? <ErrorState message={error} onRetry={() => setAttempt((n) => n + 1)} /> : null}
      {!error && items === null ? <GridSkeleton /> : null}
      {items && items.length === 0 ? <EmptyState text="Nothing watched yet." /> : null}

      {items && items.length > 0 ? (
        <div className="poster-grid">
          {items.map((item) => (
            <PosterCard
              key={item.id}
              title={item.title}
              image={item.poster}
              subtitle={item.date ? item.date.slice(0, 10) : 'Resume'}
              to={serversRoute(item.server, { href: item.link, title: item.title, image: item.poster })}
            />
          ))}
        </div>
      ) : null}
    </AppPage>
  );
}
