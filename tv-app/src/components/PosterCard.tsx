import { Link } from 'react-router-dom';

export interface PosterCardProps {
  title: string;
  image?: string;
  to: string;
  subtitle?: string;
}

export function PosterCard({ title, image, to, subtitle }: PosterCardProps) {
  return (
    <Link to={to} className="poster" data-tv aria-label={title}>
      <div className="poster__frame">
        {image ? (
          <img
            src={image}
            alt=""
            loading="lazy"
            decoding="async"
            referrerPolicy="no-referrer"
            onError={(event) => {
              event.currentTarget.style.display = 'none';
            }}
          />
        ) : null}
        <span className="poster__initials" aria-hidden="true">{title.slice(0, 2)}</span>
        <div className="poster__shade">
          <span className="poster__title">{title}</span>
          {subtitle ? <span className="poster__sub">{subtitle}</span> : null}
        </div>
      </div>
    </Link>
  );
}

export function PosterGrid({ items }: { items: PosterCardProps[] }) {
  return (
    <div className="poster-grid">
      {items.map((item, index) => (
        <PosterCard key={`${item.to}-${index}`} {...item} />
      ))}
    </div>
  );
}
