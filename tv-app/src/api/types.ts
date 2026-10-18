/** Every endpoint answers with this envelope (admin/includes/functions/general.php). */
export interface ApiEnvelope<T> {
  ok: boolean;
  error: string;
  status: string;
  data: T;
}

export interface MainServer {
  id: number;
  name: string;
}

export interface Banner {
  id: number;
  title: string;
  endpoint: string;
  server: number | string;
  url: string;
  imageurl: string;
}

export interface MainData {
  servers: MainServer[];
  banners?: Banner[];
}

/** One card from a source listing (Home&action=view). */
export interface Show {
  title: string;
  image: string;
  href: string;
  category?: string;
  categoryType?: string;
  videoType?: string;
  episode?: string;
  description?: string;
  backdrop?: string;
  date?: string;
}

export interface MoreItem {
  title: string;
  link: string;
}

/** Title details (More&action=list). Movies have no seasons or episodes. */
export interface MoreData {
  title?: string;
  overview?: string;
  release_date?: string;
  backdrop?: string;
  poster?: string;
  seasons?: MoreItem[];
  episodes?: MoreItem[];
}

/** A playable server for an episode or movie (Servers&action=list). */
export interface ServerLink {
  name: string;
  link: string;
  backdrop?: string;
  date?: string;
  overview?: string;
}

export interface LiveMatch {
  href: string;
  leftTeamName: string;
  rightTeamName: string;
  leftTeamLogo: string;
  rightTeamLogo: string;
  matchTime: string;
  result: string;
  liveStatus: string;
  league: string;
  channel: string;
  commentator: string;
}

/** A live player option (Live&action=match). */
export interface LivePlayer {
  live: string;
  name: string;
  src?: string;
}

/** Favourites and history rows share one shape. */
export interface SavedItem {
  id: string;
  userId?: string;
  server: string;
  title: string;
  poster: string;
  link: string;
  date: string;
}

export interface UserProfile {
  id: string;
  username: string;
  email: string;
  avatar: string;
}

export interface AppVersion {
  version: string;
  iosLink: string;
  androidLink: string;
  windowsLink: string;
}

export interface LoginData {
  keepalive: string;
  username: string;
  id: string;
}

export interface MsgData {
  msg: string;
}

export interface GameItem {
  id: string;
  title: string;
  description: string;
  image: string;
  tag: string;
}
