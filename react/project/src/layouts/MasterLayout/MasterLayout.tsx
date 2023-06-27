import React from 'react';
import { IProps } from './types';
import { Box } from '@chakra-ui/react';
import Container from '~components/Container';
import LayoutHeader from './LayoutHeader';

function MasterLayout({ children }: PropsType<IProps>) {
  return (
    <Box
      minHeight="100vh"
      // maxHeight="100vh"
      overflowY="auto"
      pb={25}
    >
      <LayoutHeader />
      <Box marginTop={17}>
        <Container>{children}</Container>
      </Box>
    </Box>
  );
}

export default MasterLayout;
