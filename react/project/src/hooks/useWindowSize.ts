import { useCallback, useLayoutEffect, useState } from 'react';

function getSizeWithFallback() {
  const result = {
    screen: { height: 0, width: 0 },
    page: { height: 0, width: 0 },
  };

  if (typeof window !== 'undefined') {
    result.screen.height = window?.innerHeight || 0;
    result.screen.width = window?.innerWidth || 0;
    result.page.height = document?.body?.clientHeight || 0;
    result.page.width = document?.body?.clientWidth || 0;
  }

  return result;
}

function useWindowSize() {
  const [size, setSize] = useState(() => getSizeWithFallback());

  const onResize = useCallback(() => {
    setSize(getSizeWithFallback());
  }, []);

  useLayoutEffect(() => {
    window?.addEventListener('resize', onResize);
    onResize();

    return () => {
      window?.removeEventListener('resize', onResize);
    };
  }, []);

  return {
    ...size,
    trigger: onResize,
  };
}

export default useWindowSize;
