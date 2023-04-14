/* eslint-disable @typescript-eslint/no-unused-vars */

import './bootstrap';

import {
  AppEnv,
  Nullable,
  helpers as libraryHelpers,
} from '@foreach-am/evan-base-library';
import { JobApplication } from '@foreach-am/evan-base-server';
import dbConfigs from './database/config';

const app = JobApplication.make()
  .setRootPath(__dirname)
  .setViewEngine('ejs')
  .setDatabaseConfig(dbConfigs, (error: Nullable<Error>) => {
    if (error !== null) {
      libraryHelpers.log.error('Failed to connect to database.');
      return libraryHelpers.log.error(error);
    }

    libraryHelpers.log.success(
      'Successfully connected to PostgreSQL database!'
    );
  })
  .setStaticPath('public')
  .start((error: Nullable<Error>, nodeEnv: Nullable<AppEnv>) => {
    if (error !== null) {
      libraryHelpers.log.error('Failed to start jobs server.');
      return libraryHelpers.log.error(error);
    }

    libraryHelpers.log.success(
      `Jobs server running with ${nodeEnv} environment!`
    );

    if (nodeEnv !== 'production') {
      app.printCommandsList();
    }
  });
