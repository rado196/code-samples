import React from 'react';
import { IProps } from '../types';
import { Box, HStack, VStack, Text, Image } from '@chakra-ui/react';

function AppHeader(props: PropsType<IProps>) {
  return (
    <Box
      width={'100%'}
      pb={'8px'}
      backgroundColor={'#fff'}
      borderBottomWidth={1}
    >
      <HStack pt={'12px'} pl={'10px'}>
        <Image
          width={'45px'}
          borderRadius={'50%'}
          draggable={false}
          src={props.appPreview?.community.image.src}
        />
        <VStack
          alignItems={'flex-start'}
          justifyContent={'space-around'}
          spacing={0}
          ml={'2px'}
        >
          <Text
            fontSize="15px"
            lineHeight={'12px'}
            mt={'4px'}
            fontWeight={'bold'}
          >
            {props.appPreview?.community.name}
          </Text>
          <Text fontSize="12px" color={'rgba(0 ,0, 0, .5)'}>
            {props.appPreview?.community.membersCount} members
          </Text>
        </VStack>
      </HStack>
    </Box>
  );
}

export default AppHeader;
