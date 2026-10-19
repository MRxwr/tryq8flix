import { useEffect, useState, type FormEvent } from 'react';
import { useNavigate } from 'react-router-dom';
import { api } from '../api/endpoints';
import type { AppVersion, UserProfile } from '../api/types';
import { useAuth } from '../auth/AuthContext';
import { AppPage } from '../components/AppPage';
import { ErrorState, Spinner } from '../components/States';
import { errorMessage } from '../utils/errors';

export function SettingsPage() {
  const { signOut } = useAuth();
  const navigate = useNavigate();

  const [profile, setProfile] = useState<UserProfile | null>(null);
  const [version, setVersion] = useState<AppVersion | null>(null);
  const [error, setError] = useState<string | null>(null);
  const [notice, setNotice] = useState<string | null>(null);
  const [password, setPassword] = useState('');
  const [confirm, setConfirm] = useState('');
  const [confirmingDelete, setConfirmingDelete] = useState(false);
  const [busy, setBusy] = useState(false);

  useEffect(() => {
    let cancelled = false;
    api
      .profile()
      .then((data) => {
        if (!cancelled) setProfile(data);
      })
      .catch((err) => {
        if (!cancelled) setError(errorMessage(err));
      });
    api
      .version()
      .then((data) => {
        if (!cancelled) setVersion(data);
      })
      .catch(() => undefined);
    return () => {
      cancelled = true;
    };
  }, []);

  async function changePassword(event: FormEvent) {
    event.preventDefault();
    setBusy(true);
    setNotice(null);
    setError(null);
    try {
      const data = await api.changePassword(password, confirm);
      setNotice(data.msg);
      setPassword('');
      setConfirm('');
    } catch (err) {
      setError(errorMessage(err));
    } finally {
      setBusy(false);
    }
  }

  async function logout() {
    await signOut();
    navigate('/login', { replace: true });
  }

  async function deleteAccount() {
    setBusy(true);
    try {
      await api.deleteAccount();
      await signOut();
      navigate('/login', { replace: true, state: { notice: 'Your account was deleted.' } });
    } catch (err) {
      setError(errorMessage(err));
      setBusy(false);
    }
  }

  return (
    <AppPage>
      <div className="page-head">
        <h1>Account</h1>
      </div>

      {error ? <ErrorState message={error} /> : null}
      {notice ? <p className="notice" role="status">{notice}</p> : null}

      <div className="settings-grid">
        <section className="panel">
          <h2>Profile</h2>
          {profile ? (
            <dl className="facts">
              <dt>Username</dt>
              <dd>{profile.username}</dd>
              <dt>Email</dt>
              <dd>{profile.email}</dd>
            </dl>
          ) : (
            <Spinner label="Loading profile" />
          )}
          <div className="stack">
            <button type="button" className="btn btn--ghost" data-tv onClick={logout}>
              Sign out
            </button>
          </div>
        </section>

        <section className="panel">
          <h2>Change password</h2>
          <form className="stack" onSubmit={changePassword}>
            <label className="field">
              <span>New password</span>
              <input type="password" autoComplete="new-password" value={password} onChange={(e) => setPassword(e.target.value)} required />
            </label>
            <label className="field">
              <span>Confirm password</span>
              <input type="password" autoComplete="new-password" value={confirm} onChange={(e) => setConfirm(e.target.value)} required />
            </label>
            <button type="submit" className="btn btn--primary" data-tv disabled={busy}>
              Update password
            </button>
          </form>
        </section>

        <section className="panel">
          <h2>About the app</h2>
          <dl className="facts">
            <dt>Version</dt>
            <dd>{version?.version ?? '—'}</dd>
          </dl>
          <div className="stack">
            {!confirmingDelete ? (
              <button type="button" className="btn btn--danger" data-tv onClick={() => setConfirmingDelete(true)}>
                Delete account
              </button>
            ) : (
              <>
                <p className="muted">This removes your account. Continue?</p>
                <button type="button" className="btn btn--danger" data-tv onClick={deleteAccount} disabled={busy}>
                  Yes, delete my account
                </button>
                <button type="button" className="btn btn--ghost" data-tv onClick={() => setConfirmingDelete(false)}>
                  Cancel
                </button>
              </>
            )}
          </div>
        </section>
      </div>
    </AppPage>
  );
}
