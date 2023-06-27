import React from 'react';
import { IProps } from './types';
import Link from 'next/link';
import { Box, Flex } from '@chakra-ui/react';
import Container from '~components/Container';
import imgLogo from '~assets/images/logo/logo.svg';
import Navbar from './Navbar/Navbar';

function LayoutHeader(props: PropsType<IProps>) {
  return (
    <Container>
      <Flex justifyContent="space-between" alignItems="center" paddingTop={6}>
        <Link href="/">
          <Box
            width={20}
            height={16}
            backgroundImage={imgLogo.src}
            backgroundRepeat="no-repeat"
            backgroundPosition="center"
            backgroundSize="contina"
          />
        </Link>

        <Navbar />
      </Flex>
    </Container>
  );
}

export default LayoutHeader;
