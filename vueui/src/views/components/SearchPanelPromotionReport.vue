<template>
  <div>
    <el-row :gutter="10">
      <el-col :span="18">
        <el-date-picker
          v-model="searchDate"
          type="datetimerange"
          range-separator="-"
          :picker-options="pickerOptionsForDateTime"
          first-day-of-week="1"
          align="right"
        ></el-date-picker>
      </el-col>
    </el-row>
    <el-row :gutter="10">
      <el-col :span="16">
        <el-button type="primary" @click="handleSearch">{{ $t('normal.search') }}</el-button>
      </el-col>
    </el-row>
  </div>
</template>

<script>
export default {
  name: 'SearchPanelPromotionReport',
  props: {
    reportType: String,
  },
  methods: {
    handleSearch() {
      this.$log('searchDate', this.searchDate);

      // const queryReportType = this.$store.getters.constants.queryReportType
      //   .onePage;
      // this.$log('queryReportType', queryReportType);
      const dateFrom = this.$formatDateTime(this.searchDate[0]);
      const dateTo = this.$formatDateTime(this.searchDate[1]);
      const searchBy = {
        dateFrom: dateFrom,
        dateTo: dateTo,
        monthOnly: false,
      };
      // this.$log('conditions', conditions);
      //set conditions
      // this.$store.commit('setConditions', { conditions: conditions, });
      this.$store.dispatch('remoteQuerySuperReport', {
        reportType: this.reportType,
        searchBy: searchBy,
        orderBy: null,
      });
    },
  },
  data() {
    return {
      searchDate: [new Date(), new Date(),],
      pickerOptionsForDateTime: {
        shortcuts: [
          {
            text: this.$t('datetime.thisWeek'),
            onClick: picker => {
              const start = this.$moment()
                .startOf('isoWeek')
                .format('YYYY-MM-DD HH:mm:ss');
              const end = this.$moment().format('YYYY-MM-DD HH:mm:ss');
              picker.$emit('pick', [start, end,]);
            },
          },
          {
            text: this.$t('datetime.lastWeek'),
            onClick: picker => {
              const start = this.$moment()
                .subtract(1, 'week')
                .add(1, 'day')
                .startOf('isoWeek')
                .format('YYYY-MM-DD HH:mm:ss');
              const end = this.$moment()
                .subtract(1, 'week')
                .add(1, 'day')
                .endOf('isoWeek')
                .format('YYYY-MM-DD HH:mm:ss');
              picker.$emit('pick', [start, end,]);
            },
          },
          {
            text: this.$t('datetime.lastMonth'),
            onClick: picker => {
              const start = this.$moment()
                .subtract(1, 'month')
                .add(1, 'day')
                .startOf('month')
                .format('YYYY-MM-DD HH:mm:ss');
              const end = this.$moment()
                .subtract(1, 'month')
                .add(1, 'day')
                .endOf('month')
                .format('YYYY-MM-DD HH:mm:ss');
              picker.$emit('pick', [start, end,]);
            },
          },
          {
            text: this.$t('datetime.thisMonth'),
            onClick: picker => {
              //first to now
              const start = this.$moment()
                .startOf('month')
                .format('YYYY-MM-DD HH:mm:ss');
              const end = this.$moment().format('YYYY-MM-DD HH:mm:ss');
              picker.$emit('pick', [start, end,]);
            },
          },
        ],
      },
    };
  },
};
</script>