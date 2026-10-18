import { useEffect } from 'react';
import { useNavigate, useSearchParams } from 'react-router-dom';
import { IonPage } from '@ionic/react';
import { api } from '../api/endpoints';
import { useAuth } from '../auth/AuthContext';
import { SITE_ORIGIN } from '../config';
import { EmptyState } from '../components/States';
import { toQuery } from '../utils/routes';

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

  useEffect(() => {
    if (!isAuthenticated || !save || !server || !href) return;
    api.addHistory({ server, title, poster: image, link: href }).catch(() => undefined);
  }, [isAuthenticated, save, server, href, title, image]);

  function goBack() {
    if (window.history.length > 1) navigate(-1);
    else navigate('/home', { replace: true });
  }

  return (
    <IonPage className="tv-page tv-player">
      <div className="player__bar">
        <button type="button" className="btn" data-tv data-tv-autofocus onClick={goBack}>
          ← Back
        </button>
        <span className="player__title">{title}</span>
      </div>

      {src ? (
        <iframe
          className="player__frame"
          title={title || 'Video player'}
          src={src}
          allow="autoplay; fullscreen; encrypted-media; picture-in-picture"
          allowFullScreen
          referrerPolicy="no-referrer"
        />
      ) : (
        <div className="player__empty">
          <EmptyState text="No video link was provided." />
        </div>
      )}
    </IonPage>
  );
}
