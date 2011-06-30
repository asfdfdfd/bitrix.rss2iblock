<?php
	class BitrixAutoload
	{
		public function __construct()
		{
			define("NO_BITRIX_AUTOLOAD", False);
		}
		
		public function autoload($className)
		{
			CModule::RequireAutoloadClass($className);
		}
	};
	
	spl_autoload_register(array(new BitrixAutoload(), 'autoload'));	
?>
