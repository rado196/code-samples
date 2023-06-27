import React, { useEffect, useState } from 'react';
import { IProps } from './types';
import useWindowSize from '~hooks/useWindowSize';
import MobileNavbar from './MobileNavbar';
import DesktopNavbar from './DesktopNavbar';

function Navbar(props: PropsType<IProps>) {
  const [checking, setChecking] = useState<boolean>(true);
  const [isMobile, setIsMobile] = useState<boolean>(true);

  const { screen } = useWindowSize();

  useEffect(() => {
    setIsMobile(screen.width < 540);
    setChecking(false);
  }, [screen.width]);

  if (checking) {
    return null;
  }

  return isMobile ? <MobileNavbar /> : <DesktopNavbar />;
}

export default Navbar;
