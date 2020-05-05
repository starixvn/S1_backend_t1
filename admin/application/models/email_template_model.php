<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Email_template_model extends CI_Model
{
    const PLAYER_PLATFORM_TYPE    = 1;
    const AFFILIATE_PLATFORM_TYPE = 2;
    const AGENCY_PLATFORM_TYPE    = 3;
    const KYC_PLATFORM_TYPE       = 4;

    const REGISTRATION_TEMPLATE        = 1;
    const FORGOT_PASSWORD_TEMPLATE     = 2;
    const EMAIL_VERIFICATION_TEMPLATE  = 3;
    const OTHERS_TEMPLATE              = 4;

    private $PLATFORM_TYPE = [
        self::PLAYER_PLATFORM_TYPE    => 'player',
        self::AFFILIATE_PLATFORM_TYPE => 'affiliate',
        self::AGENCY_PLATFORM_TYPE    => 'agency',
        self::KYC_PLATFORM_TYPE       => 'kyc'
    ];

    private $TEMPLATE_TYPE = [
        self::REGISTRATION_TEMPLATE       => 'Registration',
        self::FORGOT_PASSWORD_TEMPLATE    => 'Forgot Password',
        self::EMAIL_VERIFICATION_TEMPLATE => 'Email Verification',
        self::OTHERS_TEMPLATE             => 'Others'
    ];

    private $emailTemplatelList;
    private $tableName = 'email_template';

    public function __construct()
    {
        $this->load->library('language_function');
    }

    public function getPlatformType()
    {
        return $this->PLATFORM_TYPE;
    }

    public function getTemplateType()
    {
        return $this->TEMPLATE_TYPE;
    }

    public function getAllTemplateType()
    {
        $this->db->group_by(['platform_type', 'template_name']);
        $this->db->order_by('id');
        $qry = $this->db->get($this->tableName);
        $rlt = $qry->result_array();

        $data = [];
        foreach ($rlt as $row) {
            $data[$row['platform_type']][] = $row;
        }

        return $data;
    }

    public function getTemplateRowById($id)
    {
        $cond = ['id' => $id];
        $qry = $this->db->get_where($this->tableName, $cond);
        return $qry->row_array();
    }

    public function getIsEnableByTemplateName($template_name)
    {
        $cond = ['template_name' => $template_name];
        $qry = $this->db->get_where($this->tableName, $cond);
        $template = $qry->row_array();
        if(!isset($template['is_enable'])){
            return false;
        }
        else{
           return $template['is_enable'];
        }
    }

    public function getTemplateListByName($templateName, $templateLang = null)
    {
        $cond = [
            'template_name' => $templateName
        ];

        if ($templateLang) {
            $cond['template_lang'] = $templateLang;
        }

        $this->db->order_by('template_lang');
        $qry = $this->db->get_where($this->tableName, $cond);
        return $qry->result_array();
    }

    public function getCurtPlatformType()
    {
        $templateType = [];
        $templateList = $this->getAllTemplateList();
        foreach ($templateList as $row) {
            $templateType[] = $row[0]['platform_type'];
        }

        $totalPlatformType = $this->getPlatformType();
        $curtPlatformType = array_filter($totalPlatformType, function($name, $type) use ($templateType) {
            return in_array($type, $templateType);
        }, ARRAY_FILTER_USE_BOTH);

        return $curtPlatformType;
    }

    public function getAllTemplateList()
    {
        if (!$this->emailTemplatelList) {
            $this->emailTemplatelList = $this->getAllTemplateType();
        }
        return $this->emailTemplatelList;
    }

    public function getCurtPlatformTemplateDetail($templateName)
    {
        $systemLang = $this->language_function->getAllSystemLanguages();
        $templateList   = $this->getTemplateListByName($templateName);

        $curtPlatformTemplateDetail = [];
        if ($templateList) {
            $copyTmplFirst = array_first($templateList);
            $copyTmplFirst['id'] = '';
            $copyTmplFirst['mail_subject'] = '';
            $copyTmplFirst['mail_content'] = '';

            foreach ($systemLang as $langRow) {
                $isSameLang = False;
                $langId = $langRow['key'];
                foreach ($templateList as $tmplRow) {
                    if ($tmplRow['template_lang'] == $langId) {
                        $curtPlatformTemplateDetail[$langId] = $tmplRow;
                        $isSameLang = True;
                        break;
                    }
                }

                if (!$isSameLang) {
                    $copyTmplFirst['template_lang'] = $langId;
                    $curtPlatformTemplateDetail[$langId] = $copyTmplFirst;
                }

                $curtPlatformTemplateDetail[$langId]['template_lang_text'] = $this->language_function->langToLocalWord($langId);
            }
        }

        return $curtPlatformTemplateDetail;
    }

    public function insertData($data)
    {
        return $this->db->insert($this->tableName, $data);
    }

    public function updateData($id, $data, $cond = null)
    {
        if (isset($id) && $id) {
            $this->db->where('id', $id);
        }

        if ($cond) {
            $this->db->where($cond);
        }
        return $this->db->update($this->tableName, $data);
    }
}