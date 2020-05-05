<template>
  <el-menu :default-active="activeIndex" mode="horizontal" @select="handleSelect">
    <el-menu-item index="1">处理中心</el-menu-item>
    <el-menu-item index="3">消息中心</el-menu-item>
    <el-menu-item index="4">订单管理</el-menu-item>
  </el-menu>
</template>

<script>
export default {
  name: 'TopNavBar',
  props: {},
  computed: {
    activeIndex() {
      return this.$store.getters.runtime.activeNavMenuIndex;
    },
  },
  methods: {
    handleSelect(index) {
      //go to router
      // this.$log('reset current report', index);
      // Vue.console.log(Vue);
      //try clear current report
      this.$store.dispatch('resetCurrentReport');
      this.$router.push(index);
    },
  },
};
</script>
