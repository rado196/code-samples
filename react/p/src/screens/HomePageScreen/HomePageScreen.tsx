import React from 'react';
import { IProps } from './types';
import { SimpleGrid, GridItem } from '@chakra-ui/react';
import MasterLayout from '~layouts/MasterLayout';
import PageHead from '~components/PageHead';
import LeftSection from './LeftSection';
import RightSection from './RightSection';

function HomePageScreen(props: ScreenPropsType<IProps>) {
  return (
    <MasterLayout>
      <PageHead title="" />

      <SimpleGrid gap={{ base: '50px', lg: 20 }} columns={{ base: 1, lg: 2 }}>
        <GridItem maxWidth={{ base: 'full', lg: '446px' }} paddingBottom={4}>
          <LeftSection />
        </GridItem>
        <GridItem maxWidth={'full'}>
          <RightSection />
        </GridItem>
      </SimpleGrid>
    </MasterLayout>
  );
}

export default HomePageScreen;
