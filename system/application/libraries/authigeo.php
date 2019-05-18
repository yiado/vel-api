<?php

	class AuthIGEO {

		private $User;
		
		function __contructor () {
			
		}
		
		function isValid ( $username, $password ) {
			
			//Validar con la encriptacion hecha			
			$user = Doctrine_Core::getTable('User')->validate($username, $password);
			
			if (!$user == false && $user->user_username == $username ) {
				
				$this->User = $user;
				return TRUE;
				
			} else {
				
				return FALSE;
				
			}
			
		}
		
		function getUser () {
			
			return $this->User;
			
		}
		
	}