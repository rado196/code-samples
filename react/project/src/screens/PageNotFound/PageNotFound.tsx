import React, { useEffect } from 'react';
import { IProps } from './types';
import { useRouter } from 'next/router';
import { Container, Stack, Text } from '@chakra-ui/react';
import MasterLayout from '~layouts/MasterLayout';
import PageHead from '~components/PageHead';

function PageNotFound(props: ScreenPropsType<IProps>) {
  const router = useRouter();

  useEffect(() => {
    const url = router.asPath || '';
    if (url !== '/404') {
      router.replace('/404');
    }
  }, [router.asPath]);

  return (
    <MasterLayout>
      <PageHead seo={false} title="Page Not Found" />

      <Container maxW={'5xl'} marginX="auto">
        <Stack
          textAlign={'center'}
          align={'center'}
          spacing={{ base: 8, md: 10 }}
          pt={{ base: 40, md: 48 }}
        >
          <Text
            fontWeight={'bold'}
            fontSize={'200px'}
            fontFamily={'monospace'}
            lineHeight={'160px'}
          >
            404
          </Text>
          <Text fontSize={20}>
            The page you were looking for does not exist..
          </Text>
        </Stack>
      </Container>
    </MasterLayout>
  );
}

export default PageNotFound;
