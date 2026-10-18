import { useCallback, useEffect, useRef, useState } from 'react';
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

  const frame = useRef<HTMLIFrameElement>(null);
  const startButton = useRef<HTMLButtonElement>(null);
  const [loaded, setLoaded] = useState(false);
  const [slow, setSlow] = useState(false);
  const [started, setStarted] = useState(false);
  const [attempt, setAttempt] = useState(0);

  useEffect(() => {
    setLoaded(false);
    setSlow(false);
    setStarted(false);
    const timer = setTimeout(() => setSlow(true), SLOW_AFTER_MS);
    return () => clearTimeout(timer);
  }, [src, attempt]);

  useEffect(() => {
    if (!isAuthenticated || !save || !server || !href) return;
    api.addHistory({ server, title, poster: image, link: href }).catch(() => undefined);
  }, [isAuthenticated, save, server, href, title, image]);

  // The remote must land on the primary action, not on Back.
  useEffect(() => {
    if (loaded && !started) startButton.current?.focus();
  }, [loaded, started]);

  const goBack = useCallback(() => {
    if (window.history.length > 1) navigate(-1);
    else navigate('/home', { replace: true });
  }, [navigate]);

  /**
   * Cross-origin embeds expose nothing to the app, so the only way a remote can
   * drive their player is to hand keyboard focus to the iframe itself.
   */
  const startPlayback = useCallback(() => {
    setStarted(true);
    frame.current?.focus();
  }, []);

  /**
   * Once a video is on screen the viewer stays on it: anything that tries to
   * navigate the app away (a stray link, an ad overlay) is swallowed here.
   */
  const blockNavigation = useCallback((event: React.MouseEvent) => {
    const anchor = (event.target as Element | null)?.closest?.('a[href]');
    if (anchor && !anchor.hasAttribute('data-tv')) event.preventDefault();
  }, []);

  return (
    <IonPage className="tv-page tv-player" onClickCapture={blockNavigation}>
      <div className="player__bar">
        <button type="button" className="btn" data-tv data-tv-autofocus onClick={goBack}>
          ← Back
        </button>
        <span className="player__title">{title}</span>
        {started && <span className="player__hint player__hint--bar">Remote: press Back to exit</span>}
      </div>

      {src ? (
        <>
          <iframe
            key={attempt}
            ref={frame}
            className="player__frame"
            title={title || 'Video player'}
            src={src}
            tabIndex={0}
            allow="autoplay; fullscreen; encrypted-media; picture-in-picture"
            allowFullScreen
            referrerPolicy="no-referrer"
            onLoad={() => setLoaded(true)}
          />

          {!started && (
            <div className="player__overlay" role="status" aria-live="polite">
              {loaded ? (
                <>
                  <p>{slow ? 'This source is not responding.' : 'Ready to play'}</p>
                  <button
                    ref={startButton}
                    type="button"
                    className="btn btn--primary"
                    data-tv
                    onClick={startPlayback}
                  >
                    ▶ Start playback
                  </button>
                  <p className="player__hint">
                    Uses the player&apos;s own controls. If nothing happens, go back and pick another server.
                  </p>
                </>
              ) : (
                <>
                  <span className="player__ring" aria-hidden="true" />
                  <p>Loading stream…</p>
                  {slow && (
                    <p className="player__hint">
                      Some servers are offline. Try another source for this title.
                    </p>
                  )}
                </>
              )}

              <div className="player__actions">
                <button type="button" className="btn btn--ghost" data-tv onClick={goBack}>
                  Choose another server
                </button>
                <button type="button" className="btn btn--ghost" data-tv onClick={() => setAttempt((n) => n + 1)}>
                  Reload
                </button>
              </div>
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
