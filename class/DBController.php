<?php
	require_once 'DBModel.php';

	class DBController extends DBModel
	{
		protected $Limits = null;

		public function __construct($DB_H, $DB_U, $DB_P, $DB_D, $Limits, Catcher &$C = null, Logger &$L = null)
		{
			parent::__construct($DB_H, $DB_U, $DB_P, $DB_D, $C, $L);

			$this->Limits = $Limits;
		}

		public function CheckIfCanUpload($Type, $UID)
		{
			$Pack = $this->DB->prepare('SELECT `pack` FROM `users` WHERE `id` = :uid LIMIT 1');
			$Pack->bindParam(':uid', $UID);
			$Pack->execute();
			$Pack = $Pack->fetch();
			$Pack = $Pack['pack'];

			if($this->Limits[$Pack][$Type]['allowed'] == false)
				return -2;

			if($Type == 'image')
				$Table = 'images';
			else if($Type == 'audio')
				$Table = 'audios';
			// OTHERS... (SWITCH?)

			$Count = $this->DB->prepare("SELECT COUNT(*) FROM `{$Table}` WHERE `userid` = :uid");
			$Count->bindParam(':uid', $UID);
			$Count->execute();
			$Count = $Count->fetch();
			$Count = $Count[0];

			if($Count < $this->Limits[$Pack][$Type]['max'])
				return true;

			return -1;
		}
	}