import React, { Fragment } from 'react';
import { IFavicon } from './types';
import png32x32 from '~assets/favicon/favicon-32x32.png';
import png64x64 from '~assets/favicon/favicon-64x64.png';
import png72x72 from '~assets/favicon/favicon-72x72.png';
import png76x76 from '~assets/favicon/favicon-76x76.png';
import png114x114 from '~assets/favicon/favicon-114x114.png';
import png120x120 from '~assets/favicon/favicon-120x120.png';
import png128x128 from '~assets/favicon/favicon-128x128.png';
import png144x144 from '~assets/favicon/favicon-144x144.png';
import png152x152 from '~assets/favicon/favicon-152x152.png';
import png167x167 from '~assets/favicon/favicon-167x167.png';
import png180x180 from '~assets/favicon/favicon-180x180.png';
import png192x192 from '~assets/favicon/favicon-192x192.png';
import png256x256 from '~assets/favicon/favicon-256x256.png';
import png512x512 from '~assets/favicon/favicon-512x512.png';
import faviconIcon from '~assets/favicon/favicon.ico';

interface ILinkFavicon {
  rel?: string;
  type?: string;
  href?: string;
  sizes?: string;
}

const elementsWebIcons: Array<Omit<IFavicon, 'rel'>> = [
  { type: 'image/png', href: png32x32.src },
  { type: 'image/x-icon', href: faviconIcon.src },
];

const elementsAppleIcons: Array<Omit<IFavicon, 'rel' | 'type'>> = [
  { sizes: '32x32', href: png32x32.src },
  { sizes: '64x64', href: png64x64.src },
  { sizes: '72x72', href: png72x72.src },
  { sizes: '76x76', href: png76x76.src },
  { sizes: '114x114', href: png114x114.src },
  { sizes: '120x120', href: png120x120.src },
  { sizes: '128x128', href: png128x128.src },
  { sizes: '144x144', href: png144x144.src },
  { sizes: '152x152', href: png152x152.src },
  { sizes: '167x167', href: png167x167.src },
  { sizes: '180x180', href: png180x180.src },
  { sizes: '192x192', href: png192x192.src },
  { sizes: '256x256', href: png256x256.src },
  { sizes: '512x512', href: png512x512.src },
];

const commonIcons: Array<ILinkFavicon> = [
  ...elementsWebIcons.map((elementsWebIcon) => ({
    ...elementsWebIcon,
    rel: 'icon',
  })),
  ...elementsAppleIcons.map((elementsAppleIcon) => ({
    ...elementsAppleIcon,
    type: 'image/png',
    rel: 'apple-touch-icon',
  })),
];

function WebsiteFavicon() {
  return (
    <Fragment>
      {commonIcons.map((iconProps: ILinkFavicon, index: number) => (
        <link key={`WebsiteFavicon/${index}`} {...iconProps} />
      ))}
    </Fragment>
  );
}

export default WebsiteFavicon;
