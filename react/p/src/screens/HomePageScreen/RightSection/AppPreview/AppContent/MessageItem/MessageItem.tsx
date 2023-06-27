import React, { useMemo } from 'react';
import { IProps } from './types';
import { Box, HStack, Text } from '@chakra-ui/react';

function MessageItem(props: PropsType<IProps>) {
  const content = useMemo(
    () => (
      <>
        <Text fontSize="13px" lineHeight={'15px'}>
          {props.message.text}
        </Text>
        <Text
          fontWeight={'bold'}
          fontSize={'10px'}
          lineHeight={'11px'}
          textAlign={'right'}
        >
          {props.message.time}
        </Text>
      </>
    ),
    [props.message.text, props.message.time]
  );

  return (
    <Box
      backgroundColor={'rgba(255, 255, 255, 1)'}
      paddingY={'10px'}
      paddingX={'15px'}
      borderRadius={'8px'}
      borderWidth={'1px'}
      maxWidth={'90%'}
      alignSelf={'self-start'}
      mb={'4px'}
    >
      {props.message.text.length > 40 ? (
        content
      ) : (
        <HStack alignItems={'end'} spacing={'15px'}>
          {content}
        </HStack>
      )}
    </Box>
  );
}

export default MessageItem;
