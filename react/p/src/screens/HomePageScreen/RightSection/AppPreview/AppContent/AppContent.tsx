import React, { Fragment } from 'react';
import { IProps } from '../types';
import { Box, Flex } from '@chakra-ui/react';
import MessagePerson from './MessagePerson';
import MessageItem from './MessageItem';

function AppContent(props: PropsType<IProps>) {
  return (
    <Box
      height={'100%'}
      width={'100%'}
      backgroundColor={'#EEF2F5'}
      borderBottomWidth={1}
    >
      <Flex ml={'10px'} direction={'column'}>
        {props.appPreview?.messages.map(
          (messageGroup: IAppPreviewMessageGroup, groupIndex: number) => (
            <Fragment
              key={`AppContent-MessageGroup-${props.appPreview?.community.name}-${groupIndex}`}
            >
              <MessagePerson person={messageGroup.person} />

              {messageGroup.messages.map(
                (message: IAppPreviewMessageItem, messageIndex: number) => (
                  <MessageItem
                    key={`AppContent-MessageItem-${props.appPreview?.community.name}-${groupIndex}-${messageIndex}`}
                    message={message}
                  />
                )
              )}
            </Fragment>
          )
        )}
      </Flex>
    </Box>
  );
}

export default AppContent;
