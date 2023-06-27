import React from 'react';
import { IProps } from './types';
import { Box, VStack, Image } from '@chakra-ui/react';
import Link from 'next/link';
import imgLogo from '~assets/images/logo/logo.svg';

function MinimalLayout({ children }: PropsType<IProps>) {
  return (
    <VStack minHeight="100vh" width="100vw" flexDirection="column">
      <Box marginY="30px">
        <Link href="/">
          <Image src={imgLogo.src} alt="443" width="120px" height="120px" />
        </Link>
      </Box>

      <Box minHeight="200px">{children}</Box>
    </VStack>
  );
}

export default MinimalLayout;
