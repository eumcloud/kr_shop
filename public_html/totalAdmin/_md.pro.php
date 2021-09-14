<?PHP

	include "inc.php";

	// - 입력수정 사전처리 ---
	if( in_array($_mode , array("add" , "modify"))) {

		// --사전 체크 ---
		$mdName = nullchk($mdName , "MD명을 입력하세요");
		$mdNick = nullchk($mdNick , "닉네임을 입력하세요");
		// --사전 체크 ---

		// --이미지 처리 ---
		$mdImg_name = _PhotoPro( "../upfiles/member" , "mdImg" ) ; // 사진
		// --이미지 처리 ---

		// --query 사전 준비 ---
		$sque = " 
			mdName='{$mdName}',
			mdID='{$mdID}',
			mdNick='{$mdNick}',
			mdUnique='{$mdUnique}',
			mdAim='{$mdAim}',
			mdImg='{$mdImg_name}'
		";
		// --query 사전 준비 ---
	}
	// - 입력수정 사전처리 ---



	// - 모드별 처리 ---
	switch( $_mode ){

		case "add":
			_MQ_noreturn("insert odtMD set $sque ");
			$mdNo = mysql_insert_id();
			error_loc("_md.form.php?_mode=modify&mdNo=${mdNo}&_PVSC=${_PVSC}");
			break;


		case "modify":
			_MQ_noreturn(" update odtMD set  $sque where mdNo='${mdNo}' ");
			error_loc("_md.form.php?_mode=modify&mdNo=${mdNo}&_PVSC=${_PVSC}");
			break;


		case "delete":
			// -- 이미지 삭제 ---
			$r = _MQ("select mdImg from odtMD where mdNo='${mdNo}' ");
			if( $r[mdImg]) {
				_PhotoDel( "../upfiles/member" , $r[mdImg] );
			}
			// -- 이미지 삭제 ---

			_MQ_noreturn("delete from odtMD where mdNo='$mdNo' ");
			error_loc( "_md.list.php?" . enc('d' , $_PVSC) );
			break;
	}
	// - 모드별 처리 ---

?>