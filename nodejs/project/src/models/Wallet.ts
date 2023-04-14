import { PaymentMethodEnum } from '@foreach-am/evan-base-constants';
import { ModelInterface, BaseModel, orm } from '@foreach-am/evan-base-server';
import { schemaName } from '../database/config';
import PaymentMethod from './PaymentMethod';

export interface WalletBaseInterface {
  userId: number;
  isBusiness: boolean;
  balance: number;
}

export interface WalletInterface extends ModelInterface, WalletBaseInterface {}

@orm.table({
  tableName: 'wallets',
  schema: schemaName,
})
class Wallet extends BaseModel<WalletInterface> {
  @orm.column.intBigUnsigned()
  userId!: number;

  @orm.column.boolean()
  isBusiness!: number;

  @orm.column.double()
  balance!: number;

  static async afterCreate(instance: Wallet) {
    if (instance.getDataValue('isBusiness')) {
      return;
    }

    await PaymentMethod.create({
      userId: instance.getDataValue('userId'),
      isActive: true,
      paymentMethod: PaymentMethodEnum.EvanWallet,
    });
  }
}

export default Wallet;
