import React from 'react';
import { IProps } from './types';
import MasterLayout from '~layouts/MasterLayout';
import SecurityContent from '~components/SecurityContent';
import PageHead from '~components/PageHead';
import { contentTermsAndConditions } from '~constants/security-content';

function TermsAndConditionsScreen(props: PropsType<IProps>) {
  return (
    <MasterLayout>
      <PageHead title="Terms of Condition" />

      <SecurityContent content={contentTermsAndConditions} />
    </MasterLayout>
  );
}

export default TermsAndConditionsScreen;
