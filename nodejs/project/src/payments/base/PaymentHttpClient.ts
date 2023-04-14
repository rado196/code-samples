import {
  AnyType,
  Primitive,
  services as libraryServices,
} from '@foreach-am/evan-base-library';

class PaymentHttpClient {
  constructor(private readonly apiHost: string) {
    if (this.apiHost.endsWith('/')) {
      this.apiHost = this.apiHost.slice(0, this.apiHost.length - 1);
    }
  }

  private buildUrl(endpoint: string) {
    if (!endpoint.startsWith('/')) {
      endpoint = `/${endpoint}`;
    }

    return `${this.apiHost}${endpoint}`;
  }

  // eslint-disable-next-line @typescript-eslint/ban-types
  private async send(caller: Function, endpoint: string, args: AnyType) {
    return await caller(this.buildUrl(endpoint), args).request();
  }

  async get(
    endpoint: string,
    queryParams: Record<string, Primitive> = {},
    headers: Record<string, Primitive> = {}
  ) {
    return this.send(libraryServices.NetworkService.apiGet, endpoint, {
      headers: headers,
      queryParams: queryParams,
    });
  }

  async post(
    endpoint: string,
    body: AnyType = {},
    queryParams: Record<string, Primitive> = {},
    headers: Record<string, Primitive> = {}
  ) {
    return this.send(libraryServices.NetworkService.apiPost, endpoint, {
      headers: headers,
      queryParams: queryParams,
      body: body,
    });
  }

  async put(
    endpoint: string,
    body: AnyType = {},
    queryParams: Record<string, Primitive> = {},
    headers: Record<string, Primitive> = {}
  ) {
    return this.send(libraryServices.NetworkService.apiPut, endpoint, {
      headers: headers,
      queryParams: queryParams,
      body: body,
    });
  }

  async delete(
    endpoint: string,
    body: AnyType = {},
    queryParams: Record<string, Primitive> = {},
    headers: Record<string, Primitive> = {}
  ) {
    return this.send(libraryServices.NetworkService.apiDelete, endpoint, {
      headers: headers,
      queryParams: queryParams,
      body: body,
    });
  }
}

export default PaymentHttpClient;
