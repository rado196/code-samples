import React from 'react';
import { IProps } from './types';
import { HStack, Box, Image, Text } from '@chakra-ui/react';

function MessagePerson(props: PropsType<IProps>) {
  return (
    <HStack
      justifyContent={'flex-start'}
      width={'100%'}
      mt={'10px'}
      marginBottom={'-8px'}
    >
      <Box
        backgroundColor={'rgba(229, 229, 229, 1)'}
        padding={'2px'}
        borderRadius={'50%'}
        zIndex={3}
      >
        <Image
          width={'55px'}
          borderRadius={'50%'}
          draggable={false}
          src={props.person.avatar.src}
        />
      </Box>
      <Text fontSize="15px" ml={'2px'} fontWeight={'bold'}>
        {props.person.name}
      </Text>
    </HStack>
  );
}

export default MessagePerson;
