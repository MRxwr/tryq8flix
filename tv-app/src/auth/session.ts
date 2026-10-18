const TOKEN_KEY = 'tryq8flix.keepalive';

export function getToken(): string | null {
  try {
    return window.localStorage.getItem(TOKEN_KEY);
  } catch {
    return null;
  }
}

export function saveToken(token: string): void {
  window.localStorage.setItem(TOKEN_KEY, token);
}

export function removeToken(): void {
  window.localStorage.removeItem(TOKEN_KEY);
}
