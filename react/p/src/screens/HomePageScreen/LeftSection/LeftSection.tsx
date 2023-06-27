import React from 'react';
import { IProps } from './types';
import { Box, Heading, Text } from '@chakra-ui/react';
import LinkSection from '~components/LinkSection';

function LeftSection(props: PropsType<IProps>) {
  return (
    <Box>
      <Heading as="h1" size="xl" mt={'60px'} mb="40px">
        Experience a sense of belonging to the squad
      </Heading>

      <Text marginBottom="60px" fontSize="18px" lineHeight="25px">
        If you’re a die-hard football fan, then 443 is the app for you. Get
        behind-the-scenes access to insider news straight from the footballer’s
        mouth. Stay up-to-date with the latest updates from your favorite clubs
        and players, all in one place.
        <br />
        <br />
        Download 443 and stay connected to the beautiful game in a way that’s
        never been possible before.
      </Text>

      <LinkSection />
    </Box>
  );
}

export default LeftSection;
