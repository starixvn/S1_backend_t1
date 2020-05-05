<?php
/**
 * Class MY_Loader
 *
 */

class MY_Loader extends CI_Loader {
    protected $_ci_widget_paths;
    protected $_ci_widgets;

    public function __construct(){
        parent::__construct();

        $this->_ci_widget_paths = array(APPPATH);

        $this->CI = &get_instance();
    }

    public function initialize(){
        $this->_ci_widgets = [];

        return parent::initialize();
    }

    public function widget($widget, $name = null, $options = []){
        if (is_array($widget)) {
            $instances = [];
            foreach ($widget as $babe) {
                $instances[] = $this->widget($babe);
            }
            return $instances;
        }

        if ($widget == '') {
            return;
        }

        $path = '';

        // Is the model in a sub-folder? If so, parse out the filename and path.
        if (($last_slash = strrpos($widget, '/')) !== FALSE) {
            // The path is in front of the last slash
            $path = substr($widget, 0, $last_slash + 1);

            // And the model name behind it
            $widget = substr($widget, $last_slash + 1);
        }

        if ($name == '') {
            $name = $widget;
        }

        $widget = strtolower($widget);

        if (!in_array($name, $this->_ci_widgets, TRUE)) {
            $file_path = null;
            foreach ($this->_ci_widget_paths as $mod_path) {
                $file_path = $mod_path . 'widgets/' . $path . $widget . '.php';
                if (!file_exists($file_path)) {
                    continue;
                }

                if (!class_exists('MY_Widget')) {
                    load_class('Widget', 'core');
                }

                require_once $file_path;

                $this->_ci_widgets[] = $name;

                break;
            }
        }

        $widget_class_name = 'Widget_' . ucfirst($widget);

        if(!class_exists($widget_class_name)){
            // couldn't find the widget
            show_error('Unable to locate the widget');
            return;
        }

        $instance = new $widget_class_name($options);
        return $instance;
    }

    public function view($view, $vars = [], $return = FALSE){
        $template = substr($view, 0, strpos(ltrim($view, '/'), '/'));
        $view = ($this->CI->template->hasExtend($template)) ? $this->CI->template->extend_view($view) : $view;

        return parent::view($view, $vars, $return);
    }

    public function view_exists($view){
        $template = substr($view, 0, strpos(ltrim($view, '/'), '/'));
        $view = ($this->CI->template->hasExtend($template)) ? $this->CI->template->extend_view($view) : $view;

        return file_exists(VIEWPATH . '/' . ltrim(preg_replace('/\\.[^.\\s]{3,4}$/', '', $view), '/') . '.php');
    }
}