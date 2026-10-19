import { useEffect, useState, type FormEvent } from 'react';
import { useSearchParams } from 'react-router-dom';
import { api } from '../api/endpoints';
import type { MainServer, Show } from '../api/types';
import { AppPage } from '../components/AppPage';
import { PosterGrid } from '../components/PosterCard';
import { ServerChips } from '../components/ServerChips';
import { EmptyState, ErrorState, GridSkeleton } from '../components/States';
import { errorMessage } from '../utils/errors';
import { titleRoute, toQuery } from '../utils/routes';

export function SearchPage() {
  const [params, setParams] = useSearchParams();
  const query = params.get('q') ?? '';
  const serverId = params.get('server') ?? '2';

  const [draft, setDraft] = useState(query);
  const [servers, setServers] = useState<MainServer[]>([]);
  const [results, setResults] = useState<Show[]>([]);
  const [status, setStatus] = useState<'idle' | 'loading' | 'ready' | 'error'>('idle');
  const [error, setError] = useState<string | null>(null);
  const [attempt, setAttempt] = useState(0);

  useEffect(() => {
    setDraft(query);
  }, [query]);

  useEffect(() => {
    api.main().then((main) => setServers(main.servers ?? [])).catch(() => undefined);
  }, []);

  useEffect(() => {
    if (!query) {
      setResults([]);
      setStatus('idle');
      return;
    }
    let cancelled = false;
    setStatus('loading');
    setError(null);

    api
      .shows(serverId, 1, query)
      .then((list) => {
        if (cancelled) return;
        setResults(list);
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
  }, [query, serverId, attempt]);

  function submit(event: FormEvent) {
    event.preventDefault();
    const next = draft.trim();
    setParams({ q: next, server: serverId });
  }

  return (
    <AppPage>
      <div className="page-head">
        <h1>Search</h1>
      </div>

      <form className="search-form" onSubmit={submit} role="search">
        <input
          data-tv-autofocus
          type="search"
          className="search-input"
          placeholder="Search titles"
          aria-label="Search titles"
          value={draft}
          onChange={(event) => setDraft(event.target.value)}
        />
        <button type="submit" className="btn btn--primary" data-tv>
          Search
        </button>
      </form>

      <ServerChips
        servers={servers}
        activeId={serverId}
        hrefFor={(id) => `/search?${toQuery({ q: query, server: id })}`}
      />

      {status === 'idle' ? <EmptyState text="Type a title and press Search." /> : null}
      {status === 'loading' ? <GridSkeleton /> : null}
      {status === 'error' ? <ErrorState message={error} onRetry={() => setAttempt((n) => n + 1)} /> : null}
      {status === 'ready' && results.length === 0 ? <EmptyState text={`No results for "${query}".`} /> : null}
      {status === 'ready' && results.length > 0 ? (
        <PosterGrid
          items={results.map((show) => ({
            title: show.title,
            image: show.image,
            subtitle: show.episode || show.category,
            to: titleRoute(serverId, show),
          }))}
        />
      ) : null}
    </AppPage>
  );
}
