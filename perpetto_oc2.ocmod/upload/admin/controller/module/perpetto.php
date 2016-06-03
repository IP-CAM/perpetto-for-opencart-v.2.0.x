<?php
class ControllerModulePerpetto extends Controller {
	private $moduleName = 'perpetto';
	private $moduleNameSmall = 'perpetto';
	private $moduleData_module = 'perpetto_module';
	private $moduleModel = 'model_module_perpetto';
	
    public function index() { 
		$data['moduleName'] = $this->moduleName;
		$data['moduleNameSmall'] = $this->moduleNameSmall;
		$data['moduleData_module'] = $this->moduleData_module;
		$data['moduleModel'] = $this->moduleModel;
	 
        $this->load->language('module/'.$this->moduleNameSmall);
        $this->load->model('module/'.$this->moduleNameSmall);
        $this->load->model('setting/store');
        $this->load->model('setting/setting');
        $this->load->model('localisation/language');
        $this->load->model('design/layout');

        $catalogURL = $this->getCatalogURL();
 
        $this->document->addStyle('view/stylesheet/'.$this->moduleNameSmall.'/'.$this->moduleNameSmall.'.css');
        $this->document->setTitle($this->language->get('heading_title'));

        if(!isset($this->request->get['store_id'])) {
           $this->request->get['store_id'] = 0; 
        }
	
        $store = $this->getCurrentStore($this->request->get['store_id']);
		
        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) { 	
            if (!empty($_POST['OaXRyb1BhY2sgLSBDb21'])) {
                $this->request->post[$this->moduleNameSmall]['LicensedOn'] = $_POST['OaXRyb1BhY2sgLSBDb21'];
            }

            if (!empty($_POST['cHRpbWl6YXRpb24ef4fe'])) {
                $this->request->post[$this->moduleNameSmall]['License'] = json_decode(base64_decode($_POST['cHRpbWl6YXRpb24ef4fe']), true);
            }

            $account_id = $this->request->post[$this->moduleNameSmall]['account_id'];
            $secret = $this->request->post[$this->moduleNameSmall]['secret'];

            $account_request_data = array(
                'method'        => 'info',
                'account_id'    => $account_id,
                'secret'        => $secret
            );

            $account_info = $this->model_module_perpetto->perpettoCall($account_request_data);
            
            if(!isset($account_info->error) && !empty($account_info->data)) {
                $this->request->post[$this->moduleNameSmall]['connected'] = 'yes';
            } else {
                if(!empty($account_info->error)) {
                    $this->session->data['error_warning'] = $account_info->error;
                } else {
                    $this->session->data['error_warning'] = $this->language->get('text_invalid_account');
                }
            }            

        	$this->model_setting_setting->editSetting($this->moduleNameSmall, $this->request->post, $this->request->post['store_id']);            
			
            $this->response->redirect($this->url->link('module/'.$this->moduleNameSmall, 'store_id='.$this->request->post['store_id'] . '&token=' . $this->session->data['token'], 'SSL'));
        }
		
		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];
			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}
		
		if (isset($this->session->data['error_warning'])) {
			$data['error_warning'] = $this->session->data['error_warning'];
            unset($this->session->data['error_warning']);
		} else {
			$data['error_warning'] = '';
		}

        $data['breadcrumbs']   = array();
        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL'),
        );
        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_module'),
            'href' => $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL'),
        );
        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('module/'.$this->moduleNameSmall, 'token=' . $this->session->data['token'], 'SSL'),
        );

        $languageVariables = array(
		    // Main
			'heading_title',
			'error_permission',
			'text_success',
			'text_enabled',
			'text_disabled',
			'button_cancel',
			'save_changes',
			'text_default',
			'text_module',
			// Control panel
            'entry_code',
			'entry_code_help',
            'text_content_top', 
            'text_content_bottom',
            'text_column_left', 
            'text_column_right',
            'entry_layout',         
            'entry_position',       
            'entry_status',         
            'entry_sort_order',     
            'entry_layout_options',  
            'entry_position_options',
			'entry_action_options',
            'button_add_module',
            'button_remove',
			// Custom CSS
			'custom_css',
            'custom_css_help',
            'custom_css_placeholder',
			// Module depending
			'wrap_widget',
			'wrap_widget_help',
			'text_products',
			'text_products_help',
			'text_image_dimensions',
			'text_image_dimensions_help',
			'text_pixels',
			'text_panel_name',
			'text_panel_name_help',
			'text_products_small',
			'show_add_to_cart',
			'show_add_to_cart_help',
            'text_invalid_account',
            'text_connected_account'
        );
       
        foreach ($languageVariables as $languageVariable) {
            $data[$languageVariable] = $this->language->get($languageVariable);
        }
 
        $data['stores'] = array_merge(array(0 => array('store_id' => '0', 'name' => $this->config->get('config_name') . ' (' . $data['text_default'].')', 'url' => HTTP_SERVER, 'ssl' => HTTPS_SERVER)), $this->model_setting_store->getStores());
        $data['languages']              = $this->model_localisation_language->getLanguages();
        $data['store']                  = $store;
        $data['token']                  = $this->session->data['token'];
        $data['action']                 = $this->url->link('module/'.$this->moduleNameSmall, 'token=' . $this->session->data['token'], 'SSL');
        $data['login_action']           = $this->url->link('module/'.$this->moduleNameSmall.'/perpettoFirstLogin', 'token=' . $this->session->data['token'], 'SSL');

        $data['cancel']                 = $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL');
        $data['change_slot_position']   = htmlspecialchars_decode($this->url->link('module/perpetto/editSlotPosition','token=' . $this->session->data['token'], 'SSL'));
        $data['data']		         	= $this->model_setting_setting->getSetting($this->moduleNameSmall, $store['store_id']);
        $data['catalog_url']			= $catalogURL;
		  
		$data['moduleData']				= isset($data['data'][$this->moduleNameSmall]) ? $data['data'][$this->moduleNameSmall] : array ();
		
		$data['header']					= $this->load->controller('common/header');
		$data['column_left']			= $this->load->controller('common/column_left');
		$data['footer']					= $this->load->controller('common/footer');

        if(empty($data['data'])) {
            $this->response->setOutput($this->load->view('module/perpetto/first_login.tpl', $data));
        } else {

            if(!empty($data['data'][$this->moduleNameSmall]['account_id']) && !empty($data['data'][$this->moduleNameSmall]['secret'])) {
                $account_id = $data['data'][$this->moduleNameSmall]['account_id'];
                $secret = $data['data'][$this->moduleNameSmall]['secret'];

                $account_request_data = array(
                    'method'        => 'info',
                    'account_id'    => $account_id,
                    'secret'        => $secret
                );

                $account_info = $this->model_module_perpetto->perpettoCall($account_request_data);

                if(!isset($account_info->error) && !empty($account_info->data)) {
                    $data['account_info'] = $account_info;

                    $data['card_added'] = $account_info->data->store->has_card;
                    
                    if($account_info->data->store->trial_days_left > 0) {
                        $data['trial_days_left'] = $account_info->data->store->trial_days_left;
                    } else {
                        if(!$data['card_added']) {
                            $data['error_warning'] = $this->language->get('text_expired_trial');
                        }
                        $data['trial_days_left'] = 0;
                    }

                    $request_data = array(
                        'method'        => 'slots',
                        'account_id'    => $account_id,
                        'secret'        => $secret
                    );

                    $result = $this->model_module_perpetto->perpettoCall($request_data);

                    $grouped_slots = array();

                    if(!isset($result->error) && !empty($result->data)) {
                        $this->model_module_perpetto->deleteAllModuleLayouts();
                        $slots = $result;
                        foreach($slots->data->slots as $slot) {
                            $token_info_from_db = $this->model_module_perpetto->checkIfSlotExists($slot->token,$store['store_id']);
                            if(!empty($token_info_from_db['position'])) {
                                $slot->position = $token_info_from_db['position'];
                                $this->model_module_perpetto->setModuleToLayout($slot);
                            } else if($token_info_from_db) {
                                $slot->position = "content_bottom";
                                $this->model_module_perpetto->setModuleToLayout($slot);
                            } else {
                                $slot->position = 0;
                            }
                            $grouped_slots[$slot->page][] = $slot;
                        }
                        $data['slots'] = $grouped_slots;
                    } else {
                        if(!empty($result->error)) {
                            $data['error_warning'] = $result->error;
                        } else {
                            $data['error_warning'] = $this->language->get('text_invalid_account');
                        }
                    }


                } else {
                    if(!empty($account_info->error) && $this->session->data['error_warning'] !== $account_info->error) {
                        $data['error_warning'] = $account_info->error;
                    } else {
                        $data['error_warning'] = $this->language->get('text_invalid_account');
                    }

                    $data['trial_days_left'] = 0;
                }
                
            } else {
                $data['error_warning'] = $this->language->get('text_invalid_account');
            }      

            $enabled_ssl = $this->config->get('config_secure');
            
            if(!empty($enabled_ssl)) {
                $data['live_preview_link'] = HTTPS_CATALOG."index.php?route=product/product&product_id=".$this->model_module_perpetto->getRandomProductId()."&ptto_env=PREVIEW";
            } else {
                $data['live_preview_link'] = HTTP_CATALOG."index.php?route=product/product&product_id=".$this->model_module_perpetto->getRandomProductId()."&ptto_env=PREVIEW";
            }

            $this->response->setOutput($this->load->view('module/'.$this->moduleNameSmall.'.tpl', $data));
        }
    }

    public function perpettoFirstLogin() {
        $this->load->model('setting/setting');
        $this->load->model('module/'.$this->moduleNameSmall);
        $this->load->language('module/'.$this->moduleNameSmall);
        $account_id = $this->request->post[$this->moduleName]['account_id'];
        $secret = $this->request->post[$this->moduleName]['secret'];

        if(empty($account_id) || empty($secret)) {
            $this->session->data['error_warning'] = "All fields must be filled!";
        } else {

            $request_data = array(
                'method'        => 'info',
                'account_id'    => $account_id,
                'secret'        => $secret
            );

            $result = $this->model_module_perpetto->perpettoCall($request_data);
            
            if(!isset($result->error) && !empty($result->data)) {
                $this->request->post[$this->moduleNameSmall]['connected'] = 'yes';
                $this->model_setting_setting->editSetting($this->moduleNameSmall, $this->request->post, $this->request->post['store_id']);
                $this->session->data['success'] = $this->language->get('text_connected_account');
            } else {
                if(!empty($result->error)) {
                    $this->session->data['error_warning'] = $result->error;
                } else {
                    $this->session->data['error_warning'] = $this->language->get('text_invalid_account');
                }

            }
        }

        $this->response->redirect($this->url->link('module/'.$this->moduleNameSmall, 'store_id='.$this->request->post['store_id'] . '&token=' . $this->session->data['token'], 'SSL'));
                

    }

    public function editSlotPosition() {
        $this->load->model('module/'.$this->moduleNameSmall);
        $token = $this->request->post['slot_token'];
        $position = $this->request->post['slot_position'];
        $store_id = $this->request->post['store_id'];

        $token_info_from_db = $this->model_module_perpetto->checkIfSlotExists($token,$store_id);
        if(!empty($token_info_from_db)) {
            if($position != 'not_set') {
                $query = $this->db->query("UPDATE `" . DB_PREFIX . "perpetto_slots` SET position = '".$this->db->escape($position)."' WHERE token = '".$this->db->escape($token)."'");
            }
            else {
                $query = $this->db->query("UPDATE `" . DB_PREFIX . "perpetto_slots` SET position = 0 WHERE token = '".$this->db->escape($token)."'");
            }
        }

        $this->response->setOutput(json_encode('success')); 
    }
	
	protected function validateForm() {
		if (!$this->user->hasPermission('modify', 'module/'.$this->moduleNameSmall)) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
		return !$this->error;
	}

    private function getCatalogURL() {
        if (isset($_SERVER['HTTPS']) && (($_SERVER['HTTPS'] == 'on') || ($_SERVER['HTTPS'] == '1'))) {
            $storeURL = HTTPS_CATALOG;
        } else {
            $storeURL = HTTP_CATALOG;
        } 
        return $storeURL;
    }

    private function getServerURL() {
        if (isset($_SERVER['HTTPS']) && (($_SERVER['HTTPS'] == 'on') || ($_SERVER['HTTPS'] == '1'))) {
            $storeURL = HTTPS_SERVER;
        } else {
            $storeURL = HTTP_SERVER;
        } 
        return $storeURL;
    }

    private function getCurrentStore($store_id) {    
        if($store_id && $store_id != 0) {
            $store = $this->model_setting_store->getStore($store_id);
        } else {
            $store['store_id'] = 0;
            $store['name'] = $this->config->get('config_name');
            $store['url'] = $this->getCatalogURL(); 
        }
        return $store;
    }
    
    public function install() {
	    $this->load->model('module/'.$this->moduleNameSmall);
        $this->load->language('module/'.$this->moduleNameSmall);
	    $this->{$this->moduleModel}->install($this->moduleNameSmall);
        $this->session->data['success'] = $this->language->get('text_completed_installation');
        $this->response->redirect($this->url->link('module/'.$this->moduleNameSmall, 'store_id='.$this->config->get('config_store_id') . '&token=' . $this->session->data['token'], 'SSL'));

    }
    
    public function uninstall() {
    	$this->load->model('setting/setting');
		
		$this->load->model('setting/store');
		$this->model_setting_setting->deleteSetting($this->moduleData_module,0);
		$stores=$this->model_setting_store->getStores();
		foreach ($stores as $store) {
			$this->model_setting_setting->deleteSetting($this->moduleData_module, $store['store_id']);
		}
		
        $this->load->model('module/'.$this->moduleNameSmall);
        $this->{$this->moduleModel}->uninstall($this->moduleNameSmall);
    }
}

?>
