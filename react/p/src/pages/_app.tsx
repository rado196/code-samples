import React from 'react';
import { AppProps } from 'next/app';
import { ChakraProvider } from '@chakra-ui/react';
import { sdk } from '@443-how/sdk';
import theme from '../theme';
import '../theme/fonts';

sdk.storage.configure({
  region: process.env.NEXT_PUBLIC_AWS_S3_REGION!,
  bucket: process.env.NEXT_PUBLIC_AWS_S3_BUCKET!,
});

sdk.configure({
  baseUrl: process.env.NEXT_PUBLIC_API_URL!,
  tokenKey: process.env.NEXT_PUBLIC_API_TOKEN!,
});

function App({ Component, pageProps }: PropsType<AppProps>) {
  return (
    <ChakraProvider theme={theme}>
      <Component {...pageProps} />
    </ChakraProvider>
  );
}

export default App;
