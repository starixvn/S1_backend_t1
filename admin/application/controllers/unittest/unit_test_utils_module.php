<?php

trait unit_test_utils_module{

    public function unit_test_utils_isOptionsRequest(){
        $this->utils->printToConsole('start '.__METHOD__);
        $this->assertFalse($this->utils->isOptionsRequest());
        $this->printAssertionSummary();
    }

    public function unit_test_utils_all(){
        $this->unit_test_utils_isOptionsRequest();
        $this->unit_test_utils_stripHtmlTagsOfArray();
        $this->unit_test_utils_isResourceInsideLock();
    }

    public function unit_test_utils_stripHtmlTagsOfArray(){
        $expected_str  = 'Test Html Format JavaJava';

        /* empty data or null */
        $empty_result = $this->utils->stripHtmlTagsOfArray(null);
        $this->assertEquals(null, $empty_result);

        /* string */
        $str = '<H1>Test Html Format <a href="http://admin.onestop.t1t.in/">Java</a><a>Java</a>';
        $str_result = $this->utils->stripHtmlTagsOfArray($str);
        $this->assertEquals($expected_str, $str_result);

        /* array */
        $array = array(
            "str_with_html_tags" => '<H1>Test Html Format <a href="http://admin.onestop.t1t.in/">Java</a><a>Java</a>',
            "normal_str" => 'test'
        );
        $array_result = $this->utils->stripHtmlTagsOfArray($array);
        $this->assertEquals($expected_str, $array_result['str_with_html_tags']);


        /*
        multi-dimensional array
        */
        $multi_array = array(
            "multi_array"=> array(
                "str_with_html_tags" => '<H1>Test Html Format <a href="http://admin.onestop.t1t.in/">Java</a><a>Java</a>',
                "normal_str" => 'test'
            ),
        );
        $multi_array_result = $this->utils->stripHtmlTagsOfArray($multi_array);
        $this->assertEquals($expected_str, $multi_array_result['multi_array']['str_with_html_tags']);

        $this->printAssertionSummary();
    }

    public function unit_test_utils_isResourceInsideLock(){
        $this->utils->printToConsole('start '.__METHOD__);
        //sample
        $playerId=1;
        $this->assertFalse($this->utils->isResourceInsideLock($playerId, Utils::LOCK_ACTION_BALANCE), 'no lock');
        $success=$this->lockAndTransForPlayerBalance($playerId, function() use($playerId){
            $success=$this->utils->isResourceInsideLock($playerId, Utils::LOCK_ACTION_BALANCE);
            $this->utils->debug_log('isResourceInsideLock', $success, $playerId);
            $this->assertTrue($success, 'lock success');
            return $success;
        });
        $this->assertTrue($success, 'lockAndTransForPlayerBalance success');

        $this->printAssertionSummary();
    }

}
