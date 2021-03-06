<?php

/**
 * Page type for building unique custom content without the need for special templates
 *
 * @author Michael Eckert
 *
 * @package IQ_Content_Builder
 *
 */
 
class ContentBuilderColumn_Image extends ContentBuilderColumn
{
	private static $db = array(
		'AltAttribute' => 'Varchar(255)',
		'Width' => 'Varchar(10)',
		'Align' => "Enum('Left,Center,Right','Left')",
		'Link' => 'Link'
	);
	
	private static $has_one = array(
		'Image' => 'Image'
	);
	
	private static $singular_name = 'Image';
	
	/**
	 * this is the size images will be set to if greater than this size.
	 * to reduce oversized images 
	 * @var string - size in pixels
	 */
	private static $image_max_width = '1200';
	
	public function getCMSFields()
	{
		$fields = parent::getCMSFields();
		$pageName = preg_replace('/[^a-zA-Z0-9]/','-',$this->Page()->Title);
		while(preg_match('/\-\-/',$pageName))
		{
			$pageName = preg_replace('/\-\-/','-',$pageName);
		}
		$fields->dataFieldByName('Image')->setAllowedExtensions(array('jpg','jpeg','png','gif'))->setFolderName('Pages/'.$pageName);
		$fields->dataFieldByName('Width')->setRightTitle('ex. 200px or 80%');
		$fields->addFieldToTab('Root.Main', LinkField::create('Link','Link (optional)') );
		return $fields;
	}
	
	public function Contents()
	{
		$Image = $this->Image();
		if ( ($this->Width) && (preg_match('/px/',$this->Width)) )
		{
			$Image = $Image->SetWidth($this->Width);
		}
		elseif ($Image->Width > $this->getMaxImageSize())
		{
			$Image = $Image->SetWidth($this->getMaxImageSize());
		}
		$html = '<img src="'.$Image->getURL().'" alt="'.$this->AltAttribute.'"';
		if (preg_match('/\%/',$this->Width)) $html .= ' style="width:'.$this->Width.'"';
		$html .= " />";
		return $html;
	}
	
	public function getMaxImageSize()
	{
		return preg_replace('/[^0-9]/','',$this->config()->image_max_width);
	}
	
	public function GridFieldContents()
	{
		return ($this->Image()->Exists()) ? $this->Image()->SetHeight(50)->forTemplate() : '[ No Image ]';
	}
}





