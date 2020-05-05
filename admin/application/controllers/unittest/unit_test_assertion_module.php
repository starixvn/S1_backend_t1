<?php

trait unit_test_assertion_module{

    public function assertEquals($expected, $actual, $message=null){
        if($this->print_debug_on_assert_function){
            $this->utils->debug_log('assert', $expected, $actual);
        }
        if($expected===$actual){
            $this->assertion_summary.='.';
            //right
            return true;
        }else{
            $this->assertion_summary.='F';
            if(empty($message)){
                $this->utils->error_log('something wrong, expect', $expected, 'actual', $actual);
            }else{
                $this->utils->error_log($message, 'expect', $expected, 'actual', $actual);
            }
            $this->utils->printToConsole('Assertion failed, stop all', true);
            $this->assertExitAll();
        }
    }

    public function assertTrue($actual, $message=null){
        return $this->assertEquals(true, $actual, $message);
    }

    public function assertFalse($actual, $message=null){
        return $this->assertEquals(false, $actual, $message);
    }

    public function assertNull($actual, $message=null){
        return $this->assertEquals(null, $actual, $message);
    }

    public function assertNotNull($actual, $message=null){
        return $this->assertTrue(null!==$actual, $message);
    }

    public function assertEmpty($actual, $message=null){
        if($this->print_debug_on_assert_function){
            $this->utils->debug_log('assert empty', $actual);
        }
        if(empty($actual)){
            $this->assertion_summary.='.';
            //right
            return true;
        }else{
            $this->assertion_summary.='F';
            if(empty($message)){
                $this->utils->error_log('something wrong, expect empty', 'actual', $actual);
            }else{
                $this->utils->error_log($message, 'expect empty', 'actual', $actual);
            }
            $this->utils->printToConsole('Assertion failed, stop all', true);
            $this->assertExitAll();
        }
    }

    public function assertNotEmpty($actual, $message=null){
        if($this->print_debug_on_assert_function){
            $this->utils->debug_log('assert not empty', $actual);
        }
        if(!empty($actual)){
            $this->assertion_summary.='.';
            //right
            return true;
        }else{
            $this->assertion_summary.='F';
            if(empty($message)){
                $this->utils->error_log('something wrong, expect not empty', 'actual', $actual);
            }else{
                $this->utils->error_log($message, 'expect not empty', 'actual', $actual);
            }
            $this->utils->printToConsole('Assertion failed, stop all', true);
            $this->assertExitAll();
        }
    }

    public function assertExitAll($errorCode=1){
        $this->printAssertionSummary();
        exit($errorCode);
    }

    public function printAssertionSummary(){
        $this->utils->printToConsole('Assertion Summary: '.$this->assertion_summary);
        // $this->utils->info_log($this->assertion_summary);
    }

}
