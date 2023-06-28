<?php
/**
* 2007-2023 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2023 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_PS_VERSION_')) {
    exit;
}

class UriModuleTest extends Module
{
    protected $config_form = false;

    public function __construct()
    {
        $this->name = 'urimoduletest';
        $this->tab = 'administration';
        $this->version = '1.0.0';
        $this->author = 'martin';
        $this->need_instance = 1;
        $this->bootstrap = true;
        parent::__construct();
        $this->displayName = $this->l('Modulo de test');
        $this->description = $this->l('Descripción del Módulo de Test');
        $this->confirmUninstall = $this->l('Estás seguro de desinstalarlo?');
        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
    }

    public function install()
    {
        
        return parent::install() && $this->registerHook('displayAfterProductThumbs') && $this->installDB();
    }

    public function installDB(){
        Db::getInstance()->execute('CREATE TABLE IF NOT EXISTS '._DB_PREFIX_. $this->name . '_text (
            id int(11) NOT NULL AUTO_INCREMENT,
            texto VARCHAR(255),
            PRIMARY KEY (`id`)
          ) ENGINE='. _MYSQL_ENGINE_ .' DEFAULT CHARSET=utf8');
        return true;
    }

    public function uninstall()
    {
        return parent::uninstall() && $this->uninstallDB();
    }

    public function uninstallDB(){
        Db::getInstance()->execute('DROP TABLE IF EXISTS '._DB_PREFIX_. $this->name . '_text');
        return true;
    }
    

    public function getContent(){

        $base_url = AdminController::$currentIndex . '&configure=' . $this->name . '&token=' . Tools::getAdminTokenLite('AdminModules').'&mod_action=';
        $this->urls = [
            'add'=> $base_url.'add',
            'edit'=> $base_url.'edit&id=',
        ];

        //print_r($this->urls);
        $action = Tools::getValue('mod_action');
        return $this->postProcess() . $this->renderAdmin($action);
    }

    private function postProcess(){
        if(Tools::isSubmit('add')){
            
            $insert = [
                'texto' => pSQL(Tools::getValue('text')) //text de getForm() en fields_value, pSQL evita inyeccion de codigo
            ];
            Db::getInstance()->insert($this->name . '_text', $insert);

            return $this->displayConfirmation($this->l('Guardado correctamente'));
        }
    }

    private function getForm($action){

        $helper = new HelperForm();
        $helper->module = $this;
        $helper->name_controller = $this->name;
        $helper->identifier = $this->identifier;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->languages = $this->context->controller->getLanguages();
        $helper->currentIndex = AdminController::$currentIndex . '&configure=' . $this->name;
        $helper->default_form_language = $this->context->controller->default_form_language;
        $helper->allow_employee_form_lang = $this->context->controller->allow_employee_form_lang;
        $helper->title = $this->displayName;
        $helper->submit_action = $action;

        if($action == 'add'){
            $helper->fields_value = [
                'text' => '',
            ];
    
            $form[] = array(
                'form' => array(
                    'legend' => array(
                        'title' => $this->l('Añadir texto nuevo')
                    ),
                'input' => array(
                    array(
                        'type' => 'text',
                        'name' => 'text',
                        'label'=> $this->l('Texto'),
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Añadir'),
                ),
                )
            );
        }

        if($action == 'edit'){
            $helper->fields_value = [
                'text' => $this->getText(1),
            ];
    
            $form[] = array(
                'form' => array(
                    'legend' => array(
                        'title' => $this->l('Cambia el nombre de la variable')
                    ),
                'input' => array(
                    array(
                        'type' => 'text',
                        'name' => 'text',
                        'label'=> $this->l('Cambia el texto'),
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Editar'),
                ),
                )
            );
        }

        
        return $helper->generateForm($form);
    }

    private function renderAdmin($action){
        if ($action == '' || $action == 'home'){
            $this->context->smarty->assign([
                'textos' => $this->getTextos(),
                'urls'=> $this->urls,
            ]);
            return $this->context->smarty->fetch($this->local_path. 'views/templates/admin/admin.tpl');
        }
        return $this->getForm($action);
    }

    private function getTextos(){
        return Db::getInstance()->executes('SELECT * FROM ' ._DB_PREFIX_.$this->name.'_text');
    }

    private function getText($id){
        return Db::getInstance()->getValue('SELECT texto FROM ' . _DB_PREFIX_ . $this->name . '_text WHERE id = '. (int)$id);
    }

    public function HookDisplayAfterProductThumbs(){

        $texto = Configuration::get('URI_MODULE_TEST_TEXTO');
        $this->context->smarty->assign([
            'texto' => $texto,
        ]);

        //return $this->context->controller->fetch($this->local_path.'views/templates/hook/product.tpl'); HACE LO MISMO QUE LA LINEA DE ABAJO
        return $this->display(__FILE__, 'product.tpl'); //VAMOS A MOSTRAR EL ARCHIVO product.tpl
    }
}   