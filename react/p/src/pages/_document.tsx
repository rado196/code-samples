import React from 'react';
import { Html, Head, Main, NextScript } from 'next/document';
import { ColorModeScript } from '@chakra-ui/react';

export default function Document() {
  return (
    <Html>
      <Head />

      <body>
        <ColorModeScript />

        <div className="body-cover-window" />
        <div className="body-cover-content">
          <Main />
        </div>

        <NextScript />
      </body>
    </Html>
  );
}
