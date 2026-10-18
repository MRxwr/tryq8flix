import type { ReactNode } from 'react';
import { Link } from 'react-router-dom';

interface RowProps {
  title: string;
  action?: { label: string; to: string };
  children: ReactNode;
}

export function Row({ title, action, children }: RowProps) {
  return (
    <section className="row">
      <header className="row__head">
        <h2>{title}</h2>
        {action ? (
          <Link to={action.to} className="row__more" data-tv>
            {action.label} <span aria-hidden="true">›</span>
          </Link>
        ) : null}
      </header>
      <div className="row__track">{children}</div>
    </section>
  );
}
