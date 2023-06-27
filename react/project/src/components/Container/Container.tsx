import React from 'react';
import { IProps } from './types';
import { Box } from '@chakra-ui/react';

function Container(props: PropsType<IProps>) {
  return (
    <Box
      {...props}
      maxWidth="960px"
      marginX="auto"
      marginTop={0}
      marginBottom={0}
      paddingLeft={6}
      paddingRight={6}
    >
      {props.children}
    </Box>
  );
}

export default Container;
