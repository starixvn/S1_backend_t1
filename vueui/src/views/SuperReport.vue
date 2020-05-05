<template>
  <div v-loading="!this.$store.state.app.runtime.inited">
    <el-row>
      <el-col :span="24">
        <search-panel :report-type="reportType"></search-panel>
      </el-col>
    </el-row>
    <el-row>
      <el-col :span="24">
        <search-result :report-type="reportType"></search-result>
      </el-col>
    </el-row>
  </div>
</template>

<script>
import SearchPanel from '@/views/components/SearchPanel.vue';
import SearchResult from '@/views/components/SearchResult.vue';

export default {
  name: 'SuperReport',
  components: {
    SearchPanel,
    SearchResult,
  },
  props: {
    reportType: String,
  },
  methods: {},
  data() {
    return {};
  },
};
</script>

