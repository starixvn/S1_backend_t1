<?php

require_once(dirname(__FILE__). '/../../submodules/core-lib/application/libraries/vendor/autoload.php');

use Symfony\Component\Yaml\Yaml;

class Cli {

	private $climate;

	const WHITE_FUNCTION_LIST=[
		'sync_submodule',
		'show_submodule_branch',
		'show_submodule_status',
		'switch_core_lib',
		'switch_game_lib',
		'switch_payment_lib',
		'sync_submodule_to_master',
		'switch_google_k8s',
		'build_base_image',
		'build_og_image',
		'push_image',
		'generate_image',
		'deploy_image',
		'run_migrate',
		'sync_og_config',
		'deploy_config',
		'search_timeout_sql',
		'restart_php',
		'create_links_on_pod',
		'create_links_on_local',
		'push_submodule_to_master',
		'create_links_on_local',
		'check_php_version',
		'stop_docker_env',
		'start_docker_env',
		'start_docker_env_local_debug',
		'init_vueui',
		'generate_vueui',
		'run_vueui_dev',
		'check_deploy_permission',
		'validate_php_opening_tag',
	];

	public function __construct() {

		// parent::__construct();

		$this->climate = new League\CLImate\CLImate;

		$cmd_desc='available command: '.implode(self::WHITE_FUNCTION_LIST, ',');

		$params=[
			'command'=>[
				'description'=>$cmd_desc,
				'required'=>true,
			],
			'debug'=>[
				'description'=>'debug mode to print more log',
				'prefix'=>'D',
				'longPrefix'=>'debug',
				'noValue' => true,
			],
			'branch'=>[
				'description'=>'branch name',
				'prefix'=>'b',
				'longPrefix'=>'branch',
			],
			'google_account'=>[
				'description'=>'google account',
				'prefix'=>'a',
				'longPrefix'=>'google_account',
			],
			'google_project_name'=>[
				'description'=>'google project name: externalgateway-167009 or ibetg-164502',
				'prefix'=>'p',
				'longPrefix'=>'google_project_name',
			],
			'google_zone'=>[
				'description'=>'google zone, for example: asia-east1-a',
				'prefix'=>'z',
				'longPrefix'=>'google_zone',
			],
			'google_k8s_name'=>[
				'description'=>'google k8s name , for example: t1tog',
				'prefix'=>'k',
				'longPrefix'=>'google_k8s_name',
			],
			'commit_sha_id'=>[
				'description'=>'commit sha id',
				'prefix'=>'s',
				'longPrefix'=>'commit_sha_id',
			],
			'client_name'=>[
				'description'=>'client name',
				'prefix'=>'c',
				'longPrefix'=>'client_name',
			],
			'timeout_sql'=>[
				'description'=>'timeout sql: seconds',
				'prefix'=>'t',
				'longPrefix'=>'timeout_sql',
			],
			'kill_sql'=>[
				'description'=>'kill timeout sql',
				'prefix'=>'K',
				'longPrefix'=>'kill_sql',
				'noValue' => true,
			],
			'gitlab_user_email'=>[
				'description'=>'gitlab user email',
				'prefix'=>'e',
				'longPrefix'=>'email',
			],
			'ci_job_name'=>[
				'description'=>'job name',
				'prefix'=>'N',
				'longPrefix'=>'job_name',
			],
			'ci_job_stage'=>[
				'description'=>'job stage',
				'prefix'=>'S',
				'longPrefix'=>'job_stage',
			],
			'ci_commit_ref_name'=>[
				'description'=>'commit ref name',
				'prefix'=>'R',
				'longPrefix'=>'commit_name',
			],
		];
		$this->init_params=['google_project_name', 'client_name', 'branch', 'commit_sha_id',
		'google_account', 'google_zone', 'google_k8s_name', 'timeout_sql', 'kill_sql',
		'gitlab_user_email','ci_job_name','ci_job_stage','ci_commit_ref_name'];

		$this->climate->arguments->add($params);

		//init project path
		$this->project_home=realpath(dirname(__FILE__). '/../..');

		define('APPPATH', $this->project_home.'/admin/application');
		define('BASEPATH', $this->project_home.'/admin');

		$this->core_lib_home=$this->project_home.'/submodules/core-lib';
		$this->game_lib_home=$this->project_home.'/submodules/game-lib';
		$this->payment_lib_home=$this->project_home.'/submodules/payment-lib';

		$this->og_admin_home=$this->project_home.'/admin';
		$this->og_player_home=$this->project_home.'/player';
		$this->og_aff_home=$this->project_home.'/aff';
		$this->og_agency_home=$this->project_home.'/agency';

		$this->climate->red('running on '.$this->project_home);

		// if(!$this->climate->arguments->defined('verbose')){
			// return $this->climate->usage();
		// }

		// try{

		// 	$this->climate->arguments->parse();

		// }catch(Exception $e){

		// 	return $this->climate->usage();

		// }

	}

	public function run(){
		$success=false;

		try{

			$this->climate->arguments->parse();

		}catch(Exception $e){

			$this->error_log('parse arguments error', $e);
			return $this->climate->usage();

		}

		$command=$this->climate->arguments->get('command');

		if(empty($command)){
			return;
		}

		$this->is_debug=$this->climate->arguments->defined('debug');

		$this->setClassVarFromArg($this->init_params);

		// $this->climate->draw('bender');
		// $this->climate->animation('bender')->enterFrom('top');

		$this->climate->border('=');

		//check white list
		if(in_array($command, self::WHITE_FUNCTION_LIST)){

			$t1=new DateTime();
			$this->climate->flank('start '.date('Y-m-d H:i:s'));
			$success=$this->$command();
			$this->climate->flank('end '.date('Y-m-d H:i:s').' cost: '.$t1->diff(new DateTime())->format('%H:%I:%S'));

			if(!$success){
				$this->error_log('run command:'.$command.' failed');
			}
		}else{

			$this->climate->backgroundRed('wrong command name');

			$this->climate->usage();

		}

		$this->climate->border('=');

		return $success;

	}

	public function show_submodule_branch(){

		return $this->passthruBool('git submodule foreach git branch');

	}

	public function show_submodule_status(){

		return $this->passthruBool('git submodule foreach git status');

	}

	public function switch_core_lib(){

		return $this->switchToBranch($this->core_lib_home);

	}

	public function switch_game_lib(){

		return $this->switchToBranch($this->game_lib_home);

	}

	public function switch_payment_lib(){

		return $this->switchToBranch($this->payment_lib_home);

	}

	private function switchToBranch($path){

		$this->branch=$this->climate->arguments->get('branch');

		$success=false;
		if(!empty($this->branch)){

			$script=<<<EOD
git -C {$path} checkout {$this->branch}
git -C {$path} branch
EOD;
			//try run
			$success=$this->passthruBool($script);

		}else{
			$this->showWarning('no branch name, do nothing');
		}

		return $success;
	}

	const MAIN_BRANCH_LIST=['live_stable_prod', 'live_stable_rc', 'live_stable'];

	public function sync_submodule_to_master(){
		$this->info_log('sync_submodule_to_master');
		$this->climate->border();
		# git submodule foreach git pull origin master
		$success=$this->passthruBool('git submodule foreach git pull origin master');
		$this->climate->border();

		return $success;
	}

	public function push_submodule_to_master(){

		$success=$this->sync_submodule_to_master();

		//try commit main branch
		$repo = new Cz\Git\GitRepository($this->project_home);
		if($repo->hasChanges()){
			$this->debug_log('has changes');
			$success=$this->passthruBool('git status');
			$repo->addAllChanges();
			$repo->commit('update submodule version');
			$success=$this->passthruBool('git status');
			$repo->push('origin');
			$success=$this->passthruBool('git status');
		}else{

			$this->debug_log('nothing changes');
		}

		return $success;
	}

	public function switch_google_k8s(){
		$this->info_log('switch_google_project');
		$this->climate->border();

		if(empty($this->google_account)){
			$this->error_log('google_account is empty');
			$this->climate->usage();
			return false;
		}

		if(empty($this->google_project_name)){
			$this->error_log('google_project_name is empty');
			$this->climate->usage();
			return false;
		}

		if(empty($this->google_zone)){
			$this->error_log('google_zone is empty');
			$this->climate->usage();
			return false;
		}

		if(empty($this->google_k8s_name)){
			$this->error_log('google_k8s_name is empty');
			$this->climate->usage();
			return false;
		}

		$script=<<<EOD
gcloud config set account {$this->google_account}
gcloud container clusters get-credentials {$this->google_k8s_name} --zone {$this->google_zone} --project {$this->google_project_name}
EOD;
		$success=$this->passthruBool($script);

		$this->climate->border();

		return $success;
	}

	public function build_base_image(){
		$this->info_log('build_base_image');
		$this->climate->border();

		if(empty($this->google_project_name)){
			$this->error_log('google_project_name is empty');
			$this->climate->usage();
			return false;
		}

		$this->info_log('start build base image, google_project_name: '.$this->google_project_name);

		$buildBase=<<<EOD
docker build -t ogbasephp7:latest {$this->project_home}/baseimage
docker tag ogbasephp7:latest asia.gcr.io/{$this->google_project_name}/ogbasephp7:latest-live
EOD;
		$success=$this->passthruBool($buildBase);

		$this->climate->border();

		return $success;
	}

	public function build_og_image(){
		$this->info_log('build_og_image');
		$this->climate->border();

		//check variabes
		if(empty($this->google_project_name)){
			$this->error_log('google_project_name is empty');
			$this->climate->usage();
			return false;
		}

		if(empty($this->branch)){
			$this->error_log('branch is empty');
			$this->climate->usage();
			return false;
		}

		if(empty($this->commit_sha_id)){
			$this->error_log('commit_sha_id is empty');
			$this->climate->usage();
			return false;
		}

		//sync submodule
		$this->sync_submodule();
		//generate version file
		file_put_contents($this->project_home.'/admin/public/version', $this->commit_sha_id);
		//generate js
		//npm install
		//npm run build

		$buildOG=<<<EOD
docker build -t og:{$this->branch} {$this->project_home}
docker tag og:{$this->branch} asia.gcr.io/{$this->google_project_name}/og:{$this->branch}
docker tag og:{$this->branch} asia.gcr.io/{$this->google_project_name}/og:{$this->branch}-{$this->commit_sha_id}
EOD;

		$success=$this->passthruBool($buildOG);

		$this->climate->border();

		return $success;
	}

	public function push_image(){

		$this->info_log('push_image');
		$this->climate->border();

		//check variabes
		if(empty($this->google_project_name)){
			$this->error_log('google_project_name is empty');
			$this->climate->usage();
			return false;
		}

		if(empty($this->branch)){
			$this->error_log('branch is empty');
			$this->climate->usage();
			return false;
		}

		if(empty($this->commit_sha_id)){
			$this->error_log('commit_sha_id is empty');
			$this->climate->usage();
			return false;
		}

		$script=<<<EOD
gcloud docker -- push asia.gcr.io/{$this->google_project_name}/og:{$this->branch}
gcloud docker -- push asia.gcr.io/{$this->google_project_name}/og:{$this->branch}-{$this->commit_sha_id}
EOD;

		$success=$this->passthruBool($script);

		$this->climate->border();
		return $success;
	}

	public function generate_image(){

		if(!$this->switch_google_k8s()){
			return false;
		}

		if(!$this->build_base_image()){
			return false;
		}

		if(!$this->build_og_image()){
			return false;
		}

		if(!$this->push_image()){
			return false;
		}

		return true;

	}

	public function deploy_image(){

		//check variabes
		if(empty($this->google_project_name)){
			$this->error_log('google_project_name is empty');
			$this->climate->usage();
			return false;
		}
		if(empty($this->client_name)){
			$this->error_log('client_name is empty');
			$this->climate->usage();
			return false;
		}
		if(empty($this->branch)){
			$this->error_log('branch is empty');
			$this->climate->usage();
			return false;
		}
		if(empty($this->commit_sha_id)){
			$this->error_log('commit_sha_id is empty');
			$this->climate->usage();
			return false;
		}

		$success=false;
		$image_tag=$this->branch.'-'.$this->commit_sha_id;
		$found=false;

		$script="gcloud container images list-tags asia.gcr.io/".$this->google_project_name."/og";
		$rlt=$this->execCmd($script);

		if(!empty($rlt)){
			$this->debug_log('get image list', $rlt['0']);
			if(!empty($rlt[0])){
				$arr=explode("\n", $rlt[0]);
				foreach ($arr as $line) {
					if(strpos($line, $image_tag)!==false){
						//found
						$found=true;
						$this->debug_log('found image', $line, $image_tag);

						// $fldArr=explode(" ", $line);
						// $tagArr=explode(",", $fldArr[2]);
						// $image_tag=$tagArr[1];
						// if($image_tag!=$this->branch.'-'.$this->commit_sha_id){
						// 	$found=false;
						// }
						break;
					}
				}
			}
		}
		if($found){

			$success=$this->switch_google_k8s();
			if($success){

				//try set image
				$script="kubectl set image deployment/".$this->client_name."-og ".$this->client_name."-og=asia.gcr.io/".$this->google_project_name."/og:".$image_tag;
				// $this->debug_log($script);
				$success=$this->passthruBool($script);

				if($success){
					sleep(2);

					$script="kubectl rollout status deployment/".$this->client_name."-og";

					$success=$this->passthruBool($script);
				}
			}else{
				$this->error_log('switch_google_k8s failed');
			}
		}else{
			//faild
			$this->error_log('cannot find target image:'.$image_tag);
		}

		if($success){
			$success=$this->switch_google_k8s();
			sleep(1);
			$success=$this->run_migrate();
		}

		//$(echo Y | gcloud alpha container images list-tags asia.gcr.io/$GOOGLE_PROJECT_NAME/og | grep $CI_COMMIT_REF_NAME,$CI_COMMIT_REF_NAME | awk '{print $2}' | awk -F, '{print $2}')

		return $success;
	}

	public function run_migrate(){
		//kubectl get pods | grep -v "\-sync\-" | grep $CLIENT_NAME-og- | tail -n 1 | awk '{print "kubectl exec -it "$1" -- su - vagrant -c \"cd /home/vagrant/Code/og; ./migrate.sh \""}'  | sh

		if(empty($this->client_name)){
			$this->error_log('client_name is empty');
			$this->climate->usage();
			return false;
		}

		$success=$this->switch_google_k8s();
		$found=false;
		$clientPodName=null;
		if($success){

			$clientPodName=$this->searchPod($this->client_name.'-og-');
			$found=!empty($clientPodName);

			if($found){
				//run migrate
				$this->debug_log('run migrate');
				$script='kubectl exec -it '.$clientPodName.' -- su - vagrant -c "cd /home/vagrant/Code/og; ./migrate.sh "';
				$success=$this->passthruBool($script);

				if(!$success){
					$this->error_log('run migrate failed');
				}
				// $success=true;
			}
		}


		return $success;

	}

	public function sync_og_config(){
		//kubectl get pods | grep -v "\-sync\-" | grep $CLIENT_NAME-og- | tail -n 1 | awk '{print "kubectl exec -it "$1" -- su - vagrant -c \"cd /home/vagrant/Code/og; ./migrate.sh \""}'  | sh

		$og_config=$this->project_home.'/og_config';
		$git_url='git@git.smartbackend.com:sbs/og_config.git';
		$success=false;
		try{

			if(is_dir($og_config)){
				$this->info_log('pull '.$og_config);
				//try pull
				$repo = new Cz\Git\GitRepository($og_config);
				$repo->pull('origin');
			}else{
				$this->info_log('clone '.$git_url.' into '.$og_config);
				//clone
				$repo = Cz\Git\GitRepository::cloneRepository($git_url, $og_config);
			}

			$success=true;

		}catch(Exception $e){

			$this->debug_log($e);
			$success=false;

		}

		return $success;

	}

	public function deploy_config(){

		if(empty($this->client_name)){
			$this->error_log('client_name is empty');
			$this->climate->usage();
			return false;
		}

		$success=$this->sync_og_config();
		if(!$success){
			$this->error_log('sync_og_config failed');
			return $success;
		}

		$success=$this->switch_google_k8s();
		if(!$success){
			$this->error_log('switch_google_k8s failed');
			return $success;
		}

		//try deploy
	    // - kubectl create configmap $CLIENT_NAME-og-secret-keys --dry-run -o yaml --from-file=og_config/$CLIENT_NAME/og/secret_keys | kubectl replace -f -
	    // - kubectl create configmap $CLIENT_NAME-og-config-local --dry-run -o yaml --from-file=og_config/$CLIENT_NAME/og/config_local | kubectl replace -f -

		$success=$this->deploy_config_secret_keys();

		if($success){
			$success=$this->deploy_config_og_local();
		}

		return $success;
	}

	private function deploy_config_secret_keys(){

		$success=false;

		$script='kubectl create configmap '.$this->client_name.'-og-secret-keys --dry-run -o yaml --from-file='.$this->project_home.'/og_config/'.$this->client_name.'/og/secret_keys';
		$rlt=$this->execCmd($script);
		if(!empty($rlt) && !empty($rlt[0])){
			//try replace file
			$tmpFile=tempnam('/tmp','CON');
			$success=file_put_contents($tmpFile, $rlt[0])!==false;
			if($success){

				// $this->debug_log('yaml file', $tmpFile, file_get_contents($tmpFile));

				$this->info_log('try replace secret_keys configmap '.$tmpFile.' to '.$this->client_name);
				$script='kubectl replace -f '.$tmpFile;
				$success=$this->passthruBool($script);
				if(!$success){
					$this->error_log('replace secret_keys configmap failed');
				}
			}

			unlink($tmpFile);
		}else{
			$success=false;
			$this->error_log('create secret_keys configmap file failed');
		}

		return $success;
	}

	private function deploy_config_og_local(){

		$success=false;

		$script='kubectl create configmap '.$this->client_name.'-og-config-local --dry-run -o yaml --from-file='.$this->project_home.'/og_config/'.$this->client_name.'/og/config_local';
		$rlt=$this->execCmd($script);
		if(!empty($rlt) && !empty($rlt[0])){
			//try replace file
			$tmpFile=tempnam('/tmp','CON');
			$success=file_put_contents($tmpFile, $rlt[0])!==false;
			if($success){

				// $this->debug_log('yaml file', $tmpFile, file_get_contents($tmpFile));

				$this->info_log('try replace og_local configmap '.$tmpFile.' to '.$this->client_name);
				$script='kubectl replace -f '.$tmpFile;
				$success=$this->passthruBool($script);
				if(!$success){
					$this->error_log('replace og_local configmap failed');
				}
			}

			unlink($tmpFile);
		}else{
			$success=false;
			$this->error_log('create og_local configmap file failed');
		}

		return $success;
	}

	public function sync_submodule(){

		$this->climate->border();
		$this->info_log('get current branch');

		$success=false;

		$repoMain = new Cz\Git\GitRepository($this->project_home);
		// $repoMain->fetch('origin', ['-p']);
		// $this->debug_log('all branch', $repoMain->getLocalBranches(), $repoMain->getBranches());

		//check current branch
		$this->branch=$this->climate->arguments->get('branch');
		if(empty($this->branch)){
			//try get branch from git
			// $script="git branch | grep \* | cut -d ' ' -f2";
			// $branch_name=$this->execCmd($script, true, true);
			// if(!empty($branch_name)){
			// 	$this->branch=trim($branch_name[0]);
			// }

			$this->branch=$repoMain->getCurrentBranchName();
		}

		$this->debug_log('branch', $this->branch);

		if(empty($this->branch)){
			//can't get branch
			return false;
		}

		$repoCoreLib = new Cz\Git\GitRepository($this->core_lib_home);
		$repoPaymentLib = new Cz\Git\GitRepository($this->payment_lib_home);
		$repoGameLib = new Cz\Git\GitRepository($this->game_lib_home);

		if(!$this->isProtectedMainBranch($this->branch)){

			//core lib
			$this->info_log('sync core lib');

			$master_branch_name=$this->findMasterBranchName($repoCoreLib, $this->branch);

			//payment lib
			$this->info_log('sync payment lib');

			$master_branch_name=$this->findMasterBranchName($repoPaymentLib, $this->branch);

			//game lib
			$this->info_log('sync game lib');

			$master_branch_name=$this->findMasterBranchName($repoGameLib, $this->branch);

		}else{
			$this->info_log('ignore main branch', $this->branch);
		}

		$this->climate->border();

		// git submodule foreach git branch
		$this->passthruBool('git submodule foreach git branch');

// 		$script=<<<EOD
// echo {$this->core_lib_home}
// git -C {$this->core_lib_home} rev-parse HEAD
// echo {$this->payment_lib_home}
// git -C {$this->payment_lib_home} rev-parse HEAD
// echo {$this->game_lib_home}
// git -C {$this->game_lib_home} rev-parse HEAD
// EOD;
// 		// git submodule update
// 		//try run update submodule
// 		$ver=$this->execCmd($script, true, true);

		$ver=['main'=>$repoMain->getLastCommitId(), 'core-lib'=>$repoCoreLib->getLastCommitId(), 'payment-lib'=>$repoPaymentLib->getLastCommitId(), 'game-lib'=>$repoGameLib->getLastCommitId()];

		$success=file_put_contents($this->project_home.'/submodules/all_version.json', json_encode($ver, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT))!==false;

		return $success;
	}

	public function search_timeout_sql(){

		$success=false;

		if(empty($this->timeout_sql)){
			$this->timeout_sql=100;
		}

		//try load config
		$config_file=dirname(__FILE__).'/../../secret_keys/config_secret_local.php';

		if(file_exists($config_file)){

			require_once(dirname(__FILE__).'/../application/config/constants.php');

			include $config_file;

			$db_hostname=$config['db.default.hostname'];
			$db_port=$config['db.default.port'];
			$db_username=$config['db.default.username'];
			$db_password=$config['db.default.password'];
			$db_database=$config['db.default.database'];

            $conn=mysqli_connect($db_hostname,
                $db_username,
                $db_password,
                $db_database,
                $db_port);

	        if($conn){

	            $charset=$config['db.default.char_set'];
            	mysqli_set_charset($conn, $charset);
            	$sql='show full processlist';
                $qry = mysqli_query($conn, $sql);

                if($qry){

					while ($row = mysqli_fetch_array($qry, MYSQLI_ASSOC)) {
						if($row['Command']=='Query'){
							// $this->debug_log('query', $row);
							if($row['Time']>=$this->timeout_sql){
								$this->info_log('time', $row['Time'], $row['Info']);
								$sql=trim($row['Info']);

								if($this->kill_sql){
									//only kill select
									if(substr($sql, 0, 6)=='select'){
						                $qry = mysqli_query($conn, 'kill '.$row['Id']);
									}else{
										$this->info_log('ignore other sql', $sql);
									}
								}
							}else{
								$this->debug_log('ignore time', $row['Time']);
							}
						}
                        unset($row);
					}

                    mysqli_free_result($qry);

					$success=true;

                }else{
                	$this->error_log('query mysql failed', $sql);
                }

        	}else{
        		$this->error_log('connect mysql failed', $db_hostname,
        			$db_username, $db_password, $db_database, $db_port);
        	}

			unset($config);

		}else{
			$this->error_log('load config file failed', $config_file);
		}

		return $success;
	}

	public function check_php_version(){
		$success=false;
		$v=phpversion();

		$this->info_log('php :'.$v);

		$module_list=['gd', 'mysql', 'mysqli', 'curl', 'xmlrpc', 'xsl', 'soap', 'zip', 'xml',
			'mbstring', 'bcmath', 'mcrypt', 'memcached', 'redis', 'v8js'];

		foreach ($module_list as $module) {
			$success=extension_loaded($module);
			if(!$success){
				$this->error_log('module '.$module.' cannot be found');
				break;
			}
		}

		return $success;
	}

	public function validate_php_opening_tag(){
		
    	//$str = shell_exec('find -name "*.php" -type f | egrep -v "/views/|/vendor/|/errors/|/sites/|/secret_keys" ');
		$str=$this->execCmd('find  -name "*.php" -type f | egrep -v "/views/|/vendor/|/errors/|/sites/|/secret_keys/|/submodules/" ',false, true, true);
		$php_files = explode(PHP_EOL, $str[0]);
		$error_files = [];
		$success = false;
		$scanned_files_cnt = count($php_files);
		$error_files_cnt = 0;
		$existed_files_cnt = 0;

		foreach ($php_files as $file) {

			if(file_exists($file)){
				$existed_files_cnt++;
    			//$output = shell_exec('head -n 5 '. $file);
				$output=$this->execCmd('head -n 5 '. $file,false, true, true);
				$lines = explode(PHP_EOL, $output[0]);
				$line_cnt = 0;
				$regex  = "/<\?php/";
				$is_php_tag_found = false;

				foreach ($lines as $line) {
					if(preg_match($regex, $line, $match)){
						$is_php_tag_found = true;
						if($line_cnt > 0 || stripos($line, "<?php") > 0 ){
							array_push($error_files, $file);          
						}	
					}
				$line_cnt++;
				}
				if($is_php_tag_found === false){
					array_push($error_files, $file); 
				}
			}
		}
		if(!empty($error_files)){
			$error_files_cnt = count($error_files);
			$this->error_log('php opening tag error found on the following files',$error_files);
		}else{
			$success = true;
		}

		$this->info_log('result:','scanned_files_cnt: '.$scanned_files_cnt. ' existed_files_cnt: '.$existed_files_cnt. ' error_files_cnt: '.$error_files_cnt);

		return $success;

	}

	public function stop_docker_env(){
		$success=true;
		$rlt=$this->execCmd('docker ps -q');
		$this->debug_log('get docker container list', $rlt);
		if(!empty($rlt)){
			if(!empty($rlt[0])){
				$arr=explode("\n", $rlt[0]);
				$stop_docker='docker stop '.implode(' ', $arr);
				$success=$this->passthruBool($stop_docker);
			}
		}

		return $success;
	}

	protected function batchDeleteDir($target) {
		if(!file_exists($target)){
		    $this->debug_log('delete target not exist', $target);
			return true;
		}
	    if(is_dir($target)){
	        $files = glob( $target . '*', GLOB_MARK ); //GLOB_MARK adds a slash to directories returned

	        foreach( $files as $file ){
	            $succ=$this->batchDeleteDir( $file );
	            if(!$succ){
	            	return $succ;
	            }
	        }

	        $this->debug_log('try delete', $target);
	        rmdir( $target );
	        return true;
	    } elseif(is_file($target)) {
	        $this->debug_log('try delete', $target);
	        return unlink( $target );
	    }

	    $this->debug_log('not file or dir', $target);
	    return false;
	}

	public function start_docker_env(){

		if(empty($this->branch)){
			$this->error_log('branch is empty');
			$this->climate->usage();
			return false;
		}

		$success=$this->stop_docker_env();
		if(!$success){
			$this->error_log('stop docker container failed');
			return $success;
		}

		//clear redis local file
		// $deleteDir=$this->project_home.'/local_dev_docker/data/var/redis/';
		// $this->info_log('deleting ...', $deleteDir);
		// $success=$this->batchDeleteDir($deleteDir);
		// if(!$success){
		// 	$this->error_log('delete '.$deleteDir.' failed');
		// 	return $success;
		// }
		// $deleteDir=$this->project_home.'/local_dev_docker/data/var/redis-sentinel/';
		// $this->info_log('deleting ...', $deleteDir);
		// $success=$this->batchDeleteDir($deleteDir);
		// if(!$success){
		// 	$this->error_log('delete '.$deleteDir.' failed');
		// 	return $success;
		// }

		$branch=str_replace('_','',$this->branch);

		$composeFile = Yaml::parse(file_get_contents($this->project_home.'/docker-compose.yml'));
		$composeFile['services']['og']['hostname']='default_og_'.$branch;
		$yaml = Yaml::dump($composeFile, 2, 4);
		file_put_contents($this->project_home.'/docker-compose-tmp.yml', $yaml);

		if($success){
			$docker_compose_start='docker-compose -f docker-compose-tmp.yml -p og'.$branch.' up -d';
			$success=$this->passthruBool($docker_compose_start);
			if($success){
				$this->passthruBool('docker ps');
			}
		}

		return $success;
	}

	public function start_docker_env_local_debug(){

		if(empty($this->branch)){
			$this->error_log('branch is empty');
			$this->climate->usage();
			return false;
		}

		$success=$this->stop_docker_env();

		$branch=str_replace('_','',$this->branch);

		$composeFile = Yaml::parse(file_get_contents('docker-compose.yml'));
		$composeFile['services']['og']['hostname']='default_og_'.$branch;
		$composeFile['services']['og']['image']='og_local:dev-php7';
		$yaml = Yaml::dump($composeFile, 2, 4);
		file_put_contents('docker-compose-tmp.yml', $yaml);

		if($success){
			$docker_compose_start='docker-compose -f docker-compose-tmp.yml -p og'.$branch.' up -d';
			$success=$this->passthruBool($docker_compose_start);
			if($success){
				$this->passthruBool('docker ps');
			}
		}

		return $success;
	}

	public function init_vueui(){
		return $this->passthruBool('cd vueui && npm i && cd ..');
	}

	public function generate_vueui(){
		return $this->passthruBool('cd vueui && npm run build && cd ..');
	}

	public function run_vueui_dev(){
		return $this->passthruBool('cd vueui && npm run dev && cd ..');
	}

	//===private function=================================================================
	/**
	 * restart php
	 * @return bool $success
	 */
	public function restart_php(){

		if(empty($this->client_name)){
			$this->error_log('client_name is empty');
			$this->climate->usage();
			return false;
		}

		$success=$this->switch_google_k8s();
		if(!$success){
			$this->error_log('switch_google_k8s failed');
			return $success;
		}

		$this->climate->border();

		$success=false;

		$podList=$this->searchPod($this->client_name.'-og-', true);

		$this->debug_log('pod list', $podList);
		if(!empty($podList)){

			foreach ($podList as $podName) {
				//exec restart php
				$cmd='supervisorctl restart php-fpm';
				$this->execOnPod($cmd, $podName);
			}

			$success=true;
		}

		$this->climate->border();

		return $success;
	}

    public function create_links_on_pod(){
		//kubectl get pods | grep -v "\-sync\-" | grep $CLIENT_NAME-og- | tail -n 1 | awk '{print "kubectl exec -it "$1" -- su - vagrant -c \"cd /home/vagrant/Code/og; ./migrate.sh \""}'  | sh

		if(empty($this->client_name)){
			$this->error_log('client_name is empty');
			$this->climate->usage();
			return false;
		}

		$success=$this->switch_google_k8s();
		$found=false;
		$clientPodName=null;
		if($success){

            $podList=$this->searchPod($this->client_name.'-og-', true);

            if(!empty($podList)){

                foreach ($podList as $podName) {				//run migrate
                    $this->debug_log('run create_links');
                    $script='kubectl exec -it '.$podName.' -- su - vagrant -c "cd /home/vagrant/Code/og; ./create_links.sh "';
                    $succ=$this->passthruBool($script);

                    if(!$succ){
                        $this->error_log('run create_links failed');
                    }
                }
			}
		}


		return $success;

	}

	public function check_deploy_permission(){

		$this->info_log('checking_deploy_permission for production only');
		$this->climate->border();
		$this->info_log("checking permission of {$this->gitlab_user_email} on {$this->ci_job_name} , {$this->ci_job_stage}");

		if(empty($this->ci_job_name)){
			$this->error_log('ci_job_name is empty');
			$this->climate->usage();
			return false;
		}

		if(empty($this->gitlab_user_email)){
			$this->error_log('gitlab_user_email is empty');
			$this->climate->usage();
			return false;
		}

		if(empty($this->ci_commit_ref_name)){
			$this->error_log('ci_commit_ref_name is empty');
			$this->climate->usage();
			return false;
		}

		if(empty($this->ci_job_stage)){
			$this->error_log('ci_job_stage is empty');
			$this->climate->usage();
			return false;
		}

		$not_live_clients=["lotterydemo"];
		$mdb_clients=["mdb","idngame", "idngameother", "tripleonetech","sexycasino", "dj001", "dj002", "ice2020"];
		$liveBranchList=['live_stable_rc', 'live_stable_prod', 'live_stable_mdb',
			'live_stable_prod-PHP7', 'live_stable_mdb-PHP7', 'xcyl', 'macaopj'];

		$emails=["james.cto@tripleonetech.net", "noke.php.tw@tripleonetech.net", "yunfei.dev@tripleonetech.net",
			"aris.php.ph@tripleonetech.net", "ned.sa.tw@tripleonetech.net",
			"jhunel.php.ph@tripleonetech.net", "zuma.sa.tw@tripleonetech.net",
			"sony.php.ph@tripleonetech.net", "bermar.php.ph@tripleonetech.net", "jouan.php.tw@tripleonetech.net"];

		if(in_array($this->ci_job_name, $not_live_clients)){
			$this->info_log('permission is granted on not live client');
			$this->climate->border();
			return true;
		}else{
			$this->info_log($this->ci_job_name.' is a live client');
		}
		//remove -og_sync
		$noSyncJobName=$this->ci_job_name;
		if(substr($noSyncJobName, -8, 8)=='-og_sync'){
			$noSyncJobName=substr($noSyncJobName, 0, strlen($noSyncJobName)-8);
		}
		if(substr($noSyncJobName, -7, 7)=='-shadow'){
			$noSyncJobName=substr($noSyncJobName, 0, strlen($noSyncJobName)-7);
		}
		$this->debug_log('noSyncJobName is', $noSyncJobName);
		//protect mdb branch and client
		$isMDBBranch=strpos($this->ci_commit_ref_name, 'live_stable_mdb')===0;
		$isMDBClient=in_array($noSyncJobName, $mdb_clients);
		$this->info_log('branch is mdb?', $isMDBBranch, 'client is mdb?', $isMDBClient);
		if(($isMDBBranch && !$isMDBClient) || (!$isMDBBranch && $isMDBClient)){
			//don't allow deploy
			$this->error_log('permission is declined because client does not match branch');
			$this->climate->border();
			return false;
		}
		//protect live client
		$isLiveBranch=in_array($this->ci_commit_ref_name, $liveBranchList);
		$this->info_log('is live branch?', $isLiveBranch, 'live branch list', $liveBranchList);
		if(!$isLiveBranch){
			$this->error_log('permission is declined because only allow prod/mdb branch to live client');
			$this->climate->border();
			return false;
		}

		if(in_array($this->gitlab_user_email, $emails)){
			$this->info_log("permission is granted");
			$this->climate->border();
			return true;
		}else{
			$this->error_log("permission is declined");
			$this->climate->border();
			return false;
		}

	}

    public function create_links_on_local(){

    	$success=false;

    	$summary=[];

    	//try sync submodule
    	if(file_exists($this->project_home.'/.git')){

    		$success=$this->sync_submodule();

    	}

    	//create submodule links
    	if(file_exists($this->project_home.'/submodules/core-lib')){

    		//link system
    		$summary['link:core-lib/system']=$this->safeCreateLink($this->core_lib_home.'/system', $this->og_admin_home.'/system');
    		//migrations
    		$summary['link:core-lib/application/migrations']=$this->safeCreateLink($this->core_lib_home.'/application/migrations', $this->og_admin_home.'/application/migrations');
    		//lib
    		$summary['link:core-lib/application/libraries/vendor']=$this->safeCreateLink($this->core_lib_home.'/application/libraries/vendor', $this->og_admin_home.'/application/libraries/vendor');
    		$summary['link:core-lib/application/libraries/scheduler']=$this->safeCreateLink($this->core_lib_home.'/application/libraries/scheduler', $this->og_admin_home.'/application/libraries/scheduler');
    		$summary['link:core-lib/application/libraries/third_party']=$this->safeCreateLink($this->core_lib_home.'/application/libraries/third_party', $this->og_admin_home.'/application/libraries/third_party');
    		//config
    		$summary['link:core-lib/application/config/apis.php']=$this->safeCreateLink($this->core_lib_home.'/application/config/apis.php', $this->og_admin_home.'/application/config/apis.php');
    		$summary['link:core-lib/application/config/external_system_list.xml']=$this->safeCreateLink($this->core_lib_home.'/application/config/external_system_list.xml', $this->og_admin_home.'/application/config/external_system_list.xml');
    		$summary['link:core-lib/application/config/operator_settings.xml']=$this->safeCreateLink($this->core_lib_home.'/application/config/operator_settings.xml', $this->og_admin_home.'/application/config/operator_settings.xml');
    		$summary['link:core-lib/application/config/permissions.json']=$this->safeCreateLink($this->core_lib_home.'/application/config/permissions.json', $this->og_admin_home.'/application/config/permissions.json');
    		$summary['link:core-lib/application/config/standard_roles.json']=$this->safeCreateLink($this->core_lib_home.'/application/config/standard_roles.json', $this->og_admin_home.'/application/config/standard_roles.json');
    		$summary['link:core-lib/application/config/migration.php']=$this->safeCreateLink($this->core_lib_home.'/application/config/migration.php', $this->og_admin_home.'/application/config/migration.php');
    		$summary['link:core-lib/application/libraries/vendor']=$this->safeCreateLink($this->core_lib_home.'/application/config/system_feature.php', $this->og_admin_home.'/application/config/system_feature.php');
    		//payment
    		$this->safeCreateLink($this->payment_lib_home.'/payment', $this->og_admin_home.'/application/libraries/payment');
    		//game
    		$this->safeCreateLink($this->game_lib_home.'/game_platform', $this->og_admin_home.'/application/libraries/game_platform');
    		$this->safeCreateLink($this->game_lib_home.'/models/game_description', $this->og_admin_home.'/application/models/game_description');

		  	unlink($this->core_lib_home.'/application/models/base_model.php');
		  	unlink($this->game_lib_home.'/models/base_model.php');
		  	//logs
    		$this->safeCreateLink($this->og_admin_home.'/application/logs', $this->core_lib_home.'/application/logs');

    	}
    	//clean old file
    	unlink($this->project_home.'version');
    	//genereate host id
    	$hostId=$this->getHostId();
    	//var dir
    	$var='/var';
    	$var_game='/var/game_platform';
    	$tmp_clockwork='/tmp/clockwork';

    	$this->safeCreateDir($var.'/log/response_results', true);
    	$this->safeCreateDir($var_game.'/php', true);
    	$this->safeCreateDir($var_game.'/nginx', true);
    	$this->safeCreateDir($var_game.'/ag', true);
    	$this->safeCreateDir($var_game.'/entwine', true);
    	$this->safeCreateDir($var_game.'/imslots', true);
    	$this->safeCreateDir($var_game.'/impt', true);
    	$this->safeCreateDir($var_game.'/pragmaticplay', true);
    	$this->safeCreateDir($var_game.'/mg', true);

    	$this->safeCreateDir($tmp_clockwork, true);
    	//clean clockwork
    	$this->passthruBool('find '.$tmp_clockwork.' -mtime +2 -type f -exec rm -f {} \;');
    	//pub dir
    	$pub_dir=$this->project_home.'/../pub';
    	$this->safeCreateDir($pub_dir, true);
    	//reports dir
    	$reports_dir=$this->pub_dir.'/'.$hostId.'/reports';
    	$this->safeCreateDir($reports_dir, true);
    	//upload dir
    	$upload_dir=$this->pub_dir.'/'.$hostId.'/upload';
    	$this->safeCreateDir($upload_dir, true);
    	//all_version
    	$all_version_file=$this->og_admin_home.'/public/all_version';
    	unlink($all_version_file);
    	unlink($this->project_home.'/submodules/all_version');

    	$this->safeCreateDir($this->upload_dir.'/banner');
    	$this->safeCreateDir($this->upload_dir.'/notifications');
    	$this->safeCreateDir($this->upload_dir.'/themes');
    	$this->safeCreateDir($this->upload_dir.'/themes/kgvip/img');
    	$this->safeCreateDir($this->upload_dir.'/themes/lequ/img');

    	$this->safeCreateDir($this->upload_dir.'/shared_images/account');
    	$this->safeCreateDir($this->upload_dir.'/shared_images/banner');
    	$this->safeCreateDir($this->upload_dir.'/shared_images/depositslip');

    	$this->safeBackupDir($this->og_admin_home.'/public/resources/images/account');
    	$this->safeCreateLink($this->upload_dir.'/shared_images/account', $this->og_admin_home.'/public/resources/images/account');
    	$this->safeCreateLink($this->upload_dir.'/shared_images/account', $this->og_player_home.'/public/resources/images/account');
    	$this->safeCreateLink($this->upload_dir.'/shared_images/account', $this->og_aff_home.'/public/resources/images/account');
    	$this->safeBackupDir($this->og_admin_home.'/public/resources/images/banner');
    	$this->safeCreateLink($this->upload_dir.'/shared_images/banner', $this->og_admin_home.'/public/resources/images/banner');
    	$this->safeCreateLink($this->upload_dir.'/shared_images/banner', $this->og_player_home.'/public/resources/images/banner');
    	$this->safeCreateLink($this->upload_dir.'/shared_images/banner', $this->og_aff_home.'/public/resources/images/banner');
    	$this->safeBackupDir($this->og_admin_home.'/public/resources/images/depositslip');
    	$this->safeCreateLink($this->upload_dir.'/shared_images/depositslip', $this->og_admin_home.'/public/resources/images/depositslip');
    	$this->safeCreateLink($this->upload_dir.'/shared_images/depositslip', $this->og_player_home.'/public/resources/images/depositslip');
    	$this->safeCreateLink($this->upload_dir.'/shared_images/depositslip', $this->og_aff_home.'/public/resources/images/depositslip');
    	//upload
    	$this->safeCreateLink($this->upload_dir, $this->og_admin_home.'/public/upload');

    	return $success;
    }

    /**
     * backup dir
     * @param  string $dir
     * @return boolean
     */
    private function safeBackupDir($dir){
    	$success=true;
    	if(is_dir($dir)){
	    	$success=rename($dir, $dir.'_bak');
	    	$success=$this->passthruBool('cp -R ') && $success;
    	}

    	return $success;
    }

    /**
     * create dir
     * @param  string  $dir
     * @param  boolean $is_root
     * @return boolean
     */
    private function safeCreateDir($dir, $is_root=false){
    	$success = false;
    	if($is_root){
    		$success=$this->passthruBool('sudo mkdir -p '.$dir);
    		$success = $this->passthruBool('sudo chmod 777 '.$dir) && $success;
    	}else{
    		$success=mdkir($dir, 0777, true);
    		$success = chmod($dir, 0777) && $success;
    	}

    	return $success;
    }

    private function getHostId(){
    	$hostname=gethostname();
    	$hostid=$hostname;

    	if(strpos($hostname, '-')!==false){
    		$arr=explode($hostname, '-');
    		if(count($arr)>=2){
    			$hostid=$hostname[0].'-'.$hostname[1];
    		}
    	}elseif(strpos($hostname, '_')!==false){
    		$arr=explode($hostname, '_');
    		if(count($arr)>=2){
    			$hostid=$hostname[0].'_'.$hostname[1];
    		}
    	}

    	if(isset($argv[1]) && $argv[1]=='BUILD_IMAGE'){
    		$hostid='';
    	}

    	$this->debug_log($hostname.' to '.$hostid);

    	return $hostid;

    }

    /**
     * delete and create link
     * @param  string $sourceDir
     * @param  string $target
     * @return boolean
     */
    private function safeCreateLink($sourceDir, $target){
		if(is_dir($target)){
			$this->passthruBool('rm -rf '.$target);
		}else{
			unlink($target);
		}

		return $this->passthruBool('ln -sf '.$sourceDir.' '.$target);
    }

    /**
     * exec comamnd on pad
     * @param  string $cmd
     * @param  string $podName
     * @return boolean
     */
	private function execOnPod($cmd, $podName){
		$script='kubectl exec '.$podName.' '.$cmd;
		$success=$this->passthruBool($script);

		return $success;
	}

	/**
	 *
	 * copyDirToPod
	 *
	 * @param string $dir     source
	 * @param string $podName target pod
	 * @param string $targetDir target dir
	 * @return bool
	 */
	private function copyDirToPod($dir, $podName, $targetDir){
		$success=false;
		if(is_dir($dir) && !empty($podName)){

            //kubectl exec jwstaging-og-7c96db959c-tlq65 -- bash -c "mkdir -p /tmp/234234"
            //kubectl cp /Users/magicgod/magicgod/Code/core-lib jwstaging-og-7c96db959c-tlq65:/tmp/234234/core-lib
            //kubectl exec jwstaging-og-7c96db959c-tlq65 -- bash -c "cd /tmp/234234 && chown -R vagrant:vagrant core-lib && rm -rf ./core-lib/.git* && cp -R core-lib/* /home/vagrant/Code/og/submodules/core-lib/"

            $arr=explode('/', $dir);
            $lastDirName=$arr[count($arr)-1];

            // $localTarFile='/tmp/'.$podName.'-'.$dir.'.tar.gz';
            // //tar local
            // $tarGz=new PharData($localTarFile);
            $tmpName=rand(100000,999999);

            $script='kubectl exec '.$podName.' -- bash -c "mkdir -p /tmp/'.$tmpName.'"';
			$success=$this->passthruBool($script);

            $script='kubectl cp '.$dir.' '.$podName.':/tmp/'.$tmpName.'/'.$lastDirName;
			$success=$this->passthruBool($script);

            $script='kubectl exec '.$podName.' -- bash -c "cd /tmp/'.$tmpName.' && chown -R vagrant:vagrant '.$lastDirName.' && rm -rf ./'.$lastDirName.'/.git* && cp -R '.$lastDirName.'/* /home/vagrant/Code/og/submodules/core-lib"';
			$success=$this->passthruBool($script);

			// $script='kubectl cp '.$dir.' '.$podName.':'.$targetDir;
			// $success=$this->passthruBool($script);
		}else{
            $this->error_log('wrong dir or pod name', $dir, $podName);
        }

		return $success;
	}

	// private function clearCoreLinks(){
	// 	$link=$this->project_home.'/submodules/core-lib/application/logs';
	// 	if(file_exists($link)){
	// 		unlink($link);
	// 	}

	// 	return true;

	// }

	/**
	 *
	 * copyDirToPod
	 *
	 * @param string $dir     source
	 * @param string $podName target pod
	 * @param string $targetDir target dir
	 * @return bool
	 */
	// private function copyDirToPod($dir, $podName, $targetDir){
	// 	$success=false;
	// 	if(is_dir($dir) && !empty($podName)){
	// 		$script='kubectl cp '.$dir.'/ '.$podName.':'.$targetDir;
	// 		$success=$this->passthruBool($script);
	// 	}

	// 	return $success;
	// }

	/**
	 *
	 * search pod
	 * @param  string  $podNameMatch search pod name
	 * @param  boolean $returnAll    return all matched or only first one
	 * @return mixin one name ($returnAll==false) or name list ($returnAll==true)
	 *
	 */
	private function searchPod($podNameMatch, $returnAll=false){

		$first_pod_name=null;
		$pod_list=[];

		$script="kubectl get pods";
		$rlt=$this->execCmd($script);
		if(!empty($rlt) && !empty($rlt[0])){
			$arr=explode("\n", $rlt[0]);
			if(!empty($arr)){
				foreach ($arr as $line) {
					if(!empty($line)){
						$fldArr=explode(' ', $line);
						if(!empty($fldArr) && !empty($fldArr[0])){
							$pod_name=$fldArr[0];
							$this->debug_log('get pod: '.$pod_name, $podNameMatch);
							//start with
							if(strpos($pod_name, $podNameMatch)===0){

								$this->debug_log('found client pod: '.$this->client_name);

								// $found=true;
								if(!$returnAll){
									$first_pod_name=$pod_name;
									break;
								}else{
									$pod_list[]=$pod_name;
								}
							}
						}
					}
				}
			}
		}

		if($returnAll){
			return $pod_list;
		}else{
			return $first_pod_name;
		}

	}

	private function isProtectedMainBranch($branchName){
		$white_list=['live_stable_prod', 'live_stable_rc', 'live_stable'];

		return in_array($branchName, $white_list);

	}

	private function findMasterBranchName($repo, $mainBranchName){

		if($this->isProtectedMainBranch($mainBranchName)){
			$this->info_log('ignore main branch', $mainBranchName);
			return null;
		}

		$master_branch_name=$this->convertNameToMaster($mainBranchName);

		$repo->fetch('origin', ['-p']);
		// $repoCoreLib->getCurrentBranchName();

		$all_branches=$repo->getBranches();

		$exists_remote_branch=false;
		//search remote branches
		if(!empty($all_branches)){
			foreach ($all_branches as $br) {
				if($this->startsWith($br, 'remotes')){
					//remove remotes/origin/
					$remote_br=substr($br, strlen('remotes/origin/'));
					$this->debug_log('remote branch', $remote_br);
					if($remote_br==$master_branch_name){
						$exists_remote_branch=true;
					}
				}
			}
		}
		$this->debug_log('all branch', $master_branch_name, 'exists_remote_branch', $exists_remote_branch);
		if(!$exists_remote_branch){
			//default is master
			$master_branch_name=null;
		}

		if(!empty($master_branch_name)){
			//try sync it
			$repo->checkout($master_branch_name);
			$repo->pull('origin', [$master_branch_name]);
			//try pull
			$this->debug_log('try pull core lib origin '.$master_branch_name);
		}else{
			$this->debug_log('ignore pull');
		}

		return $master_branch_name;
	}

	private function convertNameToMaster($branchName){
		if(!empty($branchName)){
			$prefix='';
			if($this->startsWith($branchName, 'live_stable_prod')){
				$prefix='live_stable_prod';
			}else if($this->startsWith($branchName, 'live_stable_rc')){
				$prefix='live_stable_rc';
			}else if($this->startsWith($branchName, 'live_stable')){
				$prefix='live_stable';
			}

			if(!empty($prefix)){
				$branchName='master'.substr($branchName,strlen($prefix),strlen($branchName));
			}
		}

		return $branchName;
	}

	private function setClassVarFromArg($argNames){
		if(!empty($argNames)){
			foreach ($argNames as $argName) {
				$this->$argName=$this->climate->arguments->get($argName);
			}
		}
	}

	private function startsWith($str, $prefix){
		return substr($str, 0, strlen($prefix))==$prefix;
	}

	private function showWarning($msg){
		$this->climate->lightRed($msg);
	}

	private function processScriptToArray($script){
		$script=str_replace("\r\n","\r", $script);
		return explode("\r", $script);
	}

	private function passthruBool($script){
		return $this->passthru($script)==0;
	}

	private function passthru($script){

		if($this->is_debug){
			$this->debug_log($script);
		}
		$rlt_var=0;
		passthru($script, $rlt_var);
		return $rlt_var;
	}

	private function execCmd($script, $print=false, $return=true, $force_array=true){

		if(is_string($script) && $force_array){
			$script=$this->processScriptToArray($script);
		}

		if(!is_array($script)){
			$script=[$script];
		}

		$str=[];
		foreach ($script as $s) {
			if(empty(trim($s)) || substr($s, 0, 1)=='#'){
				continue;
			}

			// $rlt=shell_exec($s);
			exec($s, $rlt, $rlt_var);

			if($rlt_var!=0){
				$this->error_log('run command error', $s, $rlt_var, $rlt);
				return false;
			}

			if($print){
				if($this->is_debug){
					$this->debug_log($s);
				}
				$this->debug_log($rlt);
			}

			if($return && !empty($rlt)){
				$str[]=implode("\n", $rlt);
			}else{
				unset($rlt);
			}

		}

		if($return){
			return $str;
		}
	}

	public function info_log() {
		$args = func_get_args();

		if (count($args) <= 0) {
			return '';
		}

		$msg = $this->buildDebugMessage($args, 'INFO');

		$this->climate->lightYellow($msg);

		return $msg;
	}

	public function error_log() {
		$args = func_get_args();

		if (count($args) <= 0) {
			return '';
		}

		$msg = $this->buildDebugMessage($args, 'ERROR');

		$this->climate->red($msg);

		return $msg;
	}

	public function debug_log() {

		$args = func_get_args();

		if (count($args) <= 0) {
			return '';
		}

		$msg = $this->buildDebugMessage($args, 'DEBUG');

		$this->climate->darkGray($msg);

		return $msg;
	}

	private function buildDebugMessage($args, $title = 'CLI', $addHeader = true) {

		$msg = '';

		if ($addHeader) {

			$msg .= "[" . $title . "] [";
			if (!empty($subtitle)) {
				$msg = $msg . $subtitle . '] [';
			}
		}
		foreach ($args as $key => $value) {
			$str = $this->formatDebugMessage($value);

			if(is_numeric($key)){
				$msg .= "'" . $str . "' ";
			}else{
				$msg .= $key . ": " . $str . ", ";
			}
		}

		if ($addHeader) {
			$msg .= ' ]';
		}

		return $msg;
	}

	private function formatDebugMessage($value) {

		if (is_object($value)) {
			if ($value instanceof \DateTime) {
				//print date time
				$str = $value->format(\DateTime::ISO8601);
			} else if ($value instanceof \SimpleXMLElement) {
				$str = $value->asXML();
			} else if (method_exists($value, '__toString')) {
				$str = $value->__toString();
			} else if (method_exists($value, 'toString')) {
				$str = $value->toString();
			} else if (method_exists($value, 'toJson')) {
				$str = json_encode($value->toJson(), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
			} else {
				$str = json_encode((array) $value, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
			}
		} else if (is_array($value)) {
			$str = json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
		} else if (is_null($value)) {
			$str = '(NULL)';
		} else if (is_bool($value)) {
			$str = $value ? 'true' : 'false';
		} else {
			$str = $value;
		}

		return $str;
	}

}


$cli=new Cli();

if(!$cli->run()){
	exit(1);
}else{
	exit(0);
}
