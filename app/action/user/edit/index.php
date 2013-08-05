<?php
/**
 * 「アカウント管理」- 編集画面
 */
require_once(DIR_APP . "/class/common/dbaccess/Post.php");
require_once(DIR_APP . "/class/common/dbaccess/Position.php");
require_once(DIR_APP . "/class/common/dbaccess/MemberType.php");
require_once(DIR_APP . "/class/common/dbaccess/MemberCost.php");
class _user_edit_index extends UserScene
{
	//外部からのエラー通知
	var $_error;
	//外部からの所属プロジェクト選択のエラー通知
	var $_team_error;
	//サブミットボタン
	var $_submit;
	//編集対象のメンバーID
	var	$_id;
	//社員コード
	var $_member_code;
	//社員名
	var $_name;
	//権限
	var $_auth_lv;
	//所属
	var $_post;
	//役職
	var $_position;
	//社員タイプ
	var $_mst_member_type_id;
	//社員コスト
	var $_mst_member_cost_id;
	//パスワード
	var $_password;
	//パスワードリセット有無
	var $_password_change;
	//所属プロジェクト選択のプロジェクトID
	var $_team_list_project_id;

	//出力用
	var $guide_message_member_code;		// 社員番号の入力制限説明
	var $array_auth_lv;
	var $array_post;
	var $array_position;
	var $array_member_type;
	var $array_member_cost;
	var $password_tmp;

	//初期チェック
	function check()
	{
		//受け取ったデータをセッションに保存しておく
		if(!is_array($_SESSION['manhour']['tmp']['user_edit'])) {
			MCWEB_Util::redirectAction("/user/index");
		}
		//この段階で、他のIDを指定されている場合は、編集開始アクションに戻る
		if(!empty($this->_id) && ($this->_id != $_SESSION['manhour']['tmp']['user_edit']['id'])) {
			MCWEB_Util::redirectAction("/user/edit/start?id={$this->_id}");
		}

		//他人アカウント編集権限が無ければ、自身の下記のパラメータ編集も行えないようにする
		if($this->_member_code)			{	$_SESSION['manhour']['tmp']['user_edit']['member_code']			= $this->_member_code;			}
		if($this->_name)				{	$_SESSION['manhour']['tmp']['user_edit']['name']				= $this->_name;					}
		if($this->_auth_lv)				{	$_SESSION['manhour']['tmp']['user_edit']['auth_lv']				= $this->_auth_lv;				}
		if($this->_post)				{	$_SESSION['manhour']['tmp']['user_edit']['post']				= $this->_post;					}
		if($this->_position)			{	$_SESSION['manhour']['tmp']['user_edit']['position']			= $this->_position;				}
		if($this->_mst_member_type_id)	{	$_SESSION['manhour']['tmp']['user_edit']['mst_member_type_id']	= $this->_mst_member_type_id;	}
		if($this->_mst_member_cost_id)	{	$_SESSION['manhour']['tmp']['user_edit']['mst_member_cost_id']	= $this->_mst_member_cost_id;	}
		if($this->_password_change)		{	$_SESSION['manhour']['tmp']['user_edit']['password_change']		= $this->_password_change;		}
		if($this->_team_list_project_id){	$_SESSION['manhour']['tmp']['user_edit']['team_list_project_id']= $this->_team_list_project_id;	}

		//自身のアカウントの編集時は、下記のパラメータは編集可とする
		if($_SESSION['manhour']['tmp']['user_edit']['id'] == $_SESSION['manhour']['member']['id']) {
			if($this->_password) {
				$_SESSION['manhour']['tmp']['user_edit']['password'] = $this->_password;
			}
		}
		//セッションから編集データを引用
		$this->setEditDataBySession();

		//IDが指定されてなければ、アカウント一覧へ
		if(empty($this->_id))
		{
			MCWEB_Util::redirectAction("/user/index");
		}
		//権限チェック
		$this->checkAuthEditMyAccount($this->_id);
	}

	//処理
	function task(MCWEB_InterfaceSceneOutputVars $access)
	{
		// 各種マスタ情報取得
		$this->array_auth_lv	= returnArrayAuthLv();
		$obj_post	=		 new Post();
		$this->array_post	= $obj_post->getDataAll();
		$obj_position			 = new Position();
		$this->array_position	= $obj_position->getDataAll();
		$obj_member_type			= new MemberType();
		$this->array_member_type	= $obj_member_type->getDataAll();
		$obj_member_cost			= new MemberCost();
		$this->array_member_cost	= $obj_member_cost->getDataAll();

		$this->password_tmp	= changePassWord($this->_password);

		//セッションのデータを表示用にセット
		$this->setProjectTeamViewListBySession();

		// パスワード変更無しの場合はクリア
		if ($this->_password)
		{
			$_SESSION['manhour']['tmp']['user_edit']['password'] = "";
		}

		// プロジェクトコードの入力制限説明文言取得
		$mm = MessageManager::getInstance();
		$this->guide_message_member_code = $mm->sprintfMessage(MessageDefine::USER_GUIDE_MESSAGE_MEMBER_CODE_FORMAT);

		// 本日時点で有効なプロジェクトを検索
		$obj_project		= new Project;
		$this->team_list_project_id	= $obj_project->getNowProject(array(PROJECT_TYPE_NORMAL,PROJECT_TYPE_INFORMAL));	// プロジェクトタイプ：通常/仮登録のみ
	}
}

?>