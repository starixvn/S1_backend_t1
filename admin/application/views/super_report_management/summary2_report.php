<style type="text/css">
	#wrapper.active{
		padding-left: 0px;
	}
	#page-content-wrapper{
		/* width: 100%; */
		/* padding-top: 2px; */
	}
</style>
<?php if ($this->utils->getConfig('debug_version_info')) { ?>
<script src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script>
<?php }else{ ?>
<script src="https://cdn.jsdelivr.net/npm/vue"></script>
<?php }?>

<!-- 引入样式 -->
<link rel="stylesheet" href="https://unpkg.com/element-ui/lib/theme-chalk/index.css">

  <div id="app">
  	<el-container>
  		<el-aside width="200px">
		    <el-menu>
		      <el-menu-item index="1">
		        <template slot="title"><i class="el-icon-message"></i>导航一</template>
		      </el-menu-item>
		      <el-menu-item index="2">
		        <template slot="title"><i class="el-icon-menu"></i>导航二</template>
		      </el-menu-item>
		      <el-menu-item index="3">
		        <template slot="title"><i class="el-icon-setting"></i>导航三</template>
		      </el-menu-item>
		    </el-menu>
  		</el-aside>
		<el-main>

     <el-button @click="visible = true">Button</el-button>
    <el-dialog :visible.sync="visible" title="Hello world">
      <p>Try Element</p>
    </el-dialog>

    <el-table :data="tableData" border style="width: 100%">
      <el-table-column prop="date" label="日期" width="180">
      </el-table-column>
      <el-table-column prop="name" label="姓名" width="180">
      </el-table-column>
      <el-table-column prop="address" label="地址">
      </el-table-column>
    </el-table>

    	</el-main>
	</el-container>
  </div>

<!-- 引入组件库 -->
<script src="https://unpkg.com/element-ui/lib/index.js"></script>

<script>


new Vue({
  el: '#app',
  data: function() {
    return { visible: false,
    	tableData: [{
            date: '2016-05-02',
            name: '王小虎',
            address: '上海市普陀区金沙江路 1518 弄'
          }, {
            date: '2016-05-04',
            name: '王小虎',
            address: '上海市普陀区金沙江路 1517 弄'
          }, {
            date: '2016-05-01',
            name: '王小虎',
            address: '上海市普陀区金沙江路 1519 弄'
          }, {
            date: '2016-05-03',
            name: '王小虎',
            address: '上海市普陀区金沙江路 1516 弄'
        }]
    }
  }
})

</script>

