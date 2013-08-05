<?php
/**
 * アカウント新規編集削除処理で使用
 *
 */
require_once(DIR_APP . "/class/common/dbaccess/Member.php");
require_once(DIR_APP . "/class/common/dbaccess/Client.php");
require_once(DIR_APP . "/class/common/dbaccess/Project.php");
require_once(DIR_APP . "/class/common/dbaccess/ProjectTeam.php");
require_once(DIR_APP . "/class/scene/PostAndGetScene.php");
abstract class UserScene extends PostAndGetScene
{
	//生成しておく
	var	$_id;

	//DBオブジェクト生成
	var	$obj_member;
	var	$obj_client;
	var	$obj_project;
	var	$obj_project_team;

	//マスターデータ
	var	$view_list;
	var	$client_list;
	var	$project_list;
	var	$project_list_by_client_id;

	//アカウント情報閲覧権限
	var	$b_auth_account;

	//コンストラクタ
	function __construct() {
		$this->obj_member		= new Member;
		$this->obj_client		= new Client;
		$this->obj_project		= new Project;
		$this->obj_project_team	= new ProjectTeam;

	}

	//自分のアカウント編集が行えるかをチェック
	function checkAuthEditMyAccount($member_id)
	{
		if ($member_id == $_SESSION['manhour']['member']['id'])
		{
			if (!$_SESSION['manhour']['member']['auth']['open_user_edit_mine'])
			{
				MCWEB_Util::redirectAction('/index');
			}
		} else {
			if (!$_SESSION['manhour']['member']['auth']['open_user_edit'])
			{
				MCWEB_Util::redirectAction('/index');
			}
		}
	}
	
	//セッションから編集データを引用する
	function setProjectTeamSessionByMemberId($member_id)
	{
		$_SESSION['manhour']['tmp']['user_project_team_list']		= array();
		$project_team_data	= $this->obj_project_team->getDataByMemberId($member_id);
		foreach($project_team_data as $key => $project_team)
		{
			$_SESSION['manhour']['tmp']['user_project_team_list'][]	= array('project_id' => $project_team['project_id']);
		}
	}

	//セッションから編集データを引用する
	function setEditDataBySession()
	{
		if(isset($_SESSION['manhour']['tmp']['user_edit']))
		{
			$check_session = array(
				'id',
				'member_code',
				'name',
				'auth_lv',
				'post',
				'position',
				'mst_member_type_id',
				'mst_member_cost_id',
				'password',
				'password_change',
				'team_list_project_id',
			);
			foreach($check_session as $key => $value) {
				$this_param	= '_'. $value;
				if(empty($this->$this_param) && ($this->$this_param !== 0)) {
					if(isset($_SESSION['manhour']['tmp']['user_edit'][$value])) {
						$this->$this_param = $_SESSION['manhour']['tmp']['user_edit'][$value];
					}
				}
			}
		}
	}

	//全クライアントを取得してセット
	function setClientList() {
		if(!$this->client_list) {
			$this->client_list	= $this->obj_client->getDataAll();
		}
	}

	//全プロジェクトを取得してセット
	function setProjectList() {
		if(!$this->project_list) {
			$this->project_list	= $this->obj_project->getDataAllSortName();
		}
	}

	//DBに保存した所属プロジェクト一覧をセット
	function setProjectTeamViewListByProjectTeamTable($id) {

		$project_team_data	= $this->obj_project_team->getDataByMemberId($id);
		//一時処理用の配列を整形
		$project_team_list	= array();
		if(is_array($project_team_data))
		{
			$project_id_list	= array();
			foreach($project_team_data as $key => $project_team)
			{
				$project_id_list[]	= $project_team['project_id'];
			}
			$this->setProjectTeamViewListByProjectIdList($project_id_list);
		}
	}

	//セッションに保存した所属プロジェクト一覧をセット
	function setProjectTeamViewListBySession() {

		//一時処理用の配列を整形
		$project_team_list	= array();
		if(is_array($_SESSION['manhour']['tmp']['user_project_team_list']))
		{
			$project_id_list	= array();
			foreach($_SESSION['manhour']['tmp']['user_project_team_list'] as $key => $project_team)
			{
				$project_id_list[]	= $project_team['project_id'];
			}
			$this->setProjectTeamViewListByProjectIdList($project_id_list);
		}
	}

	//セッションに保存した所属プロジェクト一覧をセット
	function setProjectTeamViewListByProjectIdList($project_id_list) {
		$this->setClientList();
		$this->setProjectList();

		//一時処理用の配列を整形
		$project_team_list	= array();
		if(is_array($project_id_list))
		{
			foreach($project_id_list as $key => $project_id)
			{
				$project_team_list[$key]['client_name']		= '';
				$project_team_list[$key]['project_name']	= '';
				//プロジェクトIDからプロジェクト名を取得
				if(isset($this->project_list[$project_id]))
				{
					$project_team_list[$key]['project_name']		= $this->project_list[$project_id]['name'];
					$project_team_list[$key]['project_type']		= $this->project_list[$project_id]['project_type'];
					if(isset($this->client_list[$this->project_list[$project_id]['client_id']]))
					{
						$project_team_list[$key]['client_name']	= $this->client_list[$this->project_list[$project_id]['client_id']]['name'];
					}
				}
				$project_team_list[$key]['project_id']	=	$project_id;
				$project_team_list[$key]['key']			=	$key;
			}
		}
		$this->view_list = $project_team_list;
	}
}
?>