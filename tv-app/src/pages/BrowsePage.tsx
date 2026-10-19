import { useEffect, useState } from 'react';
import { useSearchParams } from 'react-router-dom';
import { api } from '../api/endpoints';
import type { MainServer, Show } from '../api/types';
import { AppPage } from '../components/AppPage';
import { PosterGrid } from '../components/PosterCard';
import { ServerChips } from '../components/ServerChips';
import { EmptyState, ErrorState, GridSkeleton } from '../components/States';
import { errorMessage } from '../utils/errors';
import { titleRoute } from '../utils/routes';

type Status = 'loading' | 'ready' | 'error';

export function BrowsePage() {
  const [params] = useSearchParams();
  const serverId = params.get('server') ?? '2';

  const [servers, setServers] = useState<MainServer[]>([]);
  const [items, setItems] = useState<Show[]>([]);
  const [page, setPage] = useState(1);
  const [status, setStatus] = useState<Status>('loading');
  const [error, setError] = useState<string | null>(null);
  const [loadingMore, setLoadingMore] = useState(false);
  const [attempt, setAttempt] = useState(0);

  useEffect(() => {
    api.main().then((main) => setServers(main.servers ?? [])).catch(() => undefined);
  }, []);

  useEffect(() => {
    let cancelled = false;
    setItems([]);
    setPage(1);
    setStatus('loading');
    setError(null);

    api
      .shows(serverId, 1)
      .then((list) => {
        if (cancelled) return;
        setItems(list);
        setStatus('ready');
      })
      .catch((err) => {
        if (cancelled) return;
        setError(errorMessage(err));
        setStatus('error');
      });

    return () => {
      cancelled = true;
    };
  }, [serverId, attempt]);

  async function loadMore() {
    const next = page + 1;
    setLoadingMore(true);
    try {
      const list = await api.shows(serverId, next);
      setItems((prev) => [...prev, ...list]);
      setPage(next);
    } catch (err) {
      setError(errorMessage(err));
    } finally {
      setLoadingMore(false);
    }
  }

  const title = servers.find((server) => String(server.id) === serverId)?.name ?? 'Browse';

  return (
    <AppPage>
      <div className="page-head">
        <h1>{title}</h1>
      </div>
      <ServerChips servers={servers} activeId={serverId} hrefFor={(id) => `/browse?server=${id}`} />

      {status === 'loading' ? <GridSkeleton /> : null}
      {status === 'error' ? <ErrorState message={error} onRetry={() => setAttempt((n) => n + 1)} /> : null}
      {status === 'ready' && items.length === 0 ? <EmptyState text="Nothing to show from this source yet." /> : null}

      {status === 'ready' && items.length > 0 ? (
        <>
          <PosterGrid
            items={items.map((show) => ({
              title: show.title,
              image: show.image,
              subtitle: show.episode || show.category,
              to: titleRoute(serverId, show),
            }))}
          />
          <div className="center-row">
            <button type="button" className="btn" data-tv onClick={loadMore} disabled={loadingMore}>
              {loadingMore ? 'Loading…' : 'Load more'}
            </button>
          </div>
        </>
      ) : null}
    </AppPage>
  );
}
