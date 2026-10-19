import { useState, type FormEvent } from 'react';
import { Link, useLocation, useNavigate } from 'react-router-dom';
import { IonPage } from '@ionic/react';
import { motion } from 'framer-motion';
import { api } from '../api/endpoints';
import { useAuth } from '../auth/AuthContext';
import { errorMessage } from '../utils/errors';

export function LoginPage() {
  const { signIn } = useAuth();
  const navigate = useNavigate();
  const location = useLocation();
  const notice = (location.state as { notice?: string } | null)?.notice;

  const [username, setUsername] = useState('');
  const [password, setPassword] = useState('');
  const [error, setError] = useState<string | null>(null);
  const [busy, setBusy] = useState(false);

  async function submit(event: FormEvent) {
    event.preventDefault();
    setBusy(true);
    setError(null);
    try {
      const data = await api.login(username.trim(), password);
      signIn(data.keepalive);
      navigate('/home', { replace: true });
    } catch (err) {
      setError(errorMessage(err));
    } finally {
      setBusy(false);
    }
  }

  return (
    <IonPage className="tv-page tv-auth">
      <motion.form
        className="auth-card"
        onSubmit={submit}
        initial={{ opacity: 0, y: 20 }}
        animate={{ opacity: 1, y: 0 }}
        transition={{ duration: 0.45, ease: 'easeOut' }}
      >
        <h1 className="auth-brand">TRYQ8FLIX</h1>
        <h2>Sign in</h2>
        {notice ? <p className="notice">{notice}</p> : null}
        {error ? <p className="notice notice--error" role="alert">{error}</p> : null}

        <label className="field">
          <span>Username</span>
          <input
            data-tv-autofocus
            autoComplete="username"
            value={username}
            onChange={(event) => setUsername(event.target.value)}
            required
          />
        </label>
        <label className="field">
          <span>Password</span>
          <input
            type="password"
            autoComplete="current-password"
            value={password}
            onChange={(event) => setPassword(event.target.value)}
            required
          />
        </label>

        <button type="submit" className="btn btn--primary btn--block" data-tv disabled={busy}>
          {busy ? 'Signing in…' : 'Sign in'}
        </button>

        <div className="auth-links">
          <Link to="/register">Create an account</Link>
          <Link to="/forgot">Forgot password?</Link>
        </div>
      </motion.form>
    </IonPage>
  );
}
