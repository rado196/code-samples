import React from 'react';
import { IProps } from './types';
import MasterLayout from '~layouts/MasterLayout';
import PageHead from '~components/PageHead';
import SecurityContent from '~components/SecurityContent';
import { contentPrivacyPolicy } from '~constants/security-content';

function PrivacyPolicyScreen(props: ScreenPropsType<IProps>) {
  return (
    <MasterLayout>
      <PageHead title="Privacy Policy" />
      <SecurityContent content={contentPrivacyPolicy} />
    </MasterLayout>
  );
}

export default PrivacyPolicyScreen;
