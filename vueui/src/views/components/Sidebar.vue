<template>
  <el-aside :width="sidebarWidth">
    <el-menu
      class="sidebar_menu"
      background-color="#222"
      text-color="#fff"
      @select="handleSelect"
      :default-active="$route.path"
      mode="vertical"
      :collapse="!$store.getters.runtime.sidebar.opened"
    >
      <el-menu-item index="/super_report/summary2">
        <v-icon name="chart-pie"/>
        <span slot="title">{{ $t('super_report_title.summary2') }}</span>
      </el-menu-item>
      <el-menu-item index="/super_report/player">
        <v-icon name="users"/>
        <span slot="title">{{ $t('super_report_title.player') }}</span>
      </el-menu-item>
      <el-menu-item index="/super_report/game">
        <v-icon name="dice"/>
        <span slot="title">{{ $t('super_report_title.game') }}</span>
      </el-menu-item>
      <el-menu-item index="/super_report/payment">
        <v-icon name="credit-card"/>
        <span slot="title">{{ $t('super_report_title.payment') }}</span>
      </el-menu-item>
      <el-menu-item index="/super_report/promotion">
        <v-icon name="bullhorn"/>
        <span slot="title">{{ $t('super_report_title.promotion') }}</span>
      </el-menu-item>
      <el-menu-item index="/super_report/cashback">
        <v-icon name="money-bill"/>
        <span slot="title">{{ $t('super_report_title.cashback') }}</span>
      </el-menu-item>
    </el-menu>
    <v-icon :name="sidebarSwitcherIcon" class="switch_sidebar" v-on:click.native="switchSideBar()"/>
    <!-- <i :class="sidebarClass" @click="switchSideBar()"></i> -->
  </el-aside>
</template>
<style>
</style>

<script>
export default {
  name: 'Sidebar',
  props: {},
  computed: {
    sidebarSwitcherIcon() {
      return !this.$store.getters.runtime.sidebar.opened
        ? 'chevron-circle-right'
        : 'chevron-circle-left';
    },
    sidebarClass() {
      return {
        'fa-chevron-circle-right switch_sidebar': this.$store.getters.runtime
          .sidebar.opened,
        'el-icon-d-arrow-right switch_sidebar': !this.$store.getters.runtime
          .sidebar.opened,
      };
    },
    sidebarWidth() {
      return this.$store.getters.runtime.sidebar.opened ? '200px' : '64px';
    },
  },
  methods: {
    switchSideBar() {
      this.$store.dispatch('ToggleSideBar');
      // this.$log(this.$route.name);
    },
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

