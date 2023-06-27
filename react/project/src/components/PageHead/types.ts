export interface IProps {
  seo?: boolean;
  title: string;
  description?: string;
  keywords?: Array<string>;
  url?: string;
  type?: 'article' | 'website';
  image?: string;
}

export interface IFavicon {
  rel: 'icon' | 'apple-touch-icon';
  type: 'image/x-icon' | 'image/png';
  href: string;
  sizes?:
    | '32x32'
    | '64x64'
    | '72x72'
    | '76x76'
    | '114x114'
    | '120x120'
    | '128x128'
    | '144x144'
    | '152x152'
    | '167x167'
    | '180x180'
    | '192x192'
    | '256x256'
    | '512x512';
}
