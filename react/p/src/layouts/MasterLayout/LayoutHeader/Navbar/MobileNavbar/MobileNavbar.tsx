import React from 'react';
import { IProps } from './types';
import Link from 'next/link';
import {
  Drawer,
  DrawerBody,
  DrawerCloseButton,
  DrawerHeader,
  DrawerContent,
  DrawerOverlay,
  Box,
  Text,
  IconButton,
  useDisclosure,
} from '@chakra-ui/react';
import { HamburgerIcon } from '@chakra-ui/icons';
import links, { ILink } from '../links';

function MobileNavbar(props: PropsType<IProps>) {
  const { isOpen, onOpen, onClose } = useDisclosure();

  return (
    <>
      <IconButton
        right={0}
        top={0}
        zIndex={10}
        cursor="pointer"
        aria-label=""
        icon={<HamburgerIcon width={8} height={8} />}
        background="transparent"
        onClick={onOpen}
      />

      <Drawer
        closeOnEsc={true}
        closeOnOverlayClick={true}
        size="xs"
        isOpen={isOpen}
        onClose={onClose}
      >
        <DrawerOverlay />
        <DrawerContent>
          <DrawerCloseButton top={4} size="lg" />
          <DrawerHeader />

          <DrawerBody paddingTop={12}>
            {links.map((link: ILink, index: number) => (
              <Box
                key={`MobileNavbar-${index}##${link.link}`}
                paddingY={2}
                opacity={isOpen ? undefined : 0}
              >
                <Link href={link.link}>
                  <Text fontSize="20px">{link.text}</Text>
                </Link>
              </Box>
            ))}
          </DrawerBody>
        </DrawerContent>
      </Drawer>
    </>
  );
}

export default MobileNavbar;
