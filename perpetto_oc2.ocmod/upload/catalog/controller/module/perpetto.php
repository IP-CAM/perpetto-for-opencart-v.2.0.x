<?php 
class ControllerModulePerpetto extends Controller  {
	private $moduleName = 'Perpetto';
	private $moduleNameSmall = 'perpetto';
	private $moduleData_module = 'perpetto_module';
	private $moduleModel = 'model_module_perpetto';
	
    public function index() {
        $this->load->model('module/'.$this->moduleNameSmall);
    
        $languageVariables= array('heading_title', 'add_to_cart');

        foreach ($languageVariables as $variable) {
            $data[$variable] = $this->language->get($variable);
        }

        $data['ptto_current_position'] = $GLOBALS['ptto_current_position'];


        $this->load->model('design/layout');

		if (isset($this->request->get['route'])) {
			$route = (string)$this->request->get['route'];
		} else {
			$route = 'common/home';
		}

		$layout_id = 0;

		if ($route == 'product/category' && isset($this->request->get['path'])) {
			$this->load->model('catalog/category');

			$path = explode('_', (string)$this->request->get['path']);

			$layout_id = $this->model_catalog_category->getCategoryLayoutId(end($path));
		}

		if ($route == 'product/product' && isset($this->request->get['product_id'])) {
			$this->load->model('catalog/product');

			$layout_id = $this->model_catalog_product->getProductLayoutId($this->request->get['product_id']);
		}

		if ($route == 'information/information' && isset($this->request->get['information_id'])) {
			$this->load->model('catalog/information');

			$layout_id = $this->model_catalog_information->getInformationLayoutId($this->request->get['information_id']);
		}

		if (!$layout_id) {
			$layout_id = $this->model_design_layout->getLayout($route);
		}

		if (!$layout_id) {
			$layout_id = $this->config->get('config_layout_id');
		}

		$data['moduleData'] = $this->config->get($this->moduleNameSmall);

		if(!empty($data['moduleData']['account_id']) && !empty($data['moduleData']['secret']) && !empty($data['moduleData']['connected']) && $data['moduleData']['connected'] == "yes") {
			$account_id = $data['moduleData']['account_id'];
            $secret = $data['moduleData']['secret'];

            $request_data = array(
                'method'        => 'slots',
                'account_id'    => $account_id,
                'secret'        => $secret
            );

            $result = $this->model_module_perpetto->perpettoCall($request_data);

            $grouped_slots = array();
            if(!isset($result->error) && !empty($result->data)) {
                $slots = $result;
                foreach($slots->data->slots as $slot) {
                    $grouped_slots[$slot->page][] = $slot;
                }
            }

            if($layout_id == 1 || strpos($route,'common/home') !== false) {
            	foreach($grouped_slots['home_page'] as $slot) {
            		$data['page_slots'][]['token'] = $slot->token;
            	}
            } else if($layout_id == 2 || strpos($route,'product/product') !== false) {
            	foreach($grouped_slots['product_page'] as $slot) {
            		$data['page_slots'][]['token'] = $slot->token;
            	}
            } else if($layout_id == 3 || strpos($route,'product/category') !== false) {
            	foreach($grouped_slots['category_page'] as $slot) {
            		$data['page_slots'][]['token'] = $slot->token;
            	}
            } else if(strpos($route,'checkout/cart') !== false) {
            	foreach($grouped_slots['cart_page'] as $slot) {
            		$data['page_slots'][]['token'] = $slot->token;
            	}
            }

            if(!empty($data['page_slots'])) {
                foreach($data['page_slots'] as $slot) {
                    $slot_info = $this->model_module_perpetto->getSlotInfo($slot['token'],$this->config->get("config_store_id"));
                    if(!empty($slot_info)) {
                        if($slot_info['position'] == $data['ptto_current_position']) {
                            $data['slots'][] = $slot;
                        }
                    }
                }

                if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/module/perpetto.tpl')) {
                    return $this->load->view($this->config->get('config_template').'/template/module/'.$this->moduleNameSmall.'.tpl', $data);
                } else {
                    return $this->load->view('default/template/module/'.$this->moduleNameSmall.'.tpl', $data);
                }
            }

		}
		
			
    }

    public function resetSlotPositions() {

        if($this->request->server['REQUEST_METHOD'] == "PUT") {
            $put_request = array();

            $put_request = fopen("php://input","r");  

            $request_query = "";
            $query = "";

            while ($data = fread($put_request, 1024)) {
                $request_query .= $data;
            }    

            parse_str($request_query,$query);

            $request_acc_id = !empty($query['account_id']) ? $query['account_id'] : "";
            $request_secret = !empty($query['secret']) ? $query['secret'] : "";

            $json_result = array();
            
            if(!empty($request_acc_id) && !empty($request_secret)) {                
                $this->load->model('module/'.$this->moduleNameSmall);
                $settings = $this->config->get($this->moduleNameSmall);
                $account_id = !empty($settings['account_id']) ? $settings['account_id'] : "";
                $secret = !empty($settings['secret']) ? $settings['secret'] : "";

                if($account_id == $request_acc_id && $secret == $request_secret) {
                    $request_data = array(
                        'method'        => 'slots',
                        'account_id'    => $account_id,
                        'secret'        => $secret
                    );

                    $result = $this->model_module_perpetto->perpettoCall($request_data);

                    if(!isset($result->error) && !empty($result->data)) {
                        $this->model_module_perpetto->deleteAllModuleLayouts();
                        $slots = $result;
                        foreach($slots->data->slots as $slot) {
                            $token_info_from_db = $this->model_module_perpetto->checkIfSlotExists($slot->token,$this->config->get('config_store_id'));
                            if(!empty($token_info_from_db['position'])) {
                                $slot->position = $token_info_from_db['position'];
                                $this->model_module_perpetto->setModuleToLayout($slot->page,$slot->position);
                            } else if($token_info_from_db) {
                                $slot->position = "content_bottom";
                                $this->model_module_perpetto->setModuleToLayout($slot->page,$slot->position);
                            } else {
                                $slot->position = 0;
                            }
                        }
                        $json_result['status'] = 'success';
                    } else {
                        $json_result['status'] = 'error';
                        $json_result['errors'][] = 'Cannot get slots information!';
                    }
                } else {
                    $json_result['status'] = 'error';
                    $json_result['errors'][] = 'Wrong credentials!';
                }
                
            } else {
                $json_result['status'] = 'error';
                $json_result['errors'][] = 'Missing parameters!';
            }
           
            echo json_encode($json_result);exit;
            
        } else {
            header('HTTP/1.0 404 Not Found');
            echo "<h1>404 Not Found</h1>";
            echo "The page that you have requested could not be found.";
            exit();
        }
        
        
    }
    
    public function getCatalogURL($store_id){
        if(isset($store_id) && $store_id){
            $storeURL = $this->db->query('SELECT url FROM `'.DB_PREFIX.'store` WHERE store_id=' . $store_id)->row['url'];
        }elseif (isset($_SERVER['HTTPS']) && (($_SERVER['HTTPS'] == 'on') || ($_SERVER['HTTPS'] == '1'))) {
            $storeURL = HTTPS_SERVER;
        } else {
            $storeURL = HTTP_SERVER;
        } 
        return $storeURL;
    }
}
?>
