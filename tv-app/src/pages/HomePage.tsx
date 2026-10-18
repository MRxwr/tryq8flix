import { useCallback, useEffect, useRef, useState } from 'react';
import { api } from '../api/endpoints';
import type { MainServer, SavedItem, Show } from '../api/types';
import { useAuth } from '../auth/AuthContext';
import { AppPage } from '../components/AppPage';
import { Hero } from '../components/Hero';
import { PosterCard } from '../components/PosterCard';
import { Row } from '../components/Row';
import { ErrorState, RowSkeleton } from '../components/States';
import { errorMessage } from '../utils/errors';
import { serversRoute, titleRoute } from '../utils/routes';
import { Link } from 'react-router-dom';

interface ServerRow {
  status: 'loading' | 'ready' | 'error';
  shows: Show[];
  error?: string;
}

interface HeroData {
  title: string;
  description?: string;
  backdrop?: string;
  meta?: string;
  to: string;
}

/** TVDB servers carry backdrops, so the hero rotates through them. */
const HERO_SERVER_IDS = ['13', '14'];

function isUsableImage(url?: string): url is string {
  if (!url) return false;
  try {
    const parsed = new URL(url);
    return (parsed.protocol === 'https:' || parsed.protocol === 'http:')
      && Boolean(parsed.pathname && !parsed.pathname.endsWith('/original'));
  } catch {
    return false;
  }
}

export function HomePage() {
  const { isAuthenticated } = useAuth();
  const [servers, setServers] = useState<MainServer[] | null>(null);
  const [mainError, setMainError] = useState<string | null>(null);
  const [rows, setRows] = useState<Record<string, ServerRow>>({});
  const [history, setHistory] = useState<SavedItem[]>([]);
  const [favorites, setFavorites] = useState<SavedItem[]>([]);
  const [hero, setHero] = useState<HeroData | null>(null);
  const [attempt, setAttempt] = useState(0);
  const heroPicked = useRef(false);

  const loadServerRow = useCallback((server: MainServer) => {
    const key = String(server.id);
    setRows((prev) => ({ ...prev, [key]: { status: 'loading', shows: [] } }));
    api
      .shows(server.id, 1)
      .then((shows) => setRows((prev) => ({ ...prev, [key]: { status: 'ready', shows: shows.slice(0, 24) } })))
      .catch((err) => setRows((prev) => ({ ...prev, [key]: { status: 'error', shows: [], error: errorMessage(err) } })));
  }, []);

  // Source list, then every source row in parallel.
  useEffect(() => {
    let cancelled = false;
    setServers(null);
    setMainError(null);
    setRows({});
    setHero(null);
    heroPicked.current = false;

    api
      .main()
      .then((main) => {
        if (!cancelled) setServers(main.servers ?? []);
      })
      .catch((err) => {
        if (!cancelled) setMainError(errorMessage(err));
      });

    return () => {
      cancelled = true;
    };
  }, [attempt]);

  useEffect(() => {
    if (servers) servers.forEach(loadServerRow);
  }, [servers, loadServerRow]);

  // Personal rows only load for signed-in users.
  useEffect(() => {
    if (!isAuthenticated) return;
    let cancelled = false;
    api.history().then((items) => {
      if (!cancelled) setHistory(items);
    }).catch(() => undefined);
    api.favorites().then((items) => {
      if (!cancelled) setFavorites(items);
    }).catch(() => undefined);
    return () => {
      cancelled = true;
    };
  }, [isAuthenticated, attempt]);

  // Wait for TVDB's backdrop-enabled rows before choosing a non-backdrop fallback.
  useEffect(() => {
    if (heroPicked.current || !servers) return;

    const preferred = servers.filter((server) => HERO_SERVER_IDS.includes(String(server.id)));
    const candidates = [...preferred, ...servers.filter((server) => !HERO_SERVER_IDS.includes(String(server.id)))];
    const hasPendingPreferred = preferred.some((server) => {
      const row = rows[String(server.id)];
      return !row || row.status === 'loading';
    });

    for (const server of preferred) {
      const row = rows[String(server.id)];
      const picks = row?.status === 'ready' ? row.shows.filter((show) => isUsableImage(show.backdrop)) : [];
      if (picks.length === 0) continue;

      const pick = picks[Math.floor(Math.random() * picks.length)];
      heroPicked.current = true;
      setHero({
        title: pick.title,
        description: pick.description,
        backdrop: pick.backdrop,
        meta: pick.date ?? pick.category,
        to: titleRoute(server.id, pick),
      });
      return;
    }

    if (hasPendingPreferred) return;

    for (const server of candidates) {
      const row = rows[String(server.id)];
      const picks = row?.status === 'ready' ? row.shows.filter((show) => isUsableImage(show.image)) : [];
      if (picks.length === 0) continue;

      const pick = picks[Math.floor(Math.random() * picks.length)];
      heroPicked.current = true;
      setHero({
        title: pick.title,
        description: pick.description,
        backdrop: pick.image,
        meta: pick.date ?? pick.category,
        to: titleRoute(server.id, pick),
      });
      return;
    }
  }, [servers, rows]);

  return (
    <AppPage>
      {hero ? (
        <Hero
          title={hero.title}
          description={hero.description}
          backdrop={hero.backdrop}
          meta={hero.meta}
          actions={
            <Link to={hero.to} className="btn btn--primary" data-tv data-tv-autofocus>
              ▶ Watch now
            </Link>
          }
        />
      ) : (
        <section className="hero hero--empty">
          <div className="hero__content">
            <h1 className="hero__title">{mainError ? 'Could not load TRYQ8FLIX' : 'Loading…'}</h1>
          </div>
        </section>
      )}

      {mainError ? <ErrorState message={mainError} onRetry={() => setAttempt((n) => n + 1)} /> : null}

      <div className="home-rows">
        {isAuthenticated && history.length > 0 ? (
          <Row title="Continue watching">
            {history.slice(0, 20).map((item) => (
              <PosterCard
                key={`history-${item.id}`}
                title={item.title}
                image={item.poster}
                subtitle="Resume"
                to={serversRoute(item.server, { href: item.link, title: item.title, image: item.poster })}
              />
            ))}
          </Row>
        ) : null}

        {isAuthenticated && favorites.length > 0 ? (
          <Row title="My list" action={{ label: 'View all', to: '/favorites' }}>
            {favorites.slice(0, 20).map((item) => (
              <PosterCard
                key={`fav-${item.id}`}
                title={item.title}
                image={item.poster}
                to={titleRoute(item.server, { href: item.link, title: item.title, image: item.poster })}
              />
            ))}
          </Row>
        ) : null}

        {(servers ?? []).map((server) => {
          const key = String(server.id);
          const row = rows[key];
          return (
            <Row key={key} title={server.name} action={{ label: 'View all', to: `/browse?server=${key}` }}>
              {!row || row.status === 'loading' ? <RowSkeleton /> : null}
              {row?.status === 'error' ? (
                <ErrorState compact message={row.error ?? null} onRetry={() => loadServerRow(server)} />
              ) : null}
              {row?.status === 'ready'
                ? row.shows.map((show, index) => (
                  <PosterCard
                    key={`${key}-${index}-${show.href}`}
                    title={show.title}
                    image={show.image}
                    subtitle={show.episode || show.category}
                    to={titleRoute(server.id, show)}
                  />
                ))
                : null}
            </Row>
          );
        })}
      </div>
    </AppPage>
  );
}
