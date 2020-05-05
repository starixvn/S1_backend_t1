<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

require_once dirname(__FILE__) . '/BaseController.php';

/**
 * Class Country_management
 */
class Backoffice_api extends BaseController {

    function __construct() {
        parent::__construct();
        $this->load->helper('url');
        $this->load->library(array('permissions', 'authentication', 'input', 'session'));

        // $this->permissions->checkSettings();
        $this->permissions->setPermissions();
        if (!$this->authentication->isLoggedIn()) {
            //try login with token
            $token=$this->input->get_post('token');
            if(!empty($token)){
                $message=null;
                $success=$this->authentication->login_from_token($token, $message);
                if(!$success){
                    $this->utils->error_log('login from token is failed', $token, $message);
                    $this->returnNoPermissionAndExit();
                }else{
                    $this->session->reinit();
                }
            }
        }
        if (!$this->authentication->isLoggedIn()) {
            $this->returnNoPermissionAndExit();
        }

        $this->saveAction('Backoffice_api', uri_string(), "username: " . $this->authentication->getUsername());

    }

    private function returnNoPermissionAndExit(){
        $result['errorCode']=self::ERROR_NO_PERMISSION;
        $result['errorMessage']=lang('Sorry, no permission');
        $this->returnJsonResult($result);
        exit(1);
    }

    //=======================================================================
    const ERROR_NO_PERMISSION=1;
    const ERROR_WRONG_REPORT_TYPE=2;

    // const PAGE_SIZE_LIST=[2, 25, 50, 100, 500];

    private function getConstantsForAPI(){

        $this->load->model(['multiple_db_model']);

        return [
            'errorCodes'=>[
                self::ERROR_NO_PERMISSION=>lang('Sorry, no permission'),
                self::ERROR_WRONG_REPORT_TYPE=>lang('Wrong report type'),
            ],
            'reportTypes'=>[
                Multiple_db_model::SUPER_REPORT_TYPE_SUMMARY2=>lang('Summary Report'),
                Multiple_db_model::SUPER_REPORT_TYPE_PLAYER=>lang('Player Report'),
            ],
            'queryReportType'=>[
                'onePage'=>Multiple_db_model::QUERY_REPORT_TYPE_ONE_PAGE,
                'count'=>Multiple_db_model::QUERY_REPORT_TYPE_COUNT,
                'total'=>Multiple_db_model::QUERY_REPORT_TYPE_TOTAL,
                'summary'=>Multiple_db_model::QUERY_REPORT_TYPE_SUMMARY,
                'export'=>Multiple_db_model::QUERY_REPORT_TYPE_EXPORT,
            ],
            'orderDesc'=>'desc',
            'orderAsc'=>'asc',
        ];
    }
    private function getRuntimeForAPI(){
        $this->load->model(['common_token']);
        $this->load->library(['language_function']);
        $sessionLang=$this->language_function->getCurrentLanguage();
        $lang=Language_function::ISO2_LANG[$sessionLang];
        $userId=$this->authentication->getUserId();
        $token=$this->common_token->getAdminUserToken($userId);

        return [
            'lang'=>$lang,
            'loggedUsername'=>$this->authentication->getUsername(),
            'showSubTotal'=>true,
            'sidebar'=>[
                'opened'=>true,
            ],
            'activeNavMenuIndex'=> '1',
            'debugReportMode'=>$this->utils->getConfig('debug_report_mode'),
            'token'=>$token,
            'inited'=>true,
        ];
    }

    /**
     *
     * @return json init runtime variables, constants
     */
    public function init_api(){
        $result=['success'=>false];

        if (!$this->permissions->checkPermissions('super_report')) {
            $result['errorCode']=self::ERROR_NO_PERMISSION;
            $result['errorMessage']=lang('Sorry, no permission');
            return $this->returnJsonResult($result);
        }

        // $json=$this->input->readJsonOnce();
        // $this->utils->debug_log('read json', $json);

        $result['success']=true;
        $result['result']=['constants'=>$this->getConstantsForAPI(),
            'runtime'=>$this->getRuntimeForAPI()];

        return $this->returnJsonResult($result);
    }

    /**
     *
     * @param  string $report_type
     * @param  string $queryReportType onePage/count/total/summary/export
     * @return json format:
     * {
     *     "success": true (boolean),
     *     "errorCode": null (int optional),
     *     "errorMessage": null (string optiona),
     *     "result": (any result, like report)
     * }
     *
     */
    public function super_report_api($report_type, $queryReportType){

        $result=['success'=>false];

        if (!$this->permissions->checkPermissions('super_report')) {
            $result['errorCode']=self::ERROR_NO_PERMISSION;
            $result['errorMessage']=lang('Sorry, no permission');
            return $this->returnJsonResult($result);
        }

        $request=$this->input->readJsonOnce();
        $conditions=$request['conditions'];
        // $queryReportType=$request['queryReportType'];

        $this->load->model(['multiple_db_model']);
        switch ($report_type) {
            case Multiple_db_model::SUPER_REPORT_TYPE_SUMMARY2:
                $result['result']=$this->multiple_db_model->querySummary2Report($conditions, $queryReportType);
                $result['success']=true;
                break;
            case Multiple_db_model::SUPER_REPORT_TYPE_PLAYER:
                $result['result']=$this->multiple_db_model->queryPlayerReport($conditions, $queryReportType);
                $result['success']=true;
                break;
            case Multiple_db_model::SUPER_REPORT_TYPE_GAME:
                $result['result']=$this->multiple_db_model->queryGameReport($conditions, $queryReportType);
                $result['success']=true;
                break;
            case Multiple_db_model::SUPER_REPORT_TYPE_PAYMENT:
                $result['result']=$this->multiple_db_model->queryPaymentReport($conditions, $queryReportType);
                $result['success']=true;
                break;
            case Multiple_db_model::SUPER_REPORT_TYPE_PORMOTION:
                $result['result']=$this->multiple_db_model->queryPromotionReport($conditions, $queryReportType);
                $result['success']=true;
                break;
            case Multiple_db_model::SUPER_REPORT_TYPE_CASHBACK:
                $result['result']=$this->multiple_db_model->queryCashbackReport($conditions, $queryReportType);
                $result['success']=true;
                break;
            default:
                $result['errorCode']=self::ERROR_WRONG_REPORT_TYPE;
                $result['errorMessage']=lang('Wrong report type');
                break;
        }

        return $this->returnJsonResult($result);

    }

}
