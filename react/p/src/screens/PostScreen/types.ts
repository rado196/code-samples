import { models } from '@443-how/models';

export interface IProps {
  post?: models.IPostModel;
  error?: string;
  exception?: Error;
}
