<?php

trait unit_test_base_model_module{

    public function unit_test_base_model_reachedIpLimitHourlyBy(){
        $this->utils->printToConsole('start '.__METHOD__);
        $ip='192.168.1.1';
        $type='register';
        $this->load->model(['player_model']);

        $this->assertTrue($this->player_model->clearIpLimitBy($ip));

        //first time
        $reached=$this->player_model->reachedIpLimitHourlyBy($ip, $type, $err);
        $this->assertNotNull($reached);
        $this->assertFalse($reached);

        for ($i=0; $i < 9; $i++) {
            $reached=$this->player_model->reachedIpLimitHourlyBy($ip, $type, $err);
            $this->assertNotNull($reached);
            $this->assertFalse($reached);
        }

        $reached=$this->player_model->reachedIpLimitHourlyBy($ip, $type, $err);
        $this->assertNotNull($reached);
        $this->assertTrue($reached);

        $ipLimit=$this->player_model->readIpLimitBy($ip);
        $this->utils->debug_log('read ip limit', $ipLimit);

        $hourKey=date('YmdH');
        $this->assertNotEmpty($ipLimit);
        $this->assertEquals(11, $ipLimit['register'][$hourKey]);
        $this->printAssertionSummary();

    }

    public function unit_test_base_model_queryExplainRows(){
        $this->utils->printToConsole('start '.__METHOD__);

        $this->load->model(['player_model']);
        $sql='select * from game_logs where flag=1';
        $params=[];
        $cnt=$this->player_model->queryExplainRows($sql, $params, 'game_logs');
        $this->utils->debug_log('explain rows', $cnt);
        $this->assertTrue($cnt>0);

        $this->printAssertionSummary();
    }

    public function unit_test_base_model_queryPlanSelectorByExplain(){
        $this->utils->printToConsole('start '.__METHOD__);

        $config=$this->unit_test_config['unit_test_base_model_queryPlanSelectorByExplain'];

        $this->load->model(['player_model']);
        // $sql='select * from game_logs where flag=1';
        $params=[$config['from'], $config['to']];
        $sqlPlans=[
            [
            'sql'=>'select * from game_logs use index(idx_updated_at) where end_at>? and end_at<?',
            'params'=>$params,
            'mainTable'=>'game_logs',
            ],
            [
            'sql'=>'select * from game_logs use index(idx_end_at) where end_at>? and end_at<?',
            'params'=>$params,
            'mainTable'=>'game_logs',
            ],
        ];
        $result=$this->player_model->queryPlanSelectorByExplain($sqlPlans);
        $this->utils->debug_log('queryPlanSelectorByExplain result', $result);

        $this->assertNotNull($result);
        $this->assertEquals($sqlPlans[1]['sql'], $result['sql']);

        $this->printAssertionSummary();
    }


    public function unit_test_base_model_all(){
        $this->unit_test_base_model_reachedIpLimitHourlyBy();
        $this->unit_test_base_model_queryExplainRows();
    }

}
