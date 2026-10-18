import { useEffect, useState } from 'react';
import { Link, useNavigate, useSearchParams } from 'react-router-dom';
import { api } from '../api/endpoints';
import type { MoreData } from '../api/types';
import { useAuth } from '../auth/AuthContext';
import { AppPage } from '../components/AppPage';
import { Hero } from '../components/Hero';
import { PosterCard } from '../components/PosterCard';
import { Row } from '../components/Row';
import { ErrorState, RowSkeleton } from '../components/States';
import { errorMessage } from '../utils/errors';
import { serversRoute, titleRoute } from '../utils/routes';

export function TitlePage() {
  const [params] = useSearchParams();
  const navigate = useNavigate();
  const { isAuthenticated } = useAuth();

  const server = params.get('server') ?? '';
  const href = params.get('href') ?? '';
  const knownTitle = params.get('title') ?? '';
  const knownImage = params.get('image') ?? '';

  const [data, setData] = useState<MoreData | null>(null);
  const [status, setStatus] = useState<'loading' | 'ready' | 'error'>('loading');
  const [error, setError] = useState<string | null>(null);
  const [attempt, setAttempt] = useState(0);
  const [favorite, setFavorite] = useState<boolean | null>(null);
  const [notice, setNotice] = useState<string | null>(null);

  const poster = knownImage || data?.poster || data?.backdrop || '';
  const displayTitle = data?.title || knownTitle;

  useEffect(() => {
    if (!server || !href) {
      setStatus('error');
      setError('This title link is incomplete.');
      return;
    }
    let cancelled = false;
    setStatus('loading');
    setError(null);

    api
      .more(server, href)
      .then((more) => {
        if (cancelled) return;
        const isMovie = !more.seasons?.length && !more.episodes?.length;
        if (isMovie) {
          navigate(serversRoute(server, { href, title: more.title || knownTitle, image: knownImage || more.poster }), { replace: true });
          return;
        }
        setData(more);
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
  }, [server, href, knownTitle, knownImage, attempt, navigate]);

  useEffect(() => {
    if (!isAuthenticated || !poster) return;
    let cancelled = false;
    api
      .isFavorite(poster)
      .then((value) => {
        if (!cancelled) setFavorite(value);
      })
      .catch(() => undefined);
    return () => {
      cancelled = true;
    };
  }, [isAuthenticated, poster]);

  async function toggleFavorite() {
    if (favorite === null || !poster) return;
    setNotice(null);
    try {
      if (favorite) {
        await api.removeFavorite(poster);
        setFavorite(false);
      } else {
        await api.addFavorite({ server, title: displayTitle, poster, link: href });
        setFavorite(true);
      }
    } catch (err) {
      setNotice(errorMessage(err));
    }
  }

  const year = data?.release_date ? data.release_date.split('-')[0] : undefined;

  return (
    <AppPage>
      {status === 'loading' ? (
        <>
          <section className="hero hero--empty" aria-busy="true">
            <div className="hero__content">
              <h1 className="hero__title">{knownTitle || 'Loading…'}</h1>
            </div>
          </section>
          <div className="home-rows">
            <Row title="Episodes">
              <RowSkeleton />
            </Row>
          </div>
        </>
      ) : null}

      {status === 'error' ? <ErrorState message={error} onRetry={() => setAttempt((n) => n + 1)} /> : null}

      {status === 'ready' && data ? (
        <>
          <Hero
            title={displayTitle}
            description={data.overview}
            backdrop={data.backdrop || data.poster || knownImage}
            meta={year}
            actions={
              isAuthenticated && favorite !== null ? (
                <button type="button" className="btn" data-tv data-tv-autofocus onClick={toggleFavorite}>
                  {favorite ? '✓ In my list' : '+ My list'}
                </button>
              ) : undefined
            }
          />
          {notice ? <p className="notice notice--error page-notice" role="alert">{notice}</p> : null}

          <div className="home-rows">
            {data.seasons?.length ? (
              <Row title="Seasons">
                {data.seasons.map((season) => (
                  <PosterCard
                    key={season.link}
                    title={season.title}
                    image={poster}
                    to={titleRoute(server, { href: season.link, title: season.title, image: poster })}
                  />
                ))}
              </Row>
            ) : null}

            {data.episodes?.length ? (
              <section className="row" id="episodes">
                <header className="row__head">
                  <h2>Episodes</h2>
                </header>
                <div className="episode-grid">
                  {data.episodes.map((episode, index) => (
                    <Link
                      key={`${episode.link}-${index}`}
                      to={serversRoute(server, { href: episode.link, title: episode.title, image: poster })}
                      className="episode"
                      data-tv
                    >
                      <span className="episode__index">{index + 1}</span>
                      <span className="episode__title">{episode.title}</span>
                    </Link>
                  ))}
                </div>
              </section>
            ) : null}
          </div>
        </>
      ) : null}
    </AppPage>
  );
}
