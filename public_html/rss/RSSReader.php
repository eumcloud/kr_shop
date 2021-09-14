<?
/*///////////////////////////////////////////////////////////////

작성자		: 손상모<smson@ihelpers.co.kr>
최초작성일	: 2004.10.27

변경내용		:

	2006.05.30 - 302 Object Moved 문제점 처리
	2006.05.30 - encoding 문제점 처리

http://www.ihelpers.co.kr

/////////////////////////////////////////////////////////////////*/

require_once("RSSParser.php");
require_once("URLCache.php");
require_once("httpclass.php");

/**
* RSS Reader Class
*
* RSS Format 읽기 기능 ( URLCache와 httpclass 이용 )
*
* @author Sang Mo,Son <smson@ihelpers.co.kr>
* @version 0.9 beta
* @access  public
*/
class RSSReader extends RSSParser {

	var $url;

	var $ErrNum;
	var $ErrMsg;

	var $isCache;
	
	var $CacheDir;

	var $cacheInterval;
    
	/**
     * Constructor
     *
     * @access	public
	 * @param	string	url
	 * @param	boolean	Cache 유무
	 * @param	int		Cache 유효시간
     * @return	void
     */
	function RSSReader($url,$isCache = true,$cacheInterval = 3600,$CacheDir = "./cache"){
		$this->url = $url;
		$this->isCache = $isCache;
		$this->cacheInterval = $cacheInterval;
		$this->CacheDir	= $CacheDir;
		parent::RSSParser();
	}

	/**
     * Cache 유무 설정
     *
     * @access	public
	 * @param	boolean	Cache 유무
     * @return	void
     */
	function setCache($isCache){
		$this->isCache = $isCache;
	}

	/**
     * Cache 디렉토리 설정
     *
     * @access	public
	 * @param	string	Cache 디렉토리
     * @return	void
     */
	function setCacheDir($CacheDir){
		$this->CacheDir = $CacheDir;
	}

	/**
     * Cache 유효시간 설정
     *
     * @access	public
	 * @param	int		Cache 유효시간
     * @return	void
     */
	function setCacheInterval($cacheInterval){
		$this->cacheInterval = $cacheInterval;
	}

	/**
     * RSS 읽기
     *
     * @access	private
     * @return	boolean	
     */
	function Read(){
		$response = $this->_getResponse();
		if($response){
			if(preg_match("/<\?xml.+encoding=\"(.+)\".+\?>/i",$response['body'],$match)){
				if(strtoupper($match[1]) == "UTF-8"){
					$response['body'] = iconv("UTF-8","EUC-KR",$response['body']);
				}
			}
			//echo $response['body'];
			$this->parse($response['body']);
			return true;
		} else {
			return false;
		}
	}

	/**
     * RSS의 URL 또는 Cache 정보
     *
     * @access	private
     * @return	array	response array
     */
	function _getResponse(){

		$aurl = parse_url($this->url);
		$host = $aurl["host"];
		$port = $aurl["port"];
		$path = $aurl["path"];
		if(!empty($aurl["query"])){
			$path = sprintf("%s?%s",$path,$aurl["query"]);
		}
		if(empty($port)){ $port = 80; }
		if(empty($path)){ $path = "/"; }


		if($this->isCache){
			$cache = new URLCache();
			$cache->setInterval($this->cacheInterval);
			$cache->setCacheDir($this->CacheDir);

			$status	= $cache->checkCache($this->url);

			if($status == 1){					// 캐쉬정보가 새로운 것이라면
				$response = $cache->get($this->url);	
				return $response;
			} else {
				$http = new HTTP($host,$port,10);
				if(!$http->Err){
					if($status == 0){			// 캐쉬정보가 이전것일때
						$response = $cache->get($this->url);	
						$http->AddHeader("If-Modified-Since",$response['last-modified']);
						$http->AddHeader("ETag",$response['etag']);
					}
					$http->Get($path);

					if($http->Response['code'] == 302){
						$this->url = $http->Response['location'];
						$response = $this->_getResponse();
						return $response;
					} else {					
						if($http->Response['code'] == 304){
						} elseif($http->Response['code'] == 200 || $http->Response['code'] == 304){
							$response = $http->Response;
							$cache->set($this->url,$response);
						} else {
							$this->Error(0,"Response Not Success");
							return false;
						}
						return $response;
					}
				} else {
					$this->Error(0,"Socket Connect Fail");
					return false;
				}
				$http->Close();
			}
		} else {

			$http = new HTTP($host,$port,10);
			if(!$http->Err){
				$buf = $http->Get($path);

				if($http->Response['code'] == 302){
					$this->url = $http->Response['location'];
					$response = $this->_getResponse();
					return $response;
				} else {
					if($http->Response['code'] == 200 || $http->Response['code'] == 304){				
						return $http->Response;
					} else {
						$this->Error(0,"Response Not Success");
						return false;
					}
				}
			} else {
				$this->Error(0,"Socket Connect Fail");
				return false;
			}
			$http->Close();
		}
	}

    /**
     * 에러
     *
     * @access	public
	 * @param	int		error number
	 * @param	string	error message
     * @return	void
     */
	function Error($errnum,$errmsg){
		$this->ErrNum = $errnum;
		$this->ErrMsg = $errmsg;
	}
}
