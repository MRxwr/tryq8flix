import { useState, type FormEvent } from 'react';
import { Link } from 'react-router-dom';
import { IonPage } from '@ionic/react';
import { motion } from 'framer-motion';
import { api } from '../api/endpoints';
import { errorMessage } from '../utils/errors';

export function ForgotPage() {
  const [email, setEmail] = useState('');
  const [message, setMessage] = useState<{ tone: 'ok' | 'error'; text: string } | null>(null);
  const [busy, setBusy] = useState(false);

  async function submit(event: FormEvent) {
    event.preventDefault();
    setBusy(true);
    setMessage(null);
    try {
      const data = await api.forget(email.trim());
      setMessage({ tone: 'ok', text: data.msg });
    } catch (err) {
      setMessage({ tone: 'error', text: errorMessage(err) });
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
        <h2>Reset password</h2>
        <p className="muted">Enter the email on your account and we will send a new password.</p>
        {message ? (
          <p className={`notice${message.tone === 'error' ? ' notice--error' : ''}`} role="status">{message.text}</p>
        ) : null}

        <label className="field">
          <span>Email</span>
          <input data-tv-autofocus type="email" autoComplete="email" value={email} onChange={(e) => setEmail(e.target.value)} required />
        </label>

        <button type="submit" className="btn btn--primary btn--block" data-tv disabled={busy}>
          {busy ? 'Sending…' : 'Send new password'}
        </button>
        <div className="auth-links">
          <Link to="/login">Back to sign in</Link>
        </div>
      </motion.form>
    </IonPage>
  );
}
