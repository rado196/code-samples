import { ModelInterface, BaseModel, orm } from '@foreach-am/evan-base-server';
import { schemaName } from '../database/config';

export interface ChargingTransactionBaseInterface {
  userId: number;
  transactionId: number;
  stationId: number;
  amount: number;
}

export interface ChargingTransactionInterface
  extends ModelInterface,
    ChargingTransactionBaseInterface {}

@orm.table({
  tableName: 'charging_transactions',
  schema: schemaName,
})
class ChargingTransaction extends BaseModel<ChargingTransactionInterface> {
  @orm.column.intBigUnsigned()
  userId!: number;
  @orm.column.intBigUnsigned()
  transactionId!: number;

  @orm.column.intBigUnsigned()
  stationId!: number;

  @orm.column.double()
  amount: number;
}

export default ChargingTransaction;
