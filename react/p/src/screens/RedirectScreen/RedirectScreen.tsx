import React, { useLayoutEffect, useState } from 'react';
import { IProps, OsEnum } from './types';
import MinimalLayout from '~layouts/MinimalLayout/MinimalLayout';
import PageHead from '~components/PageHead';
import { Spinner, VStack, Text } from '@chakra-ui/react';

function detectOs(): OsEnum {
  const userAgent = window?.navigator?.userAgent;
  if (userAgent) {
    if (/android/i.test(userAgent)) {
      return OsEnum.android;
    }
    if (/iPad|iPhone|iPod/.test(userAgent)) {
      return OsEnum.ios;
    }
  }

  return OsEnum.unknown;
}

function RedirectScreen(props: PropsType<IProps>) {
  const [checking, setChecking] = useState<boolean>(true);

  useLayoutEffect(() => {
    const os = detectOs();
    if (os === OsEnum.android) {
      window.location.href = process.env.NEXT_PUBLIC_ANDROID_PLAY_URL!;
    } else if (os === OsEnum.ios) {
      window.location.href = process.env.NEXT_PUBLIC_IOS_APPSTORE_URL!;
    } else {
      setChecking(false);

      //   setTimeout(() => {
      //     window.location.href = '/';
      //   }, 3000);
    }
  }, []);

  return (
    <MinimalLayout>
      <PageHead title="Redirecting ..." />

      {checking ? (
        <VStack>
          <Spinner width="30px" height="30px" size="lg" marginBottom="10px" />
          <Text fontWeight="bold" fontSize="13px">
            Redirecting ...
          </Text>
        </VStack>
      ) : (
        <Text
          fontWeight="bold"
          fontSize="14px"
          maxWidth="300px"
          textAlign="center"
          color="orange.600"
        >
          The app available for Android and iOS devices only.
        </Text>
      )}
    </MinimalLayout>
  );
}

export default RedirectScreen;
