import { AnyType, Nullable } from '@foreach-am/evan-base-library';
import { ModelInterface, BaseModel, orm } from '@foreach-am/evan-base-server';
import { schemaName } from '../database/config';

export enum HttpMethodEnum {
  GET = 'GET',
  HEAD = 'HEAD',
  OPTIONS = 'OPTIONS',
  POST = 'POST',
  PUT = 'PUT',
  PATCH = 'PATCH',
  DELETE = 'DELETE',
}

export interface PaymentHttpLogBaseInterface {
  userId?: Nullable<number>;
  url: string;
  httpMethod: HttpMethodEnum;
  requestBody: AnyType;
  requestHeaders: AnyType;
  responseBody?: Nullable<AnyType>;
  responseHeaders?: Nullable<AnyType>;
  statusCode?: Nullable<number>;
}

export interface PaymentHttpLogInterface
  extends ModelInterface,
    PaymentHttpLogBaseInterface {}

@orm.table({
  tableName: 'payment_http_logs',
  schema: schemaName,
})
class PaymentHttpLog extends BaseModel<PaymentHttpLogInterface> {
  @orm.column.intBigUnsigned({ allowNull: true })
  userId: number;

  @orm.column.string()
  url: string;

  @orm.column.enumColumn({
    values: [
      HttpMethodEnum.GET,
      HttpMethodEnum.HEAD,
      HttpMethodEnum.OPTIONS,
      HttpMethodEnum.POST,
      HttpMethodEnum.PUT,
      HttpMethodEnum.PATCH,
      HttpMethodEnum.DELETE,
    ],
  })
  httpMethod: HttpMethodEnum;

  @orm.column.json()
  requestBody: AnyType;

  @orm.column.json()
  requestHeaders: AnyType;

  @orm.column.json({ allowNull: true })
  responseBody: AnyType;

  @orm.column.json({ allowNull: true })
  responseHeaders: AnyType;

  @orm.column.intSmallUnsigned({ allowNull: true })
  statusCode?: number;
}

export default PaymentHttpLog;
