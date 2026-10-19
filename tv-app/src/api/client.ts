import { API_BASE } from '../config';
import { getToken } from '../auth/session';
import type { ApiEnvelope } from './types';

export class ApiError extends Error {
  readonly status: number;

  constructor(message: string, status = 0) {
    super(message);
    this.name = 'ApiError';
    this.status = status;
  }
}

/** Fired on window when the server rejects the token, so the app can sign out. */
export const AUTH_EXPIRED_EVENT = 'tryq8flix:auth-expired';

type Scalar = string | number | boolean | null | undefined;

export interface RequestOptions {
  /** Query-string parameters (action, server, page, ...). */
  params?: Record<string, Scalar>;
  /** Form fields. When present the request is a POST with url-encoded body. */
  form?: Record<string, Scalar>;
}

function buildUrl(endpoint: string, params: Record<string, Scalar> = {}): string {
  const search = new URLSearchParams({ endpoint });
  for (const [key, value] of Object.entries(params)) {
    if (value !== undefined && value !== null && value !== '') {
      search.set(key, String(value));
    }
  }
  return `${API_BASE}/api/index.php?${search.toString()}`;
}

function messageOf(data: unknown): string | undefined {
  if (data && typeof data === 'object' && 'msg' in data) {
    const msg = (data as { msg: unknown }).msg;
    if (typeof msg === 'string') return msg;
  }
  return undefined;
}

/**
 * Calls api/index.php and returns `data` on success.
 * Throws ApiError with the server's message on failure.
 */
export async function apiRequest<T>(endpoint: string, options: RequestOptions = {}): Promise<T> {
  const headers: Record<string, string> = { Accept: 'application/json' };
  const token = getToken();
  if (token) headers.Authorization = `Bearer ${token}`;

  const init: RequestInit = { method: 'GET', headers };
  if (options.form) {
    const body = new URLSearchParams();
    for (const [key, value] of Object.entries(options.form)) {
      if (value !== undefined && value !== null) body.set(key, String(value));
    }
    init.method = 'POST';
    init.body = body.toString();
    headers['Content-Type'] = 'application/x-www-form-urlencoded';
  }

  let response: Response;
  try {
    response = await fetch(buildUrl(endpoint, options.params), init);
  } catch {
    throw new ApiError('Network error. Check your connection and try again.');
  }

  const text = await response.text();
  let envelope: ApiEnvelope<unknown>;
  try {
    envelope = JSON.parse(text) as ApiEnvelope<unknown>;
  } catch {
    throw new ApiError('The server sent an unexpected response.', response.status);
  }

  if (!envelope.ok) {
    const message = messageOf(envelope.data) ?? 'Request failed.';
    if (message === 'Invalid token') {
      window.dispatchEvent(new Event(AUTH_EXPIRED_EVENT));
    }
    throw new ApiError(message, response.status);
  }

  return envelope.data as T;
}
