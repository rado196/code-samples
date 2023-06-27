import React from 'react';
import { IProps } from './types';
import Head from 'next/head';
import WebsiteFavicon from './WebsiteFavicon';

const titlePrefix = '443';
function PageHead(props: PropsType<IProps>) {
  const title =
    typeof props.title !== 'string' || props.title.length === 0
      ? titlePrefix
      : `${titlePrefix} | ${props.title}`;

  return (
    <Head>
      <meta name="viewport" content="width=device-width, initial-scale=1.0" />
      <meta httpEquiv="X-UA-Compatible" content="IE=edge,chrome=1" />
      <link rel="sitemap" type="application/xml" href="/sitemap-index.xml" />
      <title>{title}</title>

      <WebsiteFavicon />

      {props.seo !== false && (
        <>
          {/* --- Meta tags: Begin ---  */}
          {props.description && (
            <meta name="description" content={props.description} />
          )}
          {Array.isArray(props.keywords) && (
            <meta name="keywords" content={props.keywords.join(',')} />
          )}
          {/* --- Meta tags: End ---  */}

          {/* --- OpenGraph: Begin ---  */}
          <meta property="og:site_name" content="443.how" />
          <meta property="og:type" content={props.type || 'article'} />
          <meta property="og:locale" content="en_US" />
          <meta property="og:locale:alternate" content="en_GB" />
          <meta property="og:title" content={title} />
          {props.url && <meta property="og:url" content={props.url} />}
          {props.image && (
            <>
              <meta property="og:image" content={props.image} />
              <meta property="og:image:alt" content="443" />
            </>
          )}
          {props.description && (
            <meta property="og:description" content={props.description} />
          )}
          {/* --- OpenGraph: End ---  */}

          {/* --- Twitter: Begin ---  */}
          <meta
            property="twitter:app:url:googleplay"
            content={process.env.NEXT_PUBLIC_ANDROID_PLAY_URL}
          />
          <meta
            property="twitter:app:url:iphone"
            content={process.env.NEXT_PUBLIC_IOS_APPSTORE_URL}
          />
          <meta property="twitter:card" content={props.type || 'article'} />
          <meta property="twitter:title" content={title} />
          {props.url && <meta property="twitter:url" content={props.url} />}
          {props.image && (
            <>
              <meta property="twitter:image" content={props.image} />
              <meta property="twitter:image:alt" content="443" />
            </>
          )}
          {props.description && (
            <meta property="twitter:description" content={props.description} />
          )}
          {/* --- Twitter: End ---  */}

          {props.children}
        </>
      )}
    </Head>
  );
}

export default PageHead;
