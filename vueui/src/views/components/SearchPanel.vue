<template>
  <el-card class="search-panel" v-loading="!this.$store.state.app.runtime.inited">
    <h5>{{$t(`super_report_title.${reportType}`)}}</h5>
    <SearchPanelSummary2Report v-if="reportType=='summary2'" :report-type="reportType"/>
    <SearchPanelPlayerReport v-if="reportType=='player'" :report-type="reportType"/>
    <SearchPanelPaymentReport v-if="reportType=='payment'" :report-type="reportType"/>
    <SearchPanelGameReport v-if="reportType=='game'" :report-type="reportType"/>
    <SearchPanelPromotionReport v-if="reportType=='promotion'" :report-type="reportType"/>
    <SearchPanelCashbackReport v-if="reportType=='cashback'" :report-type="reportType"/>
  </el-card>
</template>

<script>
import SearchPanelSummary2Report from '@/views/components/SearchPanelSummary2Report.vue';
import SearchPanelPlayerReport from '@/views/components/SearchPanelPlayerReport.vue';
import SearchPanelPaymentReport from '@/views/components/SearchPanelPaymentReport.vue';
import SearchPanelPromotionReport from '@/views/components/SearchPanelPromotionReport.vue';
import SearchPanelGameReport from '@/views/components/SearchPanelGameReport.vue';
import SearchPanelCashbackReport from '@/views/components/SearchPanelCashbackReport.vue';

export default {
  name: 'SearchPanel',
  props: {
    reportType: String,
  },
  components: {
    SearchPanelSummary2Report,
    SearchPanelPlayerReport,
    SearchPanelPaymentReport,
    SearchPanelPromotionReport,
    SearchPanelGameReport,
    SearchPanelCashbackReport,
  },
  methods: {
    handleSearch() {
      this.$log('searchDate', this.searchDate);

      // const queryReportType = this.$store.getters.constants.queryReportType
      //   .onePage;
      // this.$log('queryReportType', queryReportType);
      const dateFrom = this.$formatDate(this.searchDate[0]);
      const dateTo = this.$formatDate(this.searchDate[1]);
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
      pickerOptionsForDate: {
        shortcuts: [
          {
            text: this.$t('datetime.thisWeek'),
            onClick: picker => {
              const start = this.$moment()
                .startOf('isoWeek')
                .format('YYYY-MM-DD');
              const end = this.$moment().format('YYYY-MM-DD');
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
                .format('YYYY-MM-DD');
              const end = this.$moment()
                .subtract(1, 'week')
                .add(1, 'day')
                .endOf('isoWeek')
                .format('YYYY-MM-DD');
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
                .format('YYYY-MM-DD');
              const end = this.$moment()
                .subtract(1, 'month')
                .add(1, 'day')
                .endOf('month')
                .format('YYYY-MM-DD');
              picker.$emit('pick', [start, end,]);
            },
          },
          {
            text: this.$t('datetime.thisMonth'),
            onClick: picker => {
              //first to now
              const start = this.$moment()
                .startOf('month')
                .format('YYYY-MM-DD');
              const end = this.$moment().format('YYYY-MM-DD');
              picker.$emit('pick', [start, end,]);
            },
          },
        ],
      },
    };
  },
};
</script>