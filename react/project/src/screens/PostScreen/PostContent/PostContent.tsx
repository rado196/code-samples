import React, { useMemo } from 'react';
import { IProps } from './types';
import { Box, HStack, Image, Text } from '@chakra-ui/react';
import { sdk } from '@443-how/sdk';
import * as utils from '@443-how/utils';
import LinkSection from '~components/LinkSection';
import { getDateAndTime } from './helpers';

function PostContent(props: PropsType<IProps>) {
  const post = useMemo(
    () => ({
      personName: props.post.person.name || props.post.person.username,
      personAvatar: sdk.storage.buildPath(props.post.person.avatarUrl),
      dateTime: getDateAndTime(new Date(props.post.date)),
      message: utils.common
        .parseInfo(
          utils.time.updateUtcToCurrentTime(props.post.content || ''),
          (_id: string, name: string) => name,
          (_id: string, name: string) => name
        )
        .replace(/\n/g, '<br />'),
    }),
    [props.post]
  );

  return (
    <>
      <Box
        backgroundColor={'#fff'}
        borderWidth={'#1px'}
        borderColor={'#dddfe2'}
        borderRadius={'10px'}
        boxShadow={'base'}
        padding={{ base: '15px', lg: '20px' }}
        paddingRight={'15px'}
        maxWidth={{ base: '80%', lg: '655px' }}
        marginLeft={{ base: '10%', lg: '0' }}
      >
        <HStack>
          <Box
            backgroundColor={'rgba(229, 229, 229, 1)'}
            borderRadius={'50%'}
            padding={{ base: '2px', lg: '4px' }}
            width={{ base: '60px', lg: '90px' }}
            height={{ base: '60px', lg: '90px' }}
            marginRight={{ base: '5px', lg: '15px' }}
          >
            <Image
              width={{ base: '56px', lg: '82px' }}
              height={{ base: '56px', lg: '82px' }}
              draggable={false}
              src={post.personAvatar}
            />
          </Box>
          <Box flex={1}>
            <Text fontWeight={'bold'} mb={{ base: '0', lg: '12px' }}>
              {post.personName}
            </Text>

            <Text
              fontWeight={'bold'}
              fontSize={'12px'}
              display={{ base: 'block', lg: 'none' }}
            >
              {post.dateTime}
            </Text>

            <Text
              display={{ base: 'none', lg: 'block' }}
              lineHeight={'20px'}
              dangerouslySetInnerHTML={{ __html: post.message }}
            />

            <Text
              display={{ base: 'none', lg: 'block' }}
              textAlign={'right'}
              fontWeight={'bold'}
              mt={'15px'}
              mr={'8px'}
              fontSize={'12px'}
            >
              {post.dateTime}
            </Text>
          </Box>
        </HStack>

        <Box display={{ base: 'block', lg: 'none' }} mt={'10px'}>
          <Text dangerouslySetInnerHTML={{ __html: post.message }} />
        </Box>
      </Box>

      <Box maxWidth={'460px'} margin={'auto'} mt={'80px'} mb={'40px'}>
        <Text
          fontSize="18px"
          lineHeight="25px"
          textAlign={'center'}
          marginBottom={'40px'}
          fontWeight={600}
          paddingX={'35px'}
        >
          Download 443 and stay connected to the beautiful game in a way that’s
          never been possible before.
        </Text>
        <LinkSection />
      </Box>
    </>
  );
}

export default PostContent;
