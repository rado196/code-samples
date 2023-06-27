/// <reference types="node" />

// eslint-disable-next-line @typescript-eslint/no-explicit-any
declare type AnyType = any;
declare type ArrayOrSame<T> = T | Array<T>;
declare type Maybe<T> = T | undefined | null;
declare type Nullable<T> = T | null;

declare type ReactExactChildComponentType =
  | string
  | JSX.Element
  | Array<JSX.Element>;
declare type ReactChildComponentType = Maybe<ReactExactChildComponentType>;

declare type PropsType<
  TProps extends object = {},
  TChild = ReactChildComponentType
> = TProps & {
  children?: TChild;
};

declare type ScreenPropsType<
  TProps extends object = {},
  TChild = ReactChildComponentType
> = PropsType<TProps, TChild>;

declare interface IAppPreviewCommunity {
  image: AnyType;
  name: string;
  membersCount: number;
}

declare interface IAppPreviewMessageItem {
  text: string;
  time: string;
}

declare interface IAppPreviewMessagePerson {
  avatar: AnyType;
  name: string;
}

declare interface IAppPreviewMessageGroup {
  person: IAppPreviewMessagePerson;
  messages: Array<IAppPreviewMessageItem>;
}

declare interface IAppPreview {
  community: IAppPreviewCommunity;
  messages: Array<IAppPreviewMessageGroup>;
}
