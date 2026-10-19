import { useEffect, useState } from 'react';
import { useNavigate, useSearchParams } from 'react-router-dom';
import { Capacitor } from '@capacitor/core';
import { api } from '../api/endpoints';
import type { ServerLink } from '../api/types';
import { AppPage } from '../components/AppPage';
import { Hero } from '../components/Hero';
import { EmptyState, ErrorState, RowSkeleton } from '../components/States';
import { errorMessage } from '../utils/errors';
import { watchRoute } from '../utils/routes';
import { streamBrowser } from '../plugins/streamBrowser';

export function ServersPage() {
  const navigate = useNavigate();
  const [params] = useSearchParams();
  const server = params.get('server') ?? '';
  const href = params.get('href') ?? '';
  const title = params.get('title') ?? '';
  const image = params.get('image') ?? '';

  const [items, setItems] = useState<ServerLink[] | null>(null);
  const [error, setError] = useState<string | null>(null);
  const [openError, setOpenError] = useState<string | null>(null);
  const [attempt, setAttempt] = useState(0);

  useEffect(() => {
    if (!server || !href) {
      setError('This title link is incomplete.');
      return;
    }
    let cancelled = false;
    setItems(null);
    setError(null);
    api
      .servers(server, href)
      .then((list) => {
        if (!cancelled) setItems(list);
      })
      .catch((err) => {
        if (!cancelled) setError(errorMessage(err));
      });
    return () => {
      cancelled = true;
    };
  }, [server, href, attempt]);

  const first = items?.[0];
  const backdrop = first?.backdrop || image;

  const selectServer = async (item: ServerLink) => {
    setOpenError(null);
    if (Capacitor.getPlatform() !== 'android') {
      navigate(watchRoute({ link: item.link, title, server, href, image }));
      return;
    }

    try {
      await streamBrowser.openExternal({ url: item.link });
    } catch (err) {
      setOpenError(errorMessage(err));
    }
  };

  return (
    <AppPage>
      <Hero
        title={title || 'Choose a server'}
        description={first?.overview}
        backdrop={backdrop || undefined}
        meta={first?.date}
      />

      <div className="home-rows">
        <section className="row">
          <header className="row__head">
            <h2>Servers</h2>
          </header>

          {error ? <ErrorState message={error} onRetry={() => setAttempt((n) => n + 1)} /> : null}
          {openError ? <ErrorState message={openError} /> : null}
          {!error && items === null ? <div className="server-grid"><RowSkeleton count={4} /></div> : null}
          {items && items.length === 0 ? <EmptyState text="No servers were found for this title." /> : null}

          {items && items.length > 0 ? (
            <div className="server-grid">
              {items.map((item, index) => (
                <button
                  key={`${item.link}-${index}`}
                  type="button"
                  className="server-btn"
                  data-tv
                  data-tv-autofocus={index === 0 ? true : undefined}
                  onClick={() => void selectServer(item)}
                >
                  {item.name || `Server ${index + 1}`}
                </button>
              ))}
            </div>
          ) : null}
        </section>
      </div>
    </AppPage>
  );
}
