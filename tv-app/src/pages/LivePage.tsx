import { useEffect, useRef, useState } from 'react';
import { Capacitor } from '@capacitor/core';
import { streamBrowser } from '../plugins/streamBrowser';
import { api } from '../api/endpoints';
import type { LiveMatch, LivePlayer } from '../api/types';
import { AppPage } from '../components/AppPage';
import { Link } from 'react-router-dom';
import { EmptyState, ErrorState, GridSkeleton, Spinner } from '../components/States';
import { errorMessage } from '../utils/errors';
import { watchRoute } from '../utils/routes';

export function LivePage() {
  const [matches, setMatches] = useState<LiveMatch[] | null>(null);
  const [error, setError] = useState<string | null>(null);
  const [attempt, setAttempt] = useState(0);

  const [selected, setSelected] = useState<LiveMatch | null>(null);
  const [players, setPlayers] = useState<LivePlayer[] | null>(null);
  const [playersError, setPlayersError] = useState<string | null>(null);
  const [openError, setOpenError] = useState<string | null>(null);
  const requestId = useRef(0);

  useEffect(() => {
    let cancelled = false;
    setError(null);
    api
      .live()
      .then((list) => {
        if (!cancelled) setMatches(list);
      })
      .catch((err) => {
        if (!cancelled) setError(errorMessage(err));
      });
    return () => {
      cancelled = true;
    };
  }, [attempt]);

  function choose(match: LiveMatch) {
    const id = ++requestId.current;
    setSelected(match);
    setPlayers(null);
    setPlayersError(null);
    setOpenError(null);
    api
      .liveServers(match.href)
      .then((list) => {
        if (id === requestId.current) setPlayers(list);
      })
      .catch((err) => {
        if (id === requestId.current) setPlayersError(errorMessage(err));
      });
  }

  const matchTitle = selected ? `${selected.leftTeamName} vs ${selected.rightTeamName}` : '';

  return (
    <AppPage>
      <div className="page-head">
        <h1>Live matches</h1>
      </div>

      {error ? <ErrorState message={error} onRetry={() => setAttempt((n) => n + 1)} /> : null}
      {!error && matches === null ? <GridSkeleton count={6} /> : null}
      {matches && matches.length === 0 ? <EmptyState text="No matches are listed right now." /> : null}

      {matches && matches.length > 0 ? (
        <div className="live-layout">
          <div className="live-list">
            {matches.map((match, index) => (
              <button
                key={`${match.href}-${index}`}
                type="button"
                className={`match${selected?.href === match.href ? ' is-selected' : ''}`}
                data-tv
                data-tv-autofocus={index === 0 ? true : undefined}
                onClick={() => choose(match)}
              >
                <span className="match__teams">
                  <span className="match__team">
                    {match.leftTeamLogo ? <img src={match.leftTeamLogo} alt="" referrerPolicy="no-referrer" /> : null}
                    {match.leftTeamName}
                  </span>
                  <span className="match__vs">VS</span>
                  <span className="match__team">
                    {match.rightTeamLogo ? <img src={match.rightTeamLogo} alt="" referrerPolicy="no-referrer" /> : null}
                    {match.rightTeamName}
                  </span>
                </span>
                <span className="match__meta">
                  {match.liveStatus || match.matchTime}
                  {match.league ? ` · ${match.league}` : ''}
                </span>
              </button>
            ))}
          </div>

          <aside className="live-panel">
            {!selected ? <EmptyState text="Choose a match to see its streams." /> : null}
            {selected ? (
              <>
                <h2 className="live-panel__title">{matchTitle}</h2>
                <p className="muted">
                  {[selected.matchTime, selected.result, selected.channel, selected.commentator]
                    .filter(Boolean)
                    .join(' · ')}
                </p>
                {playersError ? <ErrorState message={playersError} /> : null}
                {openError ? <ErrorState message={openError} /> : null}
                {!playersError && players === null ? <Spinner label="Loading streams" /> : null}
                {players && players.length === 0 ? <EmptyState text="No streams are available yet." /> : null}
                {players && players.length > 0 ? (
                  <div className="server-grid">
                    {players.map((player, index) => (
                      <Link
                        key={`${player.live}-${index}`}
                        to={watchRoute({ link: player.live, title: matchTitle, save: false })}
                        onClick={(event) => {
                          if (Capacitor.getPlatform() !== 'android') return;
                          event.preventDefault();
                          setOpenError(null);
                          void streamBrowser.openInternal({ url: player.live }).catch(() => {
                            setOpenError('Internal browser could not open this stream.');
                          });
                        }}
                        className="server-btn"
                        data-tv
                      >
                        {player.name || `Stream ${index + 1}`}
                      </Link>
                    ))}
                  </div>
                ) : null}
              </>
            ) : null}
          </aside>
        </div>
      ) : null}
    </AppPage>
  );
}
