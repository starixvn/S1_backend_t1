<template>
  <el-card class="search-result-panel">
    <el-pagination
      :page-sizes="pageSizeList"
      :current-page.sync="currentPage"
      :page-size.sync="pageSize"
      :total="totalCount"
      v-if="countReady"
      layout="total, sizes, prev, pager, next, jumper"
    ></el-pagination>
    <el-table
      :data="currentRows"
      :sort-orders="['ascending', 'descending']"
      :default-sort="defaultSort"
      :summary-method="summaryReport"
      :show-summary="totalReady"
      v-loading="loading"
      @sort-change="handleSortChange"
      header-cell-class-name="adjust_table_header"
      cell-class-name="adjust_table_cell"
      border
      stripe
      style="width: 100%"
    >
      <el-table-column
        v-for="h in reportHeader"
        :key="h.key"
        :prop="h.key"
        :label="h.label"
        :fixed="h.fixed"
        :align="h.align"
        :width="`${h.width}px`"
        :min-width="`${h.minWidth}px`"
        header-align="center"
        show-overflow-tooltip
        sortable="custom"
      ></el-table-column>
    </el-table>
    <el-pagination
      :page-sizes="pageSizeList"
      :current-page.sync="currentPage"
      :page-size.sync="pageSize"
      :total="totalCount"
      v-if="countReady"
      layout="total, sizes, prev, pager, next, jumper"
    ></el-pagination>
  </el-card>
</template>
<script>
import { mapState } from 'vuex';

export default {
  name: 'SuperResult',
  props: {
    reportType: String,
  },
  computed: {
    loading() {
      return !this.$store.state.superReport.currentReport.onePageReady;
    },
    defaultSort: {
      get() {
        return { prop: 'summary_date', order: 'descending', };
      },
    },
    pageSize: {
      get() {
        return this.$store.state.superReport.currentReport.limitBy.sizePerPage;
      },
      set(val) {
        // this.$log('updatePageSize', val);
        // this.$store.commit('updateSizePerPage', { sizePerPage: val, });
        this.$store.dispatch('updateSizePerPage', {
          sizePerPage: val,
        });
      },
    },
    currentPage: {
      get() {
        return this.$store.state.superReport.currentReport.limitBy.currentPage;
      },
      set(val) {
        // this.$log('updateCurrentPage', val);
        // this.$store.commit('updateCurrentPage', { currentPage: val, });
        this.$store.dispatch('updateCurrentPage', {
          currentPage: val,
        });
      },
    },
    ...mapState({
      pageSizeList: state => state.superReport.currentReport.pageSizeList,
      totalCount: state => state.superReport.currentReport.count,
      currentRows: state => state.superReport.currentReport.rows,
      reportHeader: state => state.superReport.currentReport.header,
      countReady: state => state.superReport.currentReport.countReady,
      totalReady: state => state.superReport.currentReport.totalReady,
    }),
  },
  methods: {
    handleSortChange({ prop, order, }) {
      this.$log('handleSortChange', prop, order);
      this.$store.dispatch('updateOrderBy', {
        orderAlias: prop,
        direction:
          order === 'ascending'
            ? this.$store.getters.constants.orderAsc
            : this.$store.getters.constants.orderDesc,
      });
    },
    summaryReport(param) {
      const { columns, } = param;
      const totalRow = this.$store.state.superReport.currentReport.total;
      // this.$log('totalRow', totalRow);
      const sums = [];
      columns.forEach((column, index) => {
        if (index === 0) {
          sums[index] = this.$t('normal.total');
          return;
        }
        if (totalRow !== null) {
          sums[index] = totalRow[column.property];
        }

        // const values = data.map(item => Number(item[column.property]));
        // const precisions = [];
        // let notNumber = true;
        // values.forEach(value => {
        //   if (!isNaN(value)) {
        //     notNumber = false;
        //     let decimal = `${value}`.split('.')[1];
        //     precisions.push(decimal ? decimal.length : 0);
        //   }
        // });
        // const precision = Math.max.apply(null, precisions);
        // if (!notNumber) {
        //   sums[index] = values.reduce((prev, curr) => {
        //     const value = Number(curr);
        //     if (!isNaN(value)) {
        //       return parseFloat((prev + curr).toFixed(Math.min(precision, 20)));
        //     } else {
        //       return prev;
        //     }
        //   }, 0);
        // } else {
        //   sums[index] = '';
        // }
      });
      // this.$log('sums', sums);
      return sums;
    },
  },
  data: function() {
    return {};
  },
};
</script>
