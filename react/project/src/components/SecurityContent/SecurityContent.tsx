import React, { Fragment } from 'react';
import { IProps } from './types';
import { Box, Heading, ListItem, Text, UnorderedList } from '@chakra-ui/react';

function SecurityContent(props: PropsType<IProps>) {
  return (
    <>
      <Box marginBottom="44px">
        <Heading as="h2" size="lg">
          {props.content.title}
        </Heading>
        <Text fontSize="12px">Updated at {props.content.lastUpdatedAt}</Text>
      </Box>

      {props.content.blocks.map((block, indexBlock) => (
        <Box key={`SecurityContent-${indexBlock}`} marginBottom="44px">
          {block.heading && (
            <Heading as="h3" variant="policy-heading">
              {block.heading}
            </Heading>
          )}

          {block.content.map((content, indexContent) => (
            <Fragment key={`SecurityContent-${indexBlock}-${indexContent}`}>
              {typeof content === 'string' ? (
                <Text variant="policy-text">{content}</Text>
              ) : (
                <UnorderedList marginBottom="10px">
                  {content.map((contentItem, indexItem) => (
                    <ListItem
                      key={`SecurityContent-${indexBlock}-${indexContent}-${indexItem}`}
                    >
                      {typeof contentItem === 'string' ? (
                        <>{contentItem}</>
                      ) : (
                        <>
                          {contentItem.section}
                          {contentItem.content.map(
                            (contentItemRow, indexRow) => (
                              <Text
                                key={`SecurityContent-${indexBlock}-${indexContent}-${indexItem}-${indexRow}`}
                                variant="policy-text"
                              >
                                {contentItemRow}
                              </Text>
                            )
                          )}
                        </>
                      )}
                    </ListItem>
                  ))}
                </UnorderedList>
              )}
            </Fragment>
          ))}
        </Box>
      ))}
    </>
  );
}

export default SecurityContent;
