<?php namespace App\Http\Controllers;

use App\DBManager;
use App\Email as Email;
use app\EmailConfiguration;
use app\Exceptions\ConfigurationException;
use app\Exceptions\EmailException;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\PDOIterator;
use app\TemplateConfiguration;
use Symfony\Component\Config\Definition\Exception\Exception;
use Illuminate\Http\Request;
use App\User;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;

class PhishingController extends Controller {

	public function index()	{
		return view('displays.displayHome');
	}

	/**
	 * webbugRedirect
	 * Handles when webbugs get called. If request URI contains the word 'email', executes email webbug otherwise executes website webbug
	 *
	 * @param 	string		$id		Contains UniqueURLId that references specific user and specific project ID
	 */
	public function webbugRedirect($id) {
		$urlId = substr($id,0,15);
		$projectId = substr($id,15,16);
		try {
			$db = new DBManager();
			$sql = "SELECT USR_Username FROM gaig_users.users WHERE USR_UniqueURLId=?;";
			$bindings = array($urlId);
			$result = $db->query($sql,$bindings);
			$result = $result->fetch(\PDO::FETCH_ASSOC);
			$username = $result[0]['USR_Username'];
			if(strpos($_SERVER['REQUEST_URI'],'email') !== false) {
				$this->webbugExecutionEmail($username,$projectId);
			} else {
				$this->webbugExecutionWebsite($username,$projectId);
			}
		} catch(Exception $e) {
			//caught exception in webbug already logged
            //retry? otherwise do nothing
		}

	}

	/**
	 * webbugExecutionEmail
	 * Email specific execution of the webbug tracker.
	 *
	 * @param 	string		$username			Username of user passed from webbugRedirect
	 * @param 	string		$projectId			Project ID to create a filter choice in the results
	 */
	private function webbugExecutionEmail($username,$projectId) {
		$sql = "INSERT INTO gaig_users.email_tracking (EML_Id,EML_Ip,EML_Host,EML_Username,EML_ProjectName,
					EML_AccessTimestamp) VALUES (null,?,?,?,?,?);";
		$this->webbugRootExecution($projectId,$sql,$username);
	}

	/**
	 * webbugExecutionWebsite
	 * Website specific execution of the webbug tracker.
	 *
	 * @param 	string		$username			Username of user passed from webbugRedirect
	 * @param 	string		$projectId			Project ID to create a filter choice in the results
	 */
	private function webbugExecutionWebsite($username,$projectId) {
		$sql = "INSERT INTO gaig_users.website_tracking (WBS_Id,WBS_Ip,WBS_Host,
					WBS_BrowserAgent,WBS_ReqPath,WBS_Username,WBS_ProjectName,WBS_AccessTimestamp) 
					VALUES (null,?,?,?,?,?,?,?);";
		$this->webbugRootExecution($projectId,$sql,$username);
	}

	/**
	 * webbugRootExecution
	 * Common values for webbug execution. Returns array of values to calling method.
	 *
	 * @param	string		$parentSql			SQL to be executed based on whether the parent is Email or Website
	 * @param	string		$username			Username of the user to be used in binding of statement
	 * @return 	array|null						Returns null if IP is hidden or not given, otherwise gives needed input
	 */
	private function webbugRootExecution($projectId,$parentSql,$username) {
		if(!empty($_SERVER['REMOTE_ADDR'])) {
			try {
				$db = new DBManager();
				$ip = $_SERVER['REMOTE_ADDR'];
				$host = gethostbyaddr($_SERVER['REMOTE_ADDR']);
				$sql = "SELECT PRJ_ProjectName FROM gaig_users.projects WHERE PRJ_ProjectId=?;";
				$bindings = array($projectId);
				$result = $db->query($sql,$bindings);
				$result = $result->fetch(\PDO::FETCH_ASSOC);
				$projectName = $result[0]['PRJ_ProjectName'];
				$timestamp = date("Y-m-d H:i:s");
				$parentBindings = array($ip,$host,$username,$projectName,$timestamp);
				$db->query($parentSql,$parentBindings);
			} catch(Exception $e) {
                //caught exception in webbug already logged
                //retry? otherwise do nothing
            }
		}
	}

	public function create() {
		return redirect()->to('/breachReset');
	}

	public function breachReset() {
		return view("passwordReset.resetPage1");
	}

	public function breachVerify() {
		return view("passwordReset.resetPage2");
	}

	public function store()
	{
		return redirect()->to('/breachReset/verifyUser');
	}

	public function edit($id)
	{
		//
	}

	public function update($id)
	{
		//
	}

	public function destroy($id)
	{
		//
	}

	/**
	 * sendEmail
	 * Function mapped to Laravel route. Defines variable arrays and calls Email Class executeEmail.
	 *
	 * @param 	Request 		$request			Request object passed via AJAX from client.
	 */
	public function sendEmail(Request $request) {
		try {
			$templateConfig = new TemplateConfiguration(
				array(
					'templateName'=>$request['emailTemplate'],
					'companyName'=>$request['companyName'],
					'projectName'=>$request['projectData']['projectName'],
					'projectId'=>intval($request['projectData']['projectId'])
				)
			);

            $periodInWeeks = 4;
            $users = array();
			$emailConfig = new EmailConfiguration(
				array(
					'host'=>$request['hostName'],
					'port'=>$request['port'],
					'authUsername'=>$request['username'],
					'authPassword'=>$request['password'],
					'fromEmail'=>$request['fromEmail'],
					'subject'=>$request['subject'],
                    'users'=>$templateConfig->getValidUsers($users,$periodInWeeks)
				)
			);

			Email::executeEmail($emailConfig,$templateConfig);
		} catch(ConfigurationException $ce) {
		    //will be doing something here - what still has yet to be defined (likely just log the exception)
		} catch(EmailException $ee) {
            //will be doing something here - what still has yet to be defined (likely just log the exception)
		}
	}

    /**
     * generateEmailForm
     * Generates the Send Email Request Form in the GUI.
     *
     * @return  \Illuminate\View\View
     */
	public function generateEmailForm() {
		if($this->isUserAuth()) {
			try {
				$db = new DBManager();
				$sql = "SELECT DFT_MailServer,DFT_MailPort,DFT_Username,DFT_CompanyName FROM gaig_users.default_emailsettings
				WHERE DFT_UserId=?;";
				$bindings = array(\Session::get('authUserId'));
				$result = $db->query($sql,$bindings);
				$result = $result->fetch(\PDO::FETCH_ASSOC);
				$dft_host = $result['DFT_MailServer'];
				$dft_port = $result['DFT_MailPort'];
				$dft_user = $result['DFT_Username'];
				$dft_company = $result['DFT_CompanyName'];
				$projects = $this->returnAllProjects();
				$templates = $this->returnAllTemplates();
				$varToPass = array('projectSize'=>$projects[0],'data'=>$projects[1],'templateSize'=>$templates[0],'fileNames'=>$templates[1],
					'dft_host'=>$dft_host,'dft_port'=>$dft_port,'dft_user'=>$dft_user,'dft_company'=>$dft_company);
				return view('forms.emailRequirements')->with($varToPass);
			} catch(Exception $e) {
                //caught exception already logged
                //retry? otherwise redirect to user-friendly error view
            }
		} else {
			//not authenticated redirect
			\Session::put('loginRedirect',$_SERVER['REQUEST_URI']);
			return view('auth.loginTest'); //refactor to remove Test
		}
	}

    /**
     * viewAllProjects
     * Returns list of all project names for view in GUI.
     *
     * @return  \Illuminate\View\View
     */
	public function viewAllProjects() {
		if($this->isUserAuth()) {
			$projects = $this->returnAllProjects();
			if(!is_null($projects)) {
				$varToPass = array('projectSize'=>$projects[0],'data'=>$projects[1]);
				return view('displays.showAllProjects')->with($varToPass);
			}
			//retry? otherwise redirect to user-friendly error view
		} else {
			\Session::put('loginRedirect',$_SERVER['REQUEST_URI']);
			return view('auth.loginTest');
		}
	}

    /**
     * returnAllProjects
     * Helper function that queries project info from database.
     *
     * @return  array
     */
	private function returnAllProjects() {
		try {
			$db = new DBManager();
			$sql = "SELECT PRJ_ProjectId, PRJ_ProjectName, PRJ_ProjectStatus FROM gaig_users.projects;";
			$bindings = array();
			$projects = $db->query($sql,$bindings);
			$projectIterator = new PDOIterator($projects);
			$data = array();
			$projectSize = 0;
			foreach($projectIterator as $project) {
				$data[] = array(
				    'PRJ_ProjectId'=>$project['PRJ_ProjectId'],
                    'PRJ_ProjectName'=>$project['PRJ_ProjectName'],
                    'PRJ_ProjectStatus'=>$project['PRJ_ProjectStatus']);
				$projectSize++;
			}
			return array($projectSize,$data);
		} catch(Exception $e) {
            return null;
        }
	}

    /**
     * viewAllTemplates
     * Returns list of all template names for view in GUI.
     *
     * @return  \Illuminate\View\View
     */
	public function viewAllTemplates() {
		if($this->isUserAuth()) {
			$templates = $this->returnAllTemplates();
			for($i = 0; $i < $templates[0]; $i++) {
				$filePrefaces[$i] = substr($templates[1][$i],0,3);
				$fileTypes[$i] = substr($templates[1][$i],3,1);
				if($fileTypes[$i] == 'T') {
					$fileTypes[$i] = 'tar';
				} else if($fileTypes[$i] == 'G') {
					$fileTypes[$i] = 'gen';
				} else {
					$fileTypes[$i] = 'edu';
				}
			}
			$varToPass = array('templateSize'=>$templates[0],'fileNames'=>$templates[1],'filePrefaces'=>$filePrefaces,'fileTypes'=>$fileTypes);
			return view('displays.showAllTemplates')->with($varToPass);
		} else {
			\Session::put('loginRedirect',$_SERVER['REQUEST_URI']);
			return view('auth.loginTest');
		}
	}

    /**
     * returnAllTemplates
     * Helper function that queries template names from file structure.
     *
     * @return  array
     */
	private function returnAllTemplates() {
		$files = [];
		$fileNames = [];
		$filesInFolder = \File::files('../resources/views/emails/phishing');
		foreach($filesInFolder as $path) {
			$files[] = pathinfo($path);
		}
		$templateSize = sizeof($files);
		for($i = 0; $i < $templateSize; $i++) {
			$fileNames[$i] = $files[$i]['filename'];
			$fileNames[$i] = substr($fileNames[$i],0,-6);
		}
		return array($templateSize,$fileNames);
	}

    /**
     * createNewProject
     * Creates new project and inserts into database.
     *
     * @param   Request         $request        Data sent by user to instantiate new project
     */
	public function createNewProject(Request $request) {
		try {
			$db = new DBManager();
			$sql = "INSERT INTO gaig_users.projects (PRJ_ProjectId,PRJ_ProjectName,PRJ_ComplexityType,PRJ_TargetType,
            PRJ_ProjectAssignee,PRJ_ProjectStart,PRJ_ProjectLastActive,PRJ_ProjectStatus,PRJ_ProjectTotalUsers,
            PRJ_EmailViews,PRJ_WebsiteViews,PRJ_ProjectTotalReports) VALUES (null,?,?,?,?,?,?,'Inactive',0,0,0,0);";

			$projectName = $request->input('projectNameText');
			$projectAssignee = $request->input('projectAssigneeText');
            $complexityType = $request->input('projectComplexityType');
            $targetType = $request->input('projectTargetType');
			$date = date("Y-m-d");
			$bindings = array($projectName,$complexityType,$targetType,$projectAssignee,$date,$date);

			$db->query($sql,$bindings);
		} catch(Exception $e) {
            //caught exception already logged
            //retry? otherwise redirect to user-friendly error view
        }
	}

    /**
     * createNewTemplate
     * Creates new template and writes it to the file structure.
     *
     * @param   Request         $request        Template name and content sent by user to create new template
     */
	public function createNewTemplate(Request $request) {
		$path = '../resources/views/emails/';
		$templateName = $request->input('templateName');
		$path = $path . $templateName . '.blade.php';
		$templateContent = $request->input('templateContent');
		\File::put($path,$templateContent);
		\File::delete('../resources/views/emails/.blade.php');
	}

    /**
     * htmlReturner
     * Takes template name as input and returns content of template to be used as a preview in the GUI.
     *
     * @param   string      $id             Template Name
     * @return  string                      Template Content
     */
	public function htmlReturner($id) {
		$path = '../resources/views/emails/phishing/' . $id . '.blade.php';
		$contents = '';
		try {
			$contents = \File::get($path);
		}
		catch (FileNotFoundException $fnfe) { //change to generic exception
		    //log exception
			$contents = "Preview Unavailable";
		}
		return $contents;
	}

    /**
     * updateDefaultEmailSettings
     * Post function for updating Default Email Settings, which are used to autofill the Send Email Request Form
     *
     * @param   Request         $request            Settings sent from form
     */
	public function updateDefaultEmailSettings(Request $request) {
		try {
			$db = new DBManager();

            //modify to use object access instead of array access
			$username = $request['usernameText'];
			$company = $request['companyText'];
			$host = $request['mailServerText'];
			$port = $request['mailPortText'];
			$userId = \Session::get('authUserId');

			$settingsExist = $this->queryDefaultEmailSettings($userId);

			if($settingsExist->fetchColumn() > 0) {
				$sql = "UPDATE gaig_users.default_emailsettings SET DFT_MailServer=?,DFT_MailPort=?,DFT_Username=?, 
							DFT_CompanyName=? WHERE DFT_UserId=?;";
				$bindings = array($host,$port,$username,$company,$userId);
				$db->query($sql,$bindings);
			} else {
				$sql = "INSERT INTO gaig_users.default_emailsettings (DFT_UserId, DFT_MailServer, DFT_MailPort,
					DFT_Username, DFT_CompanyName) VALUES (?,?,?,?,?);";
				$bindings = array($userId,$host,$port,$username,$company);
				$db->query($sql,$bindings);
			}
			//return something back to ajax
		} catch(Exception $e) {
            //caught exception helper function already logged
            //retry? otherwise do nothing
        }
	}

    /**
     * generateDefaultEmailSettingsForm
     * Generates the Settings form based on input from the database.
     *
     * @return  \Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
	public function generateDefaultEmailSettingsForm() {
		if($this->isUserAuth()) {
			try {
				$settingsExist = $this->queryDefaultEmailSettings(\Session::get('authUserId'));
				if($result = $settingsExist->fetch(\PDO::FETCH_ASSOC)) {
					$dft_host = $result['DFT_MailServer'];
					$dft_port = $result['DFT_MailPort'];
					$dft_user = $result['DFT_Username'];
					$dft_company = $result['DFT_CompanyName'];
				} else {
					$dft_host = '';
					$dft_port = '';
					$dft_company = '';
					$dft_user = '';
				}
				$varToPass = array('dft_host'=>$dft_host,'dft_port'=>$dft_port,'dft_user'=>$dft_user,'dft_company'=>$dft_company);
				return view('forms.defaultEmailSettings')->with($varToPass);
			} catch(Exception $e) {
                //caught exception already logged
                //retry? otherwise redirect to user-friendly error view
            }
		} else {
			//not authenticated redirect
			\Session::put('loginRedirect',$_SERVER['REQUEST_URI']);
			return redirect()->to('/auth/login');
		}
	}

    /**
     * queryDefaultEmailSettings
     * Helper function to query database for settings.
     *
     * @param   int             $userId         User ID to query for settings.
     * @return  \PDOStatement
     */
	private function queryDefaultEmailSettings($userId) {
		$db = new DBManager();

		$sql = "SELECT COUNT(*) FROM gaig_users.default_emailsettings WHERE DFT_UserId=?;";
		$bindings = array($userId);
		$settingsExist = $db->query($sql,$bindings);
		return $settingsExist;
	}

    /**
     * postLogin
     * Verifies the provided user exists and their password is accurate. Saves authenticated user to session variable.
     *
     * @param   Request         $request            Username and password
     * @return  \Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
	public function postLogin(Request $request) {
		try {
			$db = new DBManager();
			$username = $request['usernameText'];
			$password = $request['passwordText'];

			$sql = "SELECT USR_Password,USR_UserId FROM gaig_users.users WHERE USR_Username=?;";
			$bindings = array($username);
			$result = $db->query($sql,$bindings);

			if($result = $result->fetch(\PDO::FETCH_ASSOC)) {
				if(password_verify($password,$result['USR_Password'])) {
					\Session::put('authUser',$username);
					\Session::put('authUserId',$result['USR_UserId']);
					\Session::put('authIp',$_SERVER['REMOTE_ADDR']);

					$redirectPage = \Session::get('loginRedirect');
					if($redirectPage) {
						return redirect()->to($redirectPage);
					} else {
						return view('errors.500');
					}
				} else {
					$varToPass = array('errors'=>array('The password provided does not match our records.'));
					return view('auth.loginTest')->with($varToPass);
				}
			} else {
				$varToPass = array('errors'=>array("We failed to find the username provided. Check your spelling and try 
				again. If this problem continues, contact your manager."));
				return view('auth.loginTest')->with($varToPass);
			}
		} catch(Exception $e) {
            //caught exception already logged
            //retry? otherwise redirect to user-friendly error view
        }
	}

	//disabled until add manager registration of users
    /**
     * postRegister
     * Registers a new user to the database and authenticates them.
     *
     * @param   Request         $request            Username, password, and name
     */
	public function postRegister(Request $request) {
		try {
			$db = new DBManager();
			$username = $request['usernameText'];
			$password = $request['passwordText'];
			$firstName = $request['firstNameText'];
			$lastName = $request['lastNameText'];
			$password = password_hash($password,PASSWORD_DEFAULT);
			$sql = "INSERT INTO gaig_users.users (USR_UserId,USR_Username,USR_FirstName,USR_LastName,
				USR_UniqueURLId,USR_Password,USR_ProjectMostRecent,USR_ProjectPrevious,USR_ProjectLast) VALUES
				(null,?,?,?,null,?,null,null,null);";
			$bindings = array($username,$firstName,$lastName,$password);
			$db->query($sql,$bindings);

			$sql = "SELECT USR_UserId FROM gaig_users.users WHERE USR_Username=?;";
			$bindings = array($username);
			$result = $db->query($sql,$bindings);
			$result = $result->fetch(\PDO::FETCH_ASSOC);

			\Session::put('authUser',$username);
			\Session::put('authUserId',$result['USR_UserId']);
			\Session::put('authIp',$_SERVER['REMOTE_ADDR']);
		} catch(Exception $e) {
            //caught exception already logged
            //retry? otherwise redirect to user-friendly error view
        }
	}

    /**
     * changePassword - IN DEVELOPMENT PROGRESS
     * Allows authenticated users to change their password. Requires re-entering password to verify user.
     *
     * @param   Request         $request            Password
     */
	public function changePassword(Request $request) {
        if($this->isUserAuth()) {
            try {
                $db = new DBManager();
                $passwordOld = $request['passwordOldText'];
                $username = \Session::get('authUser');

                $sql = "SELECT USR_Password,USR_UserId FROM gaig_users.users WHERE USR_Username=?;";
                $bindings = array($username);
                $result = $db->query($sql,$bindings);

                if($result = $result->fetch(\PDO::FETCH_ASSOC)) {
                    if(password_verify($passwordOld,$result['USR_Password'])) {
                        $passwordNew = password_hash($request['passwordNewText'],PASSWORD_DEFAULT);

                        $sql = "UPDATE gaig_users.users SET USR_Password=? WHERE USR_Username=?;";
                        $bindings = array($passwordNew,$username);
                        $db->query($sql,$bindings);
                    } else {
                        $varToPass = array('errors'=>array('The password provided does not match our records.'));
                        //return view('auth.loginTest')->with($varToPass);
                    }
                }
            } catch(Exception $e) {

            }
        }
    }

    /**
     * logout
     * Removes session variables identifying an authenticated user.
     *
     * @return  \Illuminate\Http\RedirectResponse
     */
	public function logout() {
		\Session::forget('authUser');
		\Session::forget('authUserId');
		\Session::forget('loginRedirect');
		\Session::forget('authIp');
		return redirect()->to('http://localhost:8888');
	}

    /**
     * postWebsiteJson
     * Posts data queried from website_tracking table. Requires authenticated user to execute data retrieval.
     *
     * @return  array|\Illuminate\View\View
     */
	public function postWebsiteJson() {
		if($this->isUserAuth()) {
			try {
				$db = new DBManager();
				$sql = "SELECT WBS_Ip,WBS_Host,WBS_ReqPath,WBS_Username,WBS_ProjectName,WBS_AccessTimestamp 
						FROM gaig_users.website_tracking;";
				$json = $db->query($sql,array());
				$jsonIterator = new PDOIterator($json);
				$websiteData = array();
				foreach($jsonIterator as $data) {
					$websiteData[] = array('WBS_Ip'=>$data[0],'WBS_Host'=>$data[1],
						'WBS_ReqPath'=>$data[2],'WBS_Username'=>$data[3],
						'WBS_ProjectName'=>$data[4],'WBS_AccessTimestamp'=>$data[5]);
				}
				return $websiteData;
			} catch(Exception $e) {
                //caught exception already logged
                //retry? otherwise redirect to error view
            }
		}
		return view('errors.401');
	}

    /**
     * postEmailJson
     * Posts data queried from email_tracking table. Requires authenticated user to execute data retrieval.
     *
     * @return  array|\Illuminate\View\View
     */
	public function postEmailJson() {
		if($this->isUserAuth()) {
			try {
				$db = new DBManager();
				$sql = "SELECT EML_Ip,EML_Host,EML_Username,EML_ProjectName,EML_AccessTimestamp
 						FROM gaig_users.email_tracking;";
				$json = $db->query($sql,array());
				$jsonIterator = new PDOIterator($json);
				$emailData = array();
				foreach($jsonIterator as $data) {
					$emailData[] = array('EML_Ip'=>$data[0],'EML_Host'=>$data[1],
						'EML_Username'=>$data[2],'EML_ProjectName'=>$data[3],'WBS_AccessTimestamp'=>$data[4]);
				}
				return $emailData;
			} catch(Exception $e) {
                //caught exception already logged
                //retry? otherwise redirect to error view
            }
		}
		return view('errors.401');
	}

    /**
     * postReportsJson
     * Posts data queried from website_tracking table. Requires authenticated user to execute data retrieval.
     *
     * @return  array|\Illuminate\View\View
     */
	public function postReportsJson() {
		if($this->isUserAuth()) {
			try {
				$db = new DBManager();
				$sql = "SELECT EML_Ip,EML_Host,EML_Username,EML_ProjectName,EML_AccessTimestamp
 						FROM gaig_users.email_tracking;";
				$json = $db->query($sql,array());
				$jsonIterator = new PDOIterator($json);
				$reportData = array();
				foreach($jsonIterator as $data) {
					$reportData[] = array('RPT_EmailSubject'=>$data[0],'RPT_UserEmail'=>$data[1],
						'RPT_OriginalFrom'=>$data[2],'RPT_ReportDate'=>$data[3]);
				}
				return $reportData;
			} catch(Exception $e) {
                //caught exception already logged
                //retry? otherwise redirect to error view
            }
		}
		return view('errors.401');
	}

    /**
     * isUserAuth
     * Helper function which checks if the authUserId session variable is set and if the authIp session variable equals the IP of the requester.
     *
     * @return  bool
     */
	private function isUserAuth() {
		return \Session::get('authUserId') && \Session::get('authIp') == $_SERVER['REMOTE_ADDR'];
	}
}
