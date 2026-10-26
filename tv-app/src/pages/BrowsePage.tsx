import { useCallback, useEffect, useLayoutEffect, useRef, useState } from 'react';
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
  const loadingMoreRef = useRef(false);
  const requestId = useRef(0);
  const pendingScroll = useRef<{ page: HTMLElement; top: number } | null>(null);

  useEffect(() => {
    api.main().then((main) => setServers(main.servers ?? [])).catch(() => undefined);
  }, []);

  useEffect(() => {
    let cancelled = false;
    const currentRequest = ++requestId.current;
    pendingScroll.current = null;
    loadingMoreRef.current = false;
    setLoadingMore(false);
    setItems([]);
    setPage(1);
    setStatus('loading');
    setError(null);

    api
      .shows(serverId, 1)
      .then((list) => {
        if (cancelled || currentRequest !== requestId.current) return;
        setItems(list);
        setStatus('ready');
      })
      .catch((err) => {
        if (cancelled || currentRequest !== requestId.current) return;
        setError(errorMessage(err));
        setStatus('error');
      });

    return () => {
      cancelled = true;
    };
  }, [serverId, attempt]);

  useLayoutEffect(() => {
    const saved = pendingScroll.current;
    if (!saved) return;

    pendingScroll.current = null;
    if (saved.page.isConnected) saved.page.scrollTop = saved.top;
  }, [items]);

  const loadMore = useCallback(async () => {
    if (loadingMoreRef.current || status !== 'ready') return;

    loadingMoreRef.current = true;
    const currentRequest = requestId.current;
    const next = page + 1;
    const currentPage = document.querySelector<HTMLElement>('.tv-page:not(.ion-page-hidden)');
    pendingScroll.current = currentPage ? { page: currentPage, top: currentPage.scrollTop } : null;
    setLoadingMore(true);
    setError(null);
    try {
      const list = await api.shows(serverId, next);
      if (currentRequest !== requestId.current) return;
      setItems((prev) => {
        const existingHrefs = new Set(prev.map((item) => item.href));
        return [...prev, ...list.filter((item) => !existingHrefs.has(item.href))];
      });
      setPage((current) => Math.max(current, next));
    } catch (err) {
      if (currentRequest === requestId.current) setError(errorMessage(err));
    } finally {
      if (currentRequest === requestId.current) {
        loadingMoreRef.current = false;
        setLoadingMore(false);
      }
    }
  }, [page, serverId, status]);

  useEffect(() => {
    if (status !== 'ready') return;

    const pageElement = document.querySelector<HTMLElement>('.tv-page:not(.ion-page-hidden)');
    if (!pageElement) return;

    const loadWhenNearBottom = () => {
      const distanceToBottom = pageElement.scrollHeight - pageElement.scrollTop - pageElement.clientHeight;
      if (distanceToBottom <= Math.max(pageElement.clientHeight, 320)) void loadMore();
    };

    pageElement.addEventListener('scroll', loadWhenNearBottom, { passive: true });
    return () => pageElement.removeEventListener('scroll', loadWhenNearBottom);
  }, [loadMore, status]);

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
          {error ? <ErrorState message={`${error} Scroll down to try again.`} /> : null}
          <PosterGrid
            items={items.map((show) => ({
              title: show.title,
              image: show.image,
              subtitle: show.episode || show.category,
              to: titleRoute(serverId, show),
            }))}
          />
          {loadingMore ? <GridSkeleton count={6} /> : null}
        </>
      ) : null}
    </AppPage>
  );
}
