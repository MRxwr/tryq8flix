import { apiRequest } from './client';
import type {
  AppVersion,
  GameItem,
  LiveMatch,
  LoginData,
  LivePlayer,
  MainData,
  MoreData,
  MsgData,
  SavedItem,
  ServerLink,
  Show,
  UserProfile,
} from './types';

interface ShowsData {
  shows?: Show[];
}

/** Some endpoints answer with a bare array, others with { matches: [...] }. */
function asArray<T>(value: T[] | { matches?: T[] } | null | undefined): T[] {
  if (Array.isArray(value)) return value;
  if (value && Array.isArray(value.matches)) return value.matches;
  return [];
}

export const api = {
  // Public
  main: () => apiRequest<MainData>('Main'),
  version: () => apiRequest<AppVersion>('Version', { params: { action: 'version' } }),
  games: async (): Promise<GameItem[]> => {
    const data = await apiRequest<{ games?: GameItem[] }>('Games/Home', { params: { action: 'list' } });
    return data.games ?? [];
  },

  // Sources (token required by the server, any non-empty token is accepted for scraping)
  shows: async (server: number | string, page = 1, search?: string): Promise<Show[]> => {
    const data = await apiRequest<ShowsData>('Home', { params: { action: 'view', server, page, search } });
    return data.shows ?? [];
  },
  more: (server: number | string, href: string) =>
    apiRequest<MoreData>('More', { params: { action: 'list', server, href } }),
  servers: async (server: number | string, href: string): Promise<ServerLink[]> => {
    const data = await apiRequest<ServerLink[] | null>('Servers', { params: { action: 'list', server, href } });
    return Array.isArray(data) ? data.filter((item) => Boolean(item.link)) : [];
  },

  // Favourites
  favorites: () => apiRequest<{ favorites?: SavedItem[] }>('Favorites', { params: { action: 'list' } })
    .then((data) => data.favorites ?? []),
  addFavorite: (item: { server: string | number; title: string; poster: string; link: string }) =>
    apiRequest<MsgData>('Favorites', { params: { action: 'add' }, form: item }),
  removeFavorite: (poster: string) =>
    apiRequest<MsgData>('Favorites', { params: { action: 'remove' }, form: { poster } }),
  isFavorite: (poster: string) =>
    apiRequest<{ isFavorite: boolean }>('Favorites', { params: { action: 'check' }, form: { poster } })
      .then((data) => data.isFavorite),

  // History
  history: () => apiRequest<{ history?: SavedItem[] }>('History', { params: { action: 'list' } })
    .then((data) => data.history ?? []),
  addHistory: (item: { server: string | number; title: string; poster: string; link: string }) =>
    apiRequest<MsgData>('History', { params: { action: 'add' }, form: item }),
  clearHistory: () => apiRequest<MsgData>('History', { params: { action: 'clear' }, form: {} }),

  // Live sports
  live: async (): Promise<LiveMatch[]> => asArray(await apiRequest<LiveMatch[]>('Live', { params: { action: 'live' } })),
  liveServers: async (match: string): Promise<LivePlayer[]> => {
    const data = await apiRequest<LivePlayer[] | { matches?: LivePlayer[] }>('Live', {
      params: { action: 'match', match },
    });
    return asArray(data).filter((player) => Boolean(player.live));
  },

  // Accounts
  login: (username: string, password: string) =>
    apiRequest<LoginData>('User', { params: { action: 'login' }, form: { username, password } }),
  register: (username: string, email: string, password: string, confirmPassword: string) =>
    apiRequest<MsgData>('User', {
      params: { action: 'register' },
      form: { username, email, password, confirmPassword },
    }),
  forget: (email: string) => apiRequest<MsgData>('User', { params: { action: 'forget' }, form: { email } }),
  logout: () => apiRequest<MsgData>('User', { params: { action: 'logout' } }),
  profile: () => apiRequest<UserProfile>('User', { params: { action: 'profile' } }),
  changePassword: (password: string, confirmPassword: string) =>
    apiRequest<MsgData>('User', { params: { action: 'change' }, form: { password, confirmPassword } }),
  deleteAccount: () => apiRequest<MsgData>('User', { params: { action: 'delete' }, form: {} }),
};
