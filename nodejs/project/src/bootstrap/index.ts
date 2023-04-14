import { loadDotEnvConfigs } from '@foreach-am/evan-base-server';
import path from 'path';

loadDotEnvConfigs(path.join(__dirname, '..'), [
  '.env',
  '.env.global',
  '.env.local',
]);
