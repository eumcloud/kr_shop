<?
/*///////////////////////////////////////////////////////////////

작성자		: 손상모<smson@ihelpers.co.kr>
최초작성일	: 2004.10.25

변경내용	: 없슴

http://www.ihelpers.co.kr

/////////////////////////////////////////////////////////////////*/

/**
* RSS parser class.
*
* This class is a parser for Resource Description Framework (RDF) Site
*
* @author Sang Mo,Son <smson@ihelpers.co.kr>
* @version 0.9 beta
* @access  public
*/
class RSSParser {

	var $parser;

    var $insideTag	= '';
    var $activeTag	= '';
    var $channel	= array();

    var $items		= array();
    var $item		= array();

	var $images		= array();
    var $image		= array();

    var $textinput	= array();
    var $textinputs	= array();

	// 2004.10.25 RSS 2.0 적용
    var $parentTags		= array('CHANNEL', 'ITEM', 'IMAGE', 'TEXTINPUT');
	var $channelTags	= array('TITLE', 'LINK', 'DESCRIPTION', 'LANGUAGE','COPYRIGHT','MANAGINGEDITOR',
								'WEBMASTER','PUBDATE','LASTBUILDDATE','CATEGORY','GENERATOR','DOCS','CLOUD',
								'TTL','RATING','SKIPHOURS','SKIPDAYS','IMAGE','ITEMS', 'TEXTINPUT');
    var $itemTags		= array('TITLE', 'LINK', 'DESCRIPTION', 'AUTHOR','CATEGORY','COMMENTS','ENCLOSURE','GUID','PUBDATE','SOURCE');
    var $imageTags		= array('TITLE', 'URL', 'LINK','WIDTH','HEIGHT','DESCRIPTION');
    var $textinputTags	= array('TITLE', 'DESCRIPTION', 'NAME', 'LINK');
    var $moduleTags		= array('DC:TITLE', 'DC:CREATOR', 'DC:SUBJECT', 'DC:DESCRIPTION',
							    'DC:PUBLISHER', 'DC:CONTRIBUTOR', 'DC:DATE', 'DC:TYPE',
								'DC:FORMAT', 'DC:IDENTIFIER', 'DC:SOURCE', 'DC:LANGUAGE',
								'DC:RELATION', 'DC:COVERAGE', 'DC:RIGHTS',
								'BLOGCHANNEL:BLOGROLL', 'BLOGCHANNEL:MYSUBSCRIPTIONS',
								'BLOGCHANNEL:MYSUBSCRIPTIONS', 'BLOGCHANNEL:CHANGES');

    /**
     * Constructor
     *
     * @access	public
     * @return	void
     */
	function RSSParser(){
		$this->_create();
	}

    /**
     * create the XML parser resource
     *
     * @access  private
     * @return	boolean
     *
     * @see		xml_parser_create
     */
    function _create(){
		$this->parser = @xml_parser_create();
        if (is_resource($this->parser)) {
			xml_set_object($this->parser, &$this);
			xml_set_element_handler($this->parser, "startHandler", "endHandler");
			xml_set_character_data_handler($this->parser, "cdataHandler");
            return true;
        }
        return false;
    }

    /**
     * Free the internal resources associated with the parser
     * 
     * @return null
     **/
    function free(){
        if (is_resource($this->parser)) {
            xml_parser_free($this->parser);
            unset( $this->parser );
        }
        return null;
    }

    /**
     * Start element handler for XML parser
     *
     * @access private
     * @param  object XML parser object
     * @param  string XML element
     * @param  array  Attributes of XML tag
     * @return void
     */
    function startHandler($parser, $element, $attr){
        switch ($element) {
            case 'CHANNEL':
            case 'ITEM':
            case 'IMAGE':
            case 'TEXTINPUT':
                $this->insideTag = $element;
                break;

            default:
                $this->activeTag = $element;
        }
    }

    /**
     * End element handler for XML parser
     *
     * @access private
     * @param  object XML parser object
     * @param  string
     * @return void
     */
    function endHandler($parser, $element){
        if ($element == $this->insideTag) {
            $this->insideTag = '';
            $this->struct[] = array_merge(array('type' => strtolower($element)),
                                          $this->last);
        }

        if ($element == 'ITEM') {
            $this->items[] = $this->item;
            $this->item = '';
        }

        if ($element == 'IMAGE') {
            $this->images[] = $this->image;
            $this->image = '';
        }

        if ($element == 'TEXTINPUT') {
            $this->textinputs = $this->textinput;
            $this->textinput = '';
        }

        $this->activeTag = '';
    }

    /**
     * Handler for character data
     *
     * @access private
     * @param  object XML parser object
     * @param  string CDATA
     * @return void
     */
    function cdataHandler($parser, $cdata){
        if (in_array($this->insideTag, $this->parentTags)) {
            $tagName = strtolower($this->insideTag);
            $var = $this->{$tagName . 'Tags'};

            if (in_array($this->activeTag, $var)){
                $this->_add($tagName, strtolower($this->activeTag),$cdata);
			} elseif(in_array($this->activeTag, $this->moduleTags)) {
                $this->_add($tagName, strtolower($this->activeTag),$cdata);
            }
        }
	}

    /**
     * Add element to internal result sets
     *
     * @access private
     * @param  string Name of the result set
     * @param  string Fieldname
     * @param  string Value
     * @return void
     * @see    cdataHandler
     */
    function _add($type, $field, $value)
    {
        if (empty($this->{$type}) || empty($this->{$type}[$field])) {
            $this->{$type}[$field] = $value;
        } else {
            $this->{$type}[$field] .= $value;
        }
        $this->last = $this->{$type};
    }

    /**
     * Central parsing function.
     *
     * @access	public
	 * @param	string	XML Content
     * @return	boolean
     */
    function parse($data){
        xml_parse($this->parser,$data);
        return true;
    }

    /**
     * Get complete structure of RSS file
     *
     * @access public
     * @return array
     */
    function getStructure()
    {
        return (array)$this->struct;
    }

    /**
     * Get general information about current channel
     *
     * @access public
     * @return array
     */
    function getChannel()
    {
        return (array)$this->channel;
    }

    /**
     * Get items from RSS file
     *
     * @access public
     * @return array
     */
    function getItems()
    {
        return (array)$this->items;
    }

    /**
     * Get images from RSS file
     *
     * @access public
     * @return array
     */
    function getImages()
    {
        return (array)$this->images;
    }

    /**
     * Get text input fields from RSS file
     *
     * @access public
     * @return array
     */
    function getTextinputs()
    {
        return (array)$this->textinputs;
    }
}
?>