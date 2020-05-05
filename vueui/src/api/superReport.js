import request from '@/utils/request';

export function querySuperReport(reportType, conditions, queryReportType) {
  return request({
    url: `/super_report_api/${reportType}/${queryReportType}`,
    method: 'post',
    data: {
      'conditions': conditions,
    },
  });
}