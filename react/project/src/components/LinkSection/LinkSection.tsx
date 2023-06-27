import React from 'react';
import { IProps } from './types';
import NextLink from 'next/link';
import { SimpleGrid, GridItem, Center, Image, Link } from '@chakra-ui/react';
import imgAndroidGooglePlay from '~assets/images/stores/google-play.svg';
import imgIosAppStore from '~assets/images/stores/app-store.svg';

function LinkSection(props: PropsType<IProps>) {
  return (
    <SimpleGrid gap={'25px'} columns={{ base: 1, lg: 2 }}>
      <GridItem>
        <Center>
          <Link
            as={NextLink}
            href={process.env.NEXT_PUBLIC_IOS_APPSTORE_URL}
            target="_blank"
          >
            <Image
              width={{ base: 260, lg: 200 }}
              draggable={false}
              src={imgIosAppStore.src}
              alt="443 - iOS"
            />
          </Link>
        </Center>
      </GridItem>
      <GridItem>
        <Center>
          <Link
            as={NextLink}
            href={process.env.NEXT_PUBLIC_ANDROID_PLAY_URL}
            target="_blank"
          >
            <Image
              width={{ base: 260, lg: 200 }}
              draggable={false}
              src={imgAndroidGooglePlay.src}
              alt="443 - Android"
            />
          </Link>
        </Center>
      </GridItem>
    </SimpleGrid>
  );
}

export default LinkSection;
