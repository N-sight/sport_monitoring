<?php

// применяется для рендеринга других страниц отличных от стандартного CRUD-a

class Controller
{
    protected $layout = 'layout.php';

    function __call($name, $params)
    {
        e404("Нет метода $name");
    }

    public function render($view_name,$data = array(), $with_layout = true)
    {
        ob_start();
        $lib = "templates/".$view_name.".php";

        foreach ($data as $key => $value)
        {
            $$key = $value;
        }

        if( file_exists($lib))
        {
            require_once ("$lib");
        }
        else
        {
            die('404 - нет такой вьюшки :'.$lib);
        }
        $content = ob_get_contents();
        ob_end_clean();

        if ( ($with_layout)){
            ob_start();
            require_once ('templates/layout/'.$this->layout);
            $content = ob_get_contents();
            ob_end_clean();
        }

        return $content;
    }
}