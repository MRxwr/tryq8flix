import { createContext, useCallback, useContext, useEffect, useMemo, useState, type ReactNode } from 'react';
import { api } from '../api/endpoints';
import { AUTH_EXPIRED_EVENT } from '../api/client';
import { getToken, removeToken, saveToken } from './session';

interface AuthValue {
  token: string | null;
  isAuthenticated: boolean;
  signIn: (token: string) => void;
  signOut: () => Promise<void>;
}

const AuthContext = createContext<AuthValue | null>(null);

export function AuthProvider({ children }: { children: ReactNode }) {
  const [token, setToken] = useState<string | null>(() => getToken());

  const clear = useCallback(() => {
    removeToken();
    setToken(null);
  }, []);

  const signIn = useCallback((next: string) => {
    saveToken(next);
    setToken(next);
  }, []);

  const signOut = useCallback(async () => {
    try {
      if (getToken()) await api.logout();
    } catch {
      // Sign out locally even if the server call fails.
    }
    clear();
  }, [clear]);

  useEffect(() => {
    window.addEventListener(AUTH_EXPIRED_EVENT, clear);
    return () => window.removeEventListener(AUTH_EXPIRED_EVENT, clear);
  }, [clear]);

  const value = useMemo<AuthValue>(
    () => ({ token, isAuthenticated: Boolean(token), signIn, signOut }),
    [token, signIn, signOut],
  );

  return <AuthContext.Provider value={value}>{children}</AuthContext.Provider>;
}

export function useAuth(): AuthValue {
  const value = useContext(AuthContext);
  if (!value) throw new Error('useAuth must be used inside AuthProvider');
  return value;
}
