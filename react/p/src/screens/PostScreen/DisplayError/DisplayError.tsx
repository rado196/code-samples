/* eslint-disable no-console */

import React, { useEffect } from 'react';
import { IProps } from './types';
import { useRouter } from 'next/router';
import { Box, Text } from '@chakra-ui/react';

function DisplayError(props: IProps) {
  const router = useRouter();
  useEffect(() => {
    setTimeout(() => {
      router.replace('/');
    }, 3000);
  }, []);

  console.error('>>> error caught:', props.error);
  console.error('>>> error info:', props.exception);

  return (
    <Box
      backgroundColor={'#fff'}
      borderWidth={'#1px'}
      borderColor={'#dddfe2'}
      borderRadius={'10px'}
      boxShadow={'base'}
      padding={{ base: '15px', lg: '20px' }}
      paddingRight={'15px'}
      maxWidth={{ base: '80%', lg: '655px' }}
      minWidth={{ base: '80%', lg: '400px' }}
      marginLeft={{ base: '10%', lg: '0' }}
    >
      <Text textAlign="center">
        <Text fontWeight="bold" color="orangered" fontSize="14px">
          Exception caught:
        </Text>
        <Text fontWeight="bold" color="red" fontSize="20px">
          {props.error}
        </Text>
      </Text>
    </Box>
  );
}

export default DisplayError;
