export interface IContentItemList {
  section: string;
  content: Array<string>;
}

export interface IContentBlock {
  heading?: string;
  content: Array<string | Array<string | IContentItemList>>;
}

export interface IContent {
  title: string;
  lastUpdatedAt: string;
  blocks: Array<IContentBlock>;
}
