<?
/*///////////////////////////////////////////////////////////////

작성자		: 손상모<smson@ihelpers.co.kr>
최초작성일	: 2001.09.06

변경내용	: 

	2004.10.25 : 코드 주석 추가
				 HTTP/1.1 지원( http://www.w3.org/Protocols/rfc2616/rfc2616.html )
				 Timeout 및 Header 값 설정 함수 지원
	2004.10.26 : 에러 함수 추가
				 Transfer-Encoding 의 chunked 에 대한 decoding 처리
				 Content-Encoding의 gzip에 처리

http://www.ihelpers.co.kr

/////////////////////////////////////////////////////////////////*/

/**
* HTTP Protocol 통신 Class
*
* @author Sang Mo,Son <smson@ihelpers.co.kr>
* @version 1.5
* @access  public
*/
class HTTP{
	
	var $Socket;
	var $Server;
	var $Port;
	var $Timeout = 5;
	var	$HttpVersion = "1.0";		// 기본 Http 통신 버전
	var $Url;
	var $Length;
	var $ResponseTime;
	var $headers	= array();
	var $Response	= array();
	var $Err = false;
	var $ErrNum;
	var $ErrMsg;

	var $_chunkedLength =0;

    /**
     * Constructor
     *
	 * @param	String	Server
	 * @param	int		Port
     * @access	public
     * @return	void
     */
	function HTTP($Server,$Port = 80,$Timeout = 30)
	{
		$this->Server = $Server;
		$this->Port = $Port;
		$this->Timeout = $Timeout;

		$this->Socket = @fsockopen ($this->Server,$this->Port, $errno, $errstr, $this->Timeout);
		if(!$this->Socket){
			$this->Error(0,"Socket Connection Fail");
		}
	}

    /**
     * Timeout 시간 설정
     *
	 * @param	int		Time out (초)
     * @access	public
     * @return	void
     */
	function setTimeout($Timeout){
		$this->Timeout = $Timeout;
	}

    /**
     * Http Protocol 버전 설정
     *
	 * @param	string	Http Protocol Version ( "1.0", "1.1" )
     * @access	public
     * @return	void
     */
	function setHttpVersion($HttpVersion){
		$this->HttpVersion = $HttpVersion;
	}

    /**
     * 웹서버의 Header 정보 읽기 
     *
	 * @param	string	url
     * @access	public
     * @return	string	
     */
	function Head($Url = "/")	
	{
		$this->Url = $Url;
		$msg  = sprintf("HEAD %s HTTP/%s\r\n",$this->Url,$this->HttpVersion);
		$msg .= $this->PutHead();
		$msg .= "\n\n";
		fputs($this->Socket,$msg);

		return $this->Read();
	}

    /**
     * 웹서버의 Header 통신 여부
     *
	 * @param	string	url
     * @access	public
     * @return	boolean
     */
	function isHead($Url = "/"){
		$this->Url = $Url;
		$msg  = sprintf("HEAD %s HTTP/%s\r\n",$this->Url,$this->HttpVersion);
		if($Cookie != ""){
			$msg .= $this->PutCookie($Cookie);
		}
		$msg .= $this->PutHead();
		$msg .= "\n\n";
		fputs($this->Socket,$msg);

		return $this->isOK();
	}

    /**
     * GET 방식으로 웹서버 Header 정보 읽기 
     *
	 * @param	string	url
     * @access	public
     * @return	string	
     */
	function GetHead($Url = "/")
	{
		$this->Url = $Url;
		$msg  = sprintf("GET %s HTTP/%s\r\n",$this->Url,$this->HttpVersion);
		$msg .= $this->PutHead();
		$msg .= "\n\n";
		fputs($this->Socket,$msg);
		$out = $this->ReadHeader();
		return $out;
	}

    /**
     * GET 방식으로 통신
     *
	 * @param	string	url
	 * @param	array	cookie
     * @access	public
     * @return	string	
     */
	function Get($Url = "/",$Cookie="")
	{
		$this->Url = $Url;
		$msg  = sprintf("GET %s HTTP/%s\r\n",$this->Url,$this->HttpVersion);
		if($Cookie != ""){
			$msg .= $this->PutCookie($Cookie);
		}
		$msg .= $this->PutHead();
		$msg .= "\n\n";
		fputs($this->Socket,$msg);
		return $this->Read();
	}

    /**
     * GET 방식으로 통신가능여부
     *
	 * @param	string	url
	 * @param	array	cookie
     * @access	public
     * @return	boolean
     */
	function isGet($Url = "/",$Cookie=""){
		$this->Url = $Url;
		$msg  = sprintf("GET %s HTTP/%s\r\n",$this->Url,$this->HttpVersion);
		if($Cookie != ""){
			$msg .= $this->PutCookie($Cookie);
		}
		$msg .= $this->PutHead();
		$msg .= "\n\n";
		fputs($this->Socket,$msg);

		return $this->isOK();
	}

    /**
     * GET 방식으로 통신가능여부
     *
	 * @param	string	url
	 * @param	array	cookie
     * @access	public
     * @return	boolean
     */
	function isGetAll($Url = "/",$Cookie=""){
		$this->Url = $Url;
		$msg  = sprintf("GET %s HTTP/%s\r\n",$this->Url,$this->HttpVersion);
		if($Cookie != ""){
			$msg .= $this->PutCookie($Cookie);
		}
		$msg .= $this->PutHead();
		$msg .= "\n\n";
		fputs($this->Socket,$msg);

		$data = $this->Read();
		$this->Length = strlen($data);
		return $this->isOK($data);
	}

    /**
     * POST 방식으로 통신
     *
	 * @param	string	url
	 * @param	array	form value
	 * @param	array	cookie
     * @access	public
     * @return	string
     */
	function Post($Url ,$Data,$Cookie = "")
	{
		$this->Url = $Url;

		fputs ($this->Socket,sprintf("POST %s HTTP/%s\r\n",$this->Url,$this->HttpVersion));
		if($Cookie != ""){
			$this->PutCookie($Cookie);
		}
		$this->PutHead();
		fputs ($this->Socket, "Content-type: application/x-www-form-urlencoded\r\n");

		$out = "";
		while (list ($k, $v) = each ($Data)) 
		{
			if(strlen($out) != 0) $out .= "&";
			$out .= rawurlencode($k). "=" .rawurlencode($v);
		}
		fputs ($this->Socket, "Content-length: ".strlen($out)."\n\n"); 
		fputs ($this->Socket, "$out");
		fputs ($this->Socket, "\n");

		return $this->Read();
	}

    /**
     * POST 방식으로 통신가능여부
     *
	 * @param	string	url
	 * @param	array	form value
	 * @param	array	cookie
     * @access	public
     * @return	boolean
     */
	function IsPost($Url,$Data,$Cookie = "")
	{
		$this->Url = $Url;
		fputs ($this->Socket,sprintf("POST %s HTTP/%s\r\n",$this->Url,$this->HttpVersion));
		if($Cookie != ""){
			$this->PutCookie($Cookie);
		}
		$this->PutHead();
		fputs ($this->Socket, "Content-type: application/x-www-form-urlencoded\r\n");

		$out = "";
		while (list ($k, $v) = each ($Data)) 
		{
			if(strlen($out) != 0) $out .= "&";
			$out .= rawurlencode($k). "=" .rawurlencode($v);
		}
		fputs ($this->Socket, "Content-length: ".strlen($out)."\n\n"); 
		fputs ($this->Socket, "$out");
		fputs ($this->Socket, "\n");
		return $this->isOk();
	}

    /**
     * Request Header 정보
     *
     * @access	public
     * @return	void
     */
	function PutHead()
	{
		$msg = "";
		$msg .= "Accept: */*\r\n";
		$msg .= "Accept-Language: ko\r\n";
		$msg .= "Accept-Encoding: gzip, deflate\r\n";
		$msg .= "User-Agent: Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.0)\r\n";
		while (list($name,$value) = each ($this->headers)) {
			$msg .= "$name: $value\r\n";
		}

		if($this->Port == 80){
			$msg .= "Host: ".$this->Server."\r\n";
		} else {
			$msg .= "Host: ".$this->Server.":".$this->Port."\r\n";
		}
		$msg .= "Connection: close\r\n";
		return $msg;
	}

    /**
     * Header 정보 추가
     *
     * @access	public
	 * @param	string	name
	 * @param	string	value
     * @return	void
     */
	function AddHeader($name,$value){
		$this->headers[$name] = $value;
	}

    /**
     * 쿠키정보
     *
	 * @param	array	cookie
     * @access	public
     * @return	void
     */
	function PutCookie($cookie){
		$msg = "";
		if(is_array($cookie)){
			$out = "";
			while (list ($k, $v) = each ($cookie)) 
			{
				if(strlen($out) != 0) $out .= ";";
				$out .= rawurlencode($k). "=" .rawurlencode($v);
			}
			$msg = "Cookie: $out\n";
		} else {
			$msg = "Cookie: $cookie\n";
		}
		return $msg;
	}

    /**
     * 통신정보 읽기
     *
     * @access	public
     * @return	string
     */
	function Read(){
		$out = $this->ReadHeader();
        $chunked = isset($this->Response['transfer-encoding']) && ('chunked' == $this->Response['transfer-encoding']);
        $gzipped = isset($this->Response['content-encoding']) && ('gzip' == $this->Response['content-encoding']);
		
        $body = '';
		while(!feof($this->Socket)){
            if ($chunked) {
                $buf = $this->_readChunked();
            } else {
				$buf = fread($this->Socket,4096);
            }
			$body .= $buf;
        }
		if($gzipped){	
			$body = gzinflate(substr($body, 10));
		} 
		$this->Response['body'] = $body;
		$out .= $body;

		return $out;
	}

    /**
     * 헤더정보일기
     *
     * @access	public
     * @return	string
     */
	function ReadHeader(){
		$out	= '';
		$buf	= $this->_readLine();
		if (sscanf($buf, 'HTTP/%s %s', $http_version, $returncode) != 2) {
			$this->Error(0,"Malformed response");
			return false;
		} else {
			$this->Response["protocol"] = 'HTTP/' . $http_version;
			$this->Response["code"]     = intval($returncode);
		}
		$out .= $buf;

		while(!feof($this->Socket)){
			$buf = $this->_readLine();
			$out .= $buf;
			if($buf == "\n" || $buf == "\r\n"){ break; }
			list($name,$value) = split(":",rtrim($buf,"\r\n"),2);
			$this->Response[strtolower($name)] = trim($value);
		}
		$this->Response["header"] = $out;
		return $out;
	}

    /**
     * 통신정보 앞부분만 읽기
     *
     * @access	public
	 * @param	string	Header 정보
     * @return	boolean
     */
	function isOk($buffer = ""){
		if($buffer == "") $buffer .= fgets($this->Socket,128);
		if(preg_match('/^HTTP\/.* (2\d{2}|3\d{2}).*/',$buffer)){
			return true;
		}
		return false;
	}

    /**
     * 라인단위로 읽기
     *
     * @access	private
     * @return	string
     */
    function _readLine()
    {
		$line = '';
		while(!feof($this->Socket)){
			$line .= fgets($this->Socket,4096);
			if (substr($line, -2) == "\r\n" || substr($line, -1) == "\n") {
				return $line;
            }
        }
		return $line;
    }

    /**
     * 모든 내용을 모두 읽는다
     *
     * @access	private
     * @return	string
     */
    function _readAll()
    {
		$data = '';
		while(!feof($this->Socket)){
			$data .= fread($this->Socket,4096);
        }
		return $data;
    }

   /**
	* chunked Transfer-Encoding 으로 인코딩된 내용을 읽기
    * 
    * @access private
    * @return string
    */
    function _readChunked()
    {
        // at start of the next chunk?
        if (0 == $this->_chunkLength) {
            $line = $this->_readLine();
            if (preg_match('/^([0-9a-f]+)/i', $line, $matches)) {
                $this->_chunkLength = hexdec($matches[1]); 
                // Chunk with zero length indicates the end
                if (0 == $this->_chunkLength) {
                    $this->_readAll(); // make this an eof()
                    return '';
                }
            }
        }
        $data = fread($this->Socket,$this->_chunkLength);
        $this->_chunkLength -= strlen($data);
        if (0 == $this->_chunkLength) {
            $this->_readLine(); // Trailing CRLF
        }
        return $data;
    }

    /**
     * 닫기
     *
     * @access	public
     * @return	void
     */
	function Close()
	{
		fclose($this->Socket);
	}

    /**
     * 에러
     *
     * @access	public
     * @return	void
     */
	function Error($errnum,$errmsg)
	{
		$this->Err = true;
		$this->ErrNum = $errnum;
		$this->ErrMsg = $errmsg;
	}
	
}
?>