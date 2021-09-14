<?
/*///////////////////////////////////////////////////////////////

작성자		: 손상모<smson@ihelpers.co.kr>
웹사이트	: http://www.ihelpers.co.kr
최초작성일	: 2004.10.25

변경내용	: 

	2004.11.01	Class외에 Cache 함수 추가


/////////////////////////////////////////////////////////////////*/


/**
* URLCache Class
*
* URL 정보를 Cache 하는 Class입니다.
*
* @author Sang Mo,Son <smson@ihelpers.co.kr>
* @version 0.9 beta
* @access  public
*/
class URLCache {

	var $CacheDir;
	var $Interval;

    /**
     * Constructor
     *
	 * @param	String	Cache Directory
	 * @param	int		Url Stale Time(s)
     * @access	public
     * @return	void
     */
	function URLCache($CacheDir = './cache',$Interval = 3600){
		$this->CacheDir = $CacheDir;
		$this->Interval	= $Interval;
	}

    /**
     * Set Cache Directory
     *
	 * @param	int		Url Stale Time
     * @access	public
     * @return	void
     */
	function setCacheDir($CacheDir){
		$this->CacheDir	= $CacheDir;
	}

    /**
     * Set Url Stale Time(s)
     *
     * @access	public
     * @return	void
     */
	function setInterval($Interval){
		$this->Interval	= $Interval;
	}

    /**
     * URL 정보에 해당하는 정보를 저장
     *
	 * @param	string	url
	 * @param	object	content
     * @access	public
     * @return	void
     */
	function set($url,$obj){
		$fname	= $this->_makeFileName($url);
		$fp = fopen($fname,'w');
		if($fp){
			fwrite($fp,$this->_serialize($obj));
			fclose($fp);
			return true;
		} else {
			$this->Error("Unable to open file for writing : $fname");
			return false;
		}
	}

    /**
     * URL 정보에 해당하는 정보 읽기
     *
	 * @param	string	url
     * @access	public
     * @return	object	
     */
	function get($url){
		$fname	= $this->_makeFileName($url);
		$fp = fopen($fname,'r');
		if($fp){
			$data = fread($fp,filesize($fname));
			fclose($fp);
			return $this->_unserialize($data);
		} else {
			$this->Error("Cache doesn't contain : $url");
			return false;
		}
	}

    /**
     * Cache 파일 점검
     *
	 * @param	string	url
     * @access	public
     * @return	int( -1 : not found, 0 : Stale, 1 : Hit )
     */
	function CheckCache($url){
		$fname	= $this->_makeFileName($url);
		if(file_exists($fname)){
			if((time()- filemtime($fname)) >= $this->Interval){
				return 0;
			} else {
				return 1;
			}
		} else {
			return -1;
		}
	}

    /**
     * URL 정보를 파일명으로 변경
     *
	 * @param	string	url
     * @access	private
     * @return	string	
     */
	function _makeFileName($url){
		return $this->CacheDir."/".md5($url);
	}

    /**
     * Serialize The Content
     *
	 * @param	object	content
     * @access	private
     * @return	string	
     */
	function _serialize($object){
		return serialize($object);
	}

    /**
     * Unserialize The Content
     *
	 * @param	string	content
     * @access	private
     * @return	object	
     */
	function _unserialize($content){
		return unserialize($content);
	}

    /**
     * Error 정보 처리
     *
	 * @param	string	error message
     * @access	public
     * @return	void
     */
	function Error($errmsg){
		printf($errmsg);
	}
}

/**
* URL 정보에 대한 Cache 함수
*
* @access	public
* @param	boolean		isCache
* @param	string		Cache Directory
* @param	int			lifetime
* @return	boolean
*/
function Cache($iscache = true,$cachedir = './cache',$lifetime = 3600){
	global $_GET,$_POST,$_SERVER;

	if($iscache == true && $_GET['CACHE'] != "NOCACHE" && count($_POST) == 0){
		$url = sprintf("http://%s%s",$_SERVER['HTTP_HOST'],$_SERVER['REQUEST_URI']);
		$urlcache = new URLCache($cachedir,$lifetime);
		$result = $urlcache->CheckCache($url);

		if($result == 1){		// lifetime 이전
			$html = $urlcache->get($url);
		} else {				// lfietime 이후 또는 Cache파일이 없을 경우 
			$curl = sprintf("http://%s%s?CACHE=NOCACHE&%s",$_SERVER['HTTP_HOST'],
						$_SERVER['PHP_SELF'],$_SERVER['QUERY_STRING']);
			$fd = fopen ("$curl", "r");
			$content = "";
			while (!feof ($fd)) {
				$buffer = fgets($fd, 4096);
				$content .= $buffer;
			}
			$html['body'] = $content;
			$urlcache->set($url,$html);
		}
		echo $html['body'];
		exit;
	} else {
		return false;
	}
}
?>