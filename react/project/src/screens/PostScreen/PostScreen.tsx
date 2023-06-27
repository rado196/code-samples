/* eslint-disable no-console */

import React, { useMemo } from 'react';
import { IProps } from './types';
import { GetServerSidePropsContext } from 'next';
import { types } from '@443-how/models';
import { sdk } from '@443-how/sdk';
import * as token from '@443-how/token';
import * as utils from '@443-how/utils';
import MinimalLayout from '~layouts/MinimalLayout';
import PageHead from '~components/PageHead';
import PostContent from './PostContent';
import DisplayError from './DisplayError';

function PrivacyPolicyScreen({
  post,
  error,
  exception,
}: ScreenPropsType<IProps>) {
  const info = useMemo(() => {
    if (!post) {
      return null;
    }

    const person = post.person.name || post.person.username;
    const community = post.community.name || post.community.shortname;
    const avatarUrl = sdk.storage.buildPath(post.person.avatarUrl);
    const message = utils.common
      .parseInfo(
        utils.time.updateUtcToCurrentTime(post.content || ''),
        (_id: string, name: string) => name,
        (_id: string, name: string) => name
      )
      .replace(/\n/g, ' ');

    return {
      title: `${person} to ${community}`,
      avatar: avatarUrl,
      description: message,
    };
  }, [post]);

  return (
    <MinimalLayout>
      {post && info ? (
        <>
          <PageHead
            title={info.title}
            description={info.description}
            image={info.avatar}
          />
          <PostContent post={post} />
        </>
      ) : (
        <>
          <PageHead title="Error" />
          <DisplayError error={error!} exception={exception} />
        </>
      )}
    </MinimalLayout>
  );
}

export default PrivacyPolicyScreen;

export async function getServerSideProps(context: GetServerSidePropsContext) {
  const postId = context.params?.id as unknown as types.ResourceIdentifier;
  if (!postId || !/^\d+$/.test(postId.toString())) {
    return { props: { error: 'InvalidPostIdentifier' } };
  }

  try {
    const url = `${process.env.NEXT_PUBLIC_API_URL!}/posts/${postId}`;
    token.initiate(process.env.NEXT_PUBLIC_API_TOKEN!, Date.now());

    const response = await fetch(url, {
      headers: { 'x-auth-token': token.generate() },
      redirect: 'follow',
    });

    const result = await response.json();
    const post = result.post;

    return { props: { post: post } };
  } catch (e) {
    console.error(e);
    return { notFound: true };
  }
}
