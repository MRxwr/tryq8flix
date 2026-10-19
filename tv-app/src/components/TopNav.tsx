import { Link, NavLink } from 'react-router-dom';

const LINKS = [
  { to: '/home', label: 'Home' },
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
      <Link to="/home" className="topnav__brand" aria-label="TryQ8Flix home">
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
