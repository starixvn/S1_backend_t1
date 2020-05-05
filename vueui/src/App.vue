<template>
  <div id="app">
    <el-container id="main_container">
      <sidebar/>
      <el-main>
        <transition name="el-zoom-in-center">
          <router-view></router-view>
        </transition>
      </el-main>
    </el-container>
  </div>
</template>

<script>
import Sidebar from '@/views/components/Sidebar.vue';
export default {
  name: 'app',
  components: {
    Sidebar,
  },
  data() {
    return {
      editableTabsValue2: '2',
      editableTabs2: [
        {
          title: 'Tab 1',
          name: '1',
          content: 'Tab 1 content',
        },
        {
          title: 'Tab 2',
          name: '2',
          content: 'Tab 2 content',
        },
      ],
      tabIndex: 2,
    };
  },
  methods: {
    addTab() {
      const newTabName = `${++this.tabIndex}`;
      this.editableTabs2.push({
        title: 'New Tab',
        name: newTabName,
        content: 'New Tab content',
      });
      this.editableTabsValue2 = newTabName;
    },
    removeTab(targetName) {
      const tabs = this.editableTabs2;
      let activeName = this.editableTabsValue2;
      if (activeName === targetName) {
        tabs.forEach((tab, index) => {
          if (tab.name === targetName) {
            let nextTab = tabs[index + 1] || tabs[index - 1];
            if (nextTab) {
              activeName = nextTab.name;
            }
          }
        });
      }

      this.editableTabsValue2 = activeName;
      this.editableTabs2 = tabs.filter(tab => tab.name !== targetName);
    },
  },
};
</script>

<style>
</style>
