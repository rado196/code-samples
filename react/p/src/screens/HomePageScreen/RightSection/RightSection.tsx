import React, { useLayoutEffect, useState } from 'react';
import { IProps } from './types';
import { Flex } from '@chakra-ui/react';
import AppPreview from './AppPreview';
import appPreviews from '~constants/app-previews/index';

function RightSection(props: PropsType<IProps>) {
  const [appPreview, setAppPreview] = useState<Nullable<IAppPreview>>(null);

  useLayoutEffect(() => {
    const randomIndex = Math.floor(Math.random() * appPreviews.length);
    setAppPreview(appPreviews[randomIndex]);
  }, []);

  return (
    <Flex width="100%" justifyContent={{ base: 'center', lg: 'flex-end' }}>
      <AppPreview appPreview={appPreview} />
    </Flex>
  );
}

export default RightSection;
