import { Link, NavLink } from 'react-router-dom';

const LINKS = [
  { to: '/browse', label: 'Browse' },
  { to: '/search', label: 'Search' },
  { to: '/live', label: 'Live' },
  { to: '/favorites', label: 'My List' },
  { to: '/history', label: 'History' },
  { to: '/settings', label: 'Account' },
];

export function TopNav() {
  return (
    <header className="topnav">
      <Link to="/browse" className="topnav__brand" aria-label="TryQ8Flix home">
        <img
          className="topnav__logo"
          src={`${import.meta.env.BASE_URL}Untitled-192.png`}
          alt=""
        />
        TRYQ8FLIX
      </Link>
      <nav className="topnav__links" aria-label="Main">
        {LINKS.map((link) => (
          <NavLink
            key={link.to}
            to={link.to}
            className={({ isActive }) => `topnav__link${isActive ? ' is-active' : ''}`}
          >
            {link.label}
          </NavLink>
        ))}
      </nav>
    </header>
  );
}
