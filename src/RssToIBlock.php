<?php	
	include_once 'simplepie/SimplePieAutoloader.php';
	include_once 'simplepie/idn/idna_convert.class.php';
	
	include_once 'BitrixAutoload.php';
	
	class RssToIBlock
	{
		private $_feedUrl;
		private $_iblockId;
		
		public function __construct($feedUrl, $iblockId)
		{
			$this->_feedUrl = $feedUrl;
			$this->_iblockId = $iblockId;
		}
		
		public function fetch()
		{		 
			$feed = new SimplePie();
			$feed->set_feed_url($this->_feedUrl);
			$feed->init();
			$feed->handle_content_type();
			
			CModule::IncludeModule("iblock");	
			
			$arFilter = array(
				"IBLOCK_ID" => $this->_iblockId,
				"FEED_URL" => $this->_feedUrl
			);
			$arSelectedFields = array("PROPERTY_GUID");
			
			$dbRecords = CIBlockElement::GetList(array(), $arFilter, false, false, $arSelectedFields);
			$arGuids = array();
			while ($arRecord = $dbRecords->GetNext())
				$arGuids[] = $arRecord["PROPERTY_GUID_VALUE"];
				
			foreach($feed->get_items() as $item)
			{	
				if(in_array($item->get_id(true), $arGuids))
					continue;
				
				$arProperties = array (
					"GUID" => $item->get_id(true),
					"PERMALINK" => $item->get_permalink(),
					"FEED_URL" => $this->_feedUrl
				);
				
				$arFields = array(
					"IBLOCK_ID" => $this->_iblockId,
					"NAME" => $item->get_title(),
					"PREVIEW_TEXT" => $item->get_description(),
					"DETAIL_TEXT" => $item->get_description(),
					"DATE_CREATE" => $item->get_date('d.m.Y H:i:s'), 
					"PROPERTY_VALUES" => $arProperties
				);
				
				$iblockElement = new CIBlockElement;
				$iblockElement->Add($arFields, false, false, false);
			}			
		}
	};
?>
