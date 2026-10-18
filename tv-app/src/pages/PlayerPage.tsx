import { useCallback, useEffect, useState } from 'react';
import { useNavigate, useSearchParams } from 'react-router-dom';
import { IonPage } from '@ionic/react';
import { api } from '../api/endpoints';
import { useAuth } from '../auth/AuthContext';
import { SITE_ORIGIN } from '../config';
import { EmptyState } from '../components/States';
import { toQuery } from '../utils/routes';

/** Some embed hosts are offline; after this long, tell the viewer instead of showing black. */
const SLOW_AFTER_MS = 12000;

export function PlayerPage() {
  const [params] = useSearchParams();
  const navigate = useNavigate();
  const { isAuthenticated } = useAuth();

  const link = params.get('link') ?? '';
  const title = params.get('title') ?? '';
  const server = params.get('server') ?? '';
  const href = params.get('href') ?? '';
  const image = params.get('image') ?? '';
  const save = params.get('save') !== '0';

  // Same page the website uses, so the embed sees the same referrer behaviour.
  const src = link ? `${SITE_ORIGIN}/videoPlayer.php?${toQuery({ link })}` : '';

  const [loaded, setLoaded] = useState(false);
  const [slow, setSlow] = useState(false);
  const [attempt, setAttempt] = useState(0);

  useEffect(() => {
    setLoaded(false);
    setSlow(false);
    const timer = setTimeout(() => setSlow(true), SLOW_AFTER_MS);
    return () => clearTimeout(timer);
  }, [src, attempt]);

  useEffect(() => {
    if (!isAuthenticated || !save || !server || !href) return;
    api.addHistory({ server, title, poster: image, link: href }).catch(() => undefined);
  }, [isAuthenticated, save, server, href, title, image]);

  const goBack = useCallback(() => {
    if (window.history.length > 1) navigate(-1);
    else navigate('/home', { replace: true });
  }, [navigate]);

  return (
    <IonPage className="tv-page tv-player">
      <div className="player__bar">
        <button type="button" className="btn" data-tv data-tv-autofocus onClick={goBack}>
          ← Back
        </button>
        <span className="player__title">{title}</span>
      </div>

      {src ? (
        <>
          <iframe
            key={attempt}
            className="player__frame"
            title={title || 'Video player'}
            src={src}
            allow="autoplay; fullscreen; encrypted-media; picture-in-picture"
            allowFullScreen
            referrerPolicy="no-referrer"
            onLoad={() => setLoaded(true)}
          />

          {!loaded && (
            <div className="player__overlay" role="status" aria-live="polite">
              <span className="player__ring" aria-hidden="true" />
              <p>{slow ? 'This source is not responding.' : 'Loading stream…'}</p>
              {slow && (
                <>
                  <p className="player__hint">
                    Some servers are offline. Try another source for this title.
                  </p>
                  <div className="player__actions">
                    <button type="button" className="btn btn--primary" data-tv onClick={goBack}>
                      Choose another server
                    </button>
                    <button type="button" className="btn btn--ghost" data-tv onClick={() => setAttempt((n) => n + 1)}>
                      Reload
                    </button>
                  </div>
                </>
              )}
            </div>
          )}
        </>
      ) : (
        <div className="player__empty">
          <EmptyState text="No video link was provided." />
        </div>
      )}
    </IonPage>
  );
}
