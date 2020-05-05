require('should');

const moment = require('moment');

const firstDayOfWeek = moment('2018-11-25')
  .subtract(1, 'week')
  .add(1, 'day')
  .startOf('isoWeek')
  .format('YYYY-MM-DD');

const lastDayOfWeek = moment('2018-11-25')
  .subtract(1, 'week')
  .add(1, 'day')
  .endOf('isoWeek')
  .format('YYYY-MM-DD');

describe('Process date time by moment', function() {
  it('First day of week is 2018-11-19', function() {
    firstDayOfWeek.should.equal('2018-11-19');
  });
  it('Last day of week is 2018-11-25', function() {
    lastDayOfWeek.should.equal('2018-11-25');
  });
});
