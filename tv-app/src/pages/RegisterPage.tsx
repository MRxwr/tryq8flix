import { useState, type FormEvent } from 'react';
import { Link, useNavigate } from 'react-router-dom';
import { IonPage } from '@ionic/react';
import { motion } from 'framer-motion';
import { api } from '../api/endpoints';
import { errorMessage } from '../utils/errors';

export function RegisterPage() {
  const navigate = useNavigate();
  const [username, setUsername] = useState('');
  const [email, setEmail] = useState('');
  const [password, setPassword] = useState('');
  const [confirm, setConfirm] = useState('');
  const [error, setError] = useState<string | null>(null);
  const [busy, setBusy] = useState(false);

  async function submit(event: FormEvent) {
    event.preventDefault();
    setBusy(true);
    setError(null);
    try {
      await api.register(username.trim(), email.trim(), password, confirm);
      // The register endpoint does not return a session token, so sign in from the login screen.
      navigate('/login', { replace: true, state: { notice: 'Account created. Sign in to continue.' } });
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
        <h2>Create account</h2>
        {error ? <p className="notice notice--error" role="alert">{error}</p> : null}

        <label className="field">
          <span>Username</span>
          <input data-tv-autofocus autoComplete="username" value={username} onChange={(e) => setUsername(e.target.value)} required />
        </label>
        <label className="field">
          <span>Email</span>
          <input type="email" autoComplete="email" value={email} onChange={(e) => setEmail(e.target.value)} required />
        </label>
        <label className="field">
          <span>Password</span>
          <input type="password" autoComplete="new-password" value={password} onChange={(e) => setPassword(e.target.value)} required />
        </label>
        <label className="field">
          <span>Confirm password</span>
          <input type="password" autoComplete="new-password" value={confirm} onChange={(e) => setConfirm(e.target.value)} required />
        </label>

        <button type="submit" className="btn btn--primary btn--block" data-tv disabled={busy}>
          {busy ? 'Creating…' : 'Create account'}
        </button>
        <div className="auth-links">
          <Link to="/login">Back to sign in</Link>
        </div>
      </motion.form>
    </IonPage>
  );
}
