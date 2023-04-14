/* eslint-disable @typescript-eslint/no-unused-vars */
/* eslint-disable no-unused-vars */
/* eslint-disable unicorn/numeric-separators-style */

import './bootstrap';

import {
  AppEnv,
  Nullable,
  helpers as libraryHelpers,
} from '@foreach-am/evan-base-library';
import { HttpApplication } from '@foreach-am/evan-base-server';
import dbConfigs from './database/config';

const app = HttpApplication.make()
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
  .setApiPrefix('/api/payments')
  .setNodePort(7502)
  .start(
    (
      error: Nullable<Error>,
      nodeEnv: Nullable<AppEnv>,
      nodePort: Nullable<number>
    ) => {
      if (error !== null) {
        libraryHelpers.log.error('Failed to start http server.');
        return libraryHelpers.log.error(error);
      }

      libraryHelpers.log.success(
        `App listening on port ${nodePort} with ${nodeEnv} environment!`
      );

      // if (nodeEnv !== 'production') {
      app.printRouteList();
      // }
    }
  );
