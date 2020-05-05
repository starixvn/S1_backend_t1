import request from '@/utils/request';

export function initApi() {
  return request({
    url: '/init_api',
    method: 'post',
  });
}