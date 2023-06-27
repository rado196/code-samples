import React from 'react';
import { IProps } from './types';
import Link from 'next/link';
import { Flex } from '@chakra-ui/react';
import links, { ILink } from '../links';

function DesktopNavbar(props: PropsType<IProps>) {
  return (
    <Flex alignItems="center" gap={6}>
      {links.map((link: ILink, index: number) => (
        <Link key={`DesktopNavbar-${index}##${link.link}`} href={link.link}>
          {link.text}
        </Link>
      ))}
    </Flex>
  );
}

export default DesktopNavbar;
