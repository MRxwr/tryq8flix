import { Link } from 'react-router-dom';
import type { MainServer } from '../api/types';

interface ServerChipsProps {
  servers: MainServer[];
  activeId: string;
  hrefFor: (id: string) => string;
}

export function ServerChips({ servers, activeId, hrefFor }: ServerChipsProps) {
  return (
    <nav className="chips" aria-label="Sources">
      {servers.map((server) => {
        const id = String(server.id);
        const active = id === activeId;
        return (
          <Link
            key={id}
            to={hrefFor(id)}
            className={`chip${active ? ' is-active' : ''}`}
            data-tv
            aria-current={active ? 'page' : undefined}
          >
            {server.name}
          </Link>
        );
      })}
    </nav>
  );
}
