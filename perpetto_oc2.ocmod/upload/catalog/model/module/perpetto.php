<?php
class ModelModulePerpetto extends Model {
  
  	public function getSetting($code, $store_id) {
	    $this->load->model('setting/setting');
		return $this->model_setting_setting->getSetting($code,$store_id);
  	}

  	public function getSlotInfo($token, $store_id) {
  		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "perpetto_slots` WHERE token = '".$token."' and store_id = '".$store_id."'");
  		return $query->row;
  	}

  	public function checkIfSlotExists($token, $store_id=0) {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "perpetto_slots` WHERE token = '".$token."' and store_id = '".$store_id."'");
		if($query->num_rows<1) {
			$this->db->query("INSERT INTO `" . DB_PREFIX . "perpetto_slots` (token,position,store_id)
								VALUES ('".$token."','content_bottom' ,'".$store_id."');");
			return true;
		} else {
			return $query->row;
		}
	}

	public function setModuleToLayout($slot_page,$position) {
		$layout_id = 0;

		switch($slot_page) {
			case 'home_page': $layout_id = 1; break; 
			case 'product_page': $layout_id = 2; break; 
			case 'category_page': $layout_id = 3; break; 
			case 'cart_page': $layout_id = 7; break; 
		}

		if(!empty($position)){
			$query = $this->db->query("SELECT * FROM " . DB_PREFIX ."layout_module WHERE layout_id = '".$layout_id."' AND position = '".$position."' and code = '" . $this->db->escape('perpetto')."'");
			if($query->num_rows < 1) {

				$order = $this->db->query("SELECT sort_order FROM " . DB_PREFIX ."layout_module WHERE layout_id = '".$layout_id."' AND position = '".$position."' and code != '" . $this->db->escape('perpetto')."' ORDER BY sort_order DESC");
				if(!empty($order->row['sort_order'])) {
					$sort_order = (int)$order->row['sort_order'] + 1;
				} else {
					$sort_order = 0;
				}

				$this->db->query("INSERT INTO " . DB_PREFIX . "layout_module 
				SET layout_id = '" . (int)$layout_id . "', code = '" . $this->db->escape('perpetto') . "', position = '" . 
				$this->db->escape($position) . "', sort_order = '".(int)$sort_order."'");

			}
		} 

		$this->load->model('design/layout');
	}

	public function getLayouts($data = array()) {
		$sql = "SELECT * FROM " . DB_PREFIX . "layout";

		$sort_data = array('name');

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY name";
		}

		if (isset($data['order']) && ($data['order'] == 'DESC')) {
			$sql .= " DESC";
		} else {
			$sql .= " ASC";
		}

		if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}

			if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}

			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}

		$query = $this->db->query($sql);

		return $query->rows;
	}

	public function deleteAllModuleLayouts($moduleName = "perpetto") {
		$layouts = array();
		$layouts = $this->getLayouts();
			
		foreach ($layouts as $layout) {
			$this->db->query("DELETE FROM " . DB_PREFIX . 
				"layout_module 
				WHERE layout_id = '" . (int)$layout['layout_id'] . "' and  
				code = '" . $this->db->escape($moduleName)."'");
		}
	}

	public function perpettoCall($data) {
		if(empty($data['method']) || empty($data['account_id']) || empty($data['secret'])) return false; // return if the request is not structured correctly

		switch($data['method']) {
			case 'info': 
				$url = "https://".$data['account_id'].".api.perpetto.com/v0/info?secret=".$data['secret'];
				break;
			case 'slots':
				$url = "https://".$data['account_id'].".api.perpetto.com/v0/info/slots?secret=".$data['secret'];
				break;
		}

		if(function_exists('curl_version')) { // Check if curl enabled
			
			$ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            if( ($result = curl_exec($ch) ) !== false) {
                $result = json_decode($result);
            } else {
            	$result = false;
            }

		} else { // Fallback to file_get_contents
			$result = @file_get_contents($url); 
			if($result != false) {
				 $result = json_decode($result);
			}
		}

		return $result;
	}

}
?>