import type { ReactNode } from 'react';
import { Navigate, Route } from 'react-router-dom';
import { IonApp, IonRouterOutlet, setupIonicReact } from '@ionic/react';
import { IonReactRouter } from '@ionic/react-router';

/* Ionic core styles (router outlet transitions, page layout). */
import '@ionic/react/css/core.css';
import '@ionic/react/css/normalize.css';
import '@ionic/react/css/structure.css';
import '@ionic/react/css/typography.css';
import '@ionic/react/css/padding.css';
import '@ionic/react/css/flex-utils.css';
import '@ionic/react/css/display.css';

import './styles/app.css';

import { AuthProvider } from './auth/AuthContext';
import { RequireAuth } from './auth/RequireAuth';
import { TvNavigationRoot } from './tv/TvNavigationRoot';
import { LoginPage } from './pages/LoginPage';
import { RegisterPage } from './pages/RegisterPage';
import { ForgotPage } from './pages/ForgotPage';
import { BrowsePage } from './pages/BrowsePage';
import { SearchPage } from './pages/SearchPage';
import { TitlePage } from './pages/TitlePage';
import { ServersPage } from './pages/ServersPage';
import { PlayerPage } from './pages/PlayerPage';
import { LivePage } from './pages/LivePage';
import { FavoritesPage } from './pages/FavoritesPage';
import { HistoryPage } from './pages/HistoryPage';
import { SettingsPage } from './pages/SettingsPage';

// Material-style transitions everywhere: consistent slide/fade on TV hardware.
setupIonicReact({ mode: 'md', animated: true });

const protectedRoute = (element: ReactNode) => <RequireAuth>{element}</RequireAuth>;

// '/' on Android, '/tv-app' when the web build is uploaded to https://tryq8flix.com/tv-app/.
const basename = import.meta.env.BASE_URL.replace(/\/$/, '') || undefined;

const App: React.FC = () => (
  <IonApp>
    <IonReactRouter basename={basename}>
      <AuthProvider>
        <TvNavigationRoot />
        <IonRouterOutlet>
          <Route path="/login" element={<LoginPage />} />
          <Route path="/register" element={<RegisterPage />} />
          <Route path="/forgot" element={<ForgotPage />} />

          <Route path="/home" element={<Navigate to="/browse" replace />} />
          <Route path="/browse" element={protectedRoute(<BrowsePage />)} />
          <Route path="/search" element={protectedRoute(<SearchPage />)} />
          <Route path="/title" element={protectedRoute(<TitlePage />)} />
          <Route path="/servers" element={protectedRoute(<ServersPage />)} />
          <Route path="/watch" element={protectedRoute(<PlayerPage />)} />
          <Route path="/live" element={protectedRoute(<LivePage />)} />
          <Route path="/favorites" element={protectedRoute(<FavoritesPage />)} />
          <Route path="/history" element={protectedRoute(<HistoryPage />)} />
          <Route path="/settings" element={protectedRoute(<SettingsPage />)} />

          <Route path="/" element={<Navigate to="/browse" replace />} />
          <Route path="*" element={<Navigate to="/browse" replace />} />
        </IonRouterOutlet>
      </AuthProvider>
    </IonReactRouter>
  </IonApp>
);

export default App;
