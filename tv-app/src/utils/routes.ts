export type QueryValue = string | number | null | undefined;

/** Encodes non-empty values into a query string. */
export function toQuery(params: Record<string, QueryValue>): string {
  const search = new URLSearchParams();
  for (const [key, value] of Object.entries(params)) {
    if (value !== null && value !== undefined && value !== '') search.set(key, String(value));
  }
  return search.toString();
}

interface Item {
  href: string;
  title: string;
  image?: string;
}

/** Title details page (series with seasons/episodes, or movies redirect to servers). */
export function titleRoute(server: QueryValue, item: Item): string {
  return `/title?${toQuery({ server, href: item.href, title: item.title, image: item.image })}`;
}

/** Server picker for an episode or movie page. */
export function serversRoute(server: QueryValue, item: Item): string {
  return `/servers?${toQuery({ server, href: item.href, title: item.title, image: item.image })}`;
}

export interface WatchParams {
  link: string;
  title?: string;
  server?: QueryValue;
  href?: string;
  image?: string;
  save?: boolean;
}

/** Player page. `save` controls whether the watch is recorded in history. */
export function watchRoute(params: WatchParams): string {
  return `/watch?${toQuery({
    link: params.link,
    title: params.title,
    server: params.server,
    href: params.href,
    image: params.image,
    save: params.save === false ? '0' : '1',
  })}`;
}
