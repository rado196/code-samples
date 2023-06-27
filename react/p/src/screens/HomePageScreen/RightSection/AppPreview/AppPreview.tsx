import React, { useLayoutEffect, useState } from 'react';
import { IProps } from './types';
import { Center, Spinner, VStack } from '@chakra-ui/react';

import AppHeader from './AppHeader';
import AppContent from './AppContent';

function delayed(callback: () => void) {
  return setTimeout(callback, 200);
}

function AppPreview(props: PropsType<IProps>) {
  const [loading, setLoading] = useState<boolean>(true);
  const [loaded, setLoaded] = useState<boolean>(false);

  useLayoutEffect(() => {
    if (!props.appPreview) {
      return;
    }

    delayed(() => {
      setLoading(false);
      delayed(() => setLoaded(true));
    });
  }, [props.appPreview]);

  return (
    <VStack
      width={'340px'}
      height={'680px'}
      borderColor={'#000'}
      borderWidth={10}
      borderRadius={40}
      overflow={'hidden'}
      spacing={0}
      position={'relative'}
    >
      {props.appPreview && (
        <>
          <AppHeader appPreview={props.appPreview} />
          <AppContent appPreview={props.appPreview} />
        </>
      )}

      {!loaded && (
        <Center
          position={'absolute'}
          zIndex={999}
          top={0}
          bottom={0}
          left={0}
          right={0}
          backgroundColor={'white'}
          transition={'opacity 0.2s'}
          opacity={loading ? 1 : 0}
        >
          <Spinner />
        </Center>
      )}
    </VStack>
  );
}

export default AppPreview;
